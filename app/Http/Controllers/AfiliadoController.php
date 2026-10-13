<?php

namespace App\Http\Controllers;

use App\GanhosAfiliado;
use App\Models\Product;
use App\Product as AppProduct;
use App\RifaAfiliado;
use App\SolicitacaoAfiliado;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AfiliadoController extends Controller
{
    public function index()
    {
        if (Auth::user()) {
            if (!Auth::user()->afiliado) {
                Auth::logout();
                return redirect()->route('afiliado.home');
            }


            $data = [
                'rifas' => Product::where('ganho_afiliado', '>', 0)->get()
            ];

            return view('afiliados.home', $data);
        } else {
            return view('afiliados.login');
        }
    }

    public function rifasAtivas()
    {
        $user = Auth::user();
        if (!$user || !$user->afiliado) {
            return redirect()->route('afiliado.home');
        }

        $rifas = Product::where('ganho_afiliado', '>', 0)->where('status', '=', 'Ativo')->get();
        

        foreach ($rifas as $rifa) {
            $afiliado = RifaAfiliado::where('product_id', '=', $rifa->id)->where('afiliado_id', '=', $user->id)->get();
            
            if($afiliado->count() > 0){
                $rifa->checkAfiliado = true;
                $af = RifaAfiliado::where('product_id', '=', $rifa->id)->where('afiliado_id', '=', $user->id)->first();
                $rifa->getAfiliadoToken = $af->token;
        
            }
            else{
                $rifa->checkAfiliado = false;
                $rifa->getAfiliadoToken = '';
            }
        }


        $data = [
            'rifas' => $rifas
        ];

        return view('afiliados.rifasAtivas', $data);
    }

    public function login(Request $request)
    {
        $credentials = [
            'email' => $request->email,
            'password' => $request->password,
            'afiliado' => true
        ];

        if (Auth::attempt($credentials)) {

            return redirect()->route('afiliado.home');
        }

        return back()->withInput()->withErrors('Usuário e/ou Senha incorretos');
    }

    public function cadastro()
    {
        return view('afiliados.cadastro');
    }

    public function novo(Request $request)
    {
        $request->validate([
            'nome' => 'required|max:255',
            'email' => 'required|email|max:255',
            'senha' => 'required|min:6',
        ]);

        if ($request->senha != $request->conf_senha) {
            return back()->withInput()->withErrors('Senhas não conferem!');
        }

        if (User::where('email', '=', $request->email)->get()->count() > 0) {
            return back()->withInput()->withErrors('Email já possui cadastro!');
        }

        $user = User::create([
            'name' => $request->nome,
            'email' => $request->email,
            'telephone' => '',
            'status' => '1',
            'password' => bcrypt($request->senha),
            'cpf' => $request->cpf,
            'pix' => $request->pix,
            'afiliado' => true
        ]);

        Auth::login($user);

        return redirect()->route('afiliado.home');
    }

    public function home()
    {
        return view('afiliados.home');
    }

    public function logout()
    {
        Auth::logout();

        return redirect()->route('afiliado.home');
    }

    public function pagamentos()
    {
        $ganhos = GanhosAfiliado::select('ganhos_afiliados.*')
            ->join('participant', 'participant.id', '=', 'ganhos_afiliados.participante_id')
            ->where('afiliado_id', '=', Auth::user()->id)
            ->where('participant.pagos', '>', 0)
            ->get();

        $data = [
            'ganhos' => $ganhos,
            'disponivel' => $ganhos->where('solicitacao_id', '=', null)->sum('valor'),
            'solicitado' => $ganhos->where('solicitacao_id', '!=', null)->where('pago', '=', false)->sum('valor'),
            'recebido' => $ganhos->where('solicitacao_id', '!=', null)->where('pago', '=', true)->sum('valor'),
        ];


        return view('afiliados.pagamentos', $data);
    }

    public function afiliar($idRifa)
    {
        // So permite se afiliar a rifas ativas que aceitam afiliados
        $rifa = Product::where('id', '=', $idRifa)
            ->where('ganho_afiliado', '>', 0)
            ->where('status', '=', 'Ativo')
            ->first();

        if (!$rifa) {
            return redirect()->route('afiliado.rifas')->withErrors('Esta rifa não aceita afiliados.');
        }

        $jaAfiliado = RifaAfiliado::where('product_id', '=', $rifa->id)
            ->where('afiliado_id', '=', Auth::user()->id)
            ->exists();

        if ($jaAfiliado) {
            return redirect()->route('afiliado.rifas')->with(['message' => 'Você já é afiliado desta rifa!']);
        }

        RifaAfiliado::create([
            'product_id' => $rifa->id,
            'afiliado_id' => Auth::user()->id,
            'token' => bin2hex(random_bytes(10))
        ]);

        return redirect()->route('afiliado.rifas')->with(['message' => 'Afiliado com sucesso!']);
    }

    public function solicitarSaque()
    {
        try {
            $afiliadoId = Auth::user()->id;

            // Somente comissoes de compras PAGAS (antes entravam tambem reservas nao pagas)
            $idsPagos = GanhosAfiliado::select('ganhos_afiliados.id')
                ->join('participant', 'participant.id', '=', 'ganhos_afiliados.participante_id')
                ->where('ganhos_afiliados.afiliado_id', '=', $afiliadoId)
                ->whereNull('ganhos_afiliados.solicitacao_id')
                ->where('participant.pagos', '>', 0)
                ->pluck('ganhos_afiliados.id');

            $ganhosPendentes = GanhosAfiliado::whereIn('id', $idsPagos)->sum('valor');
            if ($ganhosPendentes == 0) {
                return back()->withErrors('Você não tem nenhum valor disponível para saque!');
            }

            $solicitacao = SolicitacaoAfiliado::create([
                'afiliado_id' => $afiliadoId
            ]);

            GanhosAfiliado::whereIn('id', $idsPagos)
            ->whereNull('solicitacao_id')
            ->update(['solicitacao_id' => $solicitacao->id]);

            return back()->with(['message' => 'Solicitação de saque realizada com sucesso!']);
        } catch (\Throwable $th) {
            \Illuminate\Support\Facades\Log::error('Erro ao solicitar saque: ' . $th->getMessage());
            return back()->withErrors('Erro interno no sistema!');
        }
    }
}
