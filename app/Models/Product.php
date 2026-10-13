<?php

namespace App\Models;

use App\AutoMessage;
use App\CompraAutomatica;
use App\CreateProductimage;
use App\Environment;
use App\Helpers\Helper;
use App\Promocao;
use App\RifaAfiliado;
use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class Product extends Model
{
    protected $table = 'products';
    protected $fillable = [
        'numbers',
        'ganho_afiliado'
    ];

    public function numbers()
    {
        if ($this->modo_de_jogo == 'numeros') {
            // Lista de numeros disponiveis guardada como texto "0001,0002,...".
            // Ignora itens vazios (antes, uma rifa esgotada devolvia [""] e o sistema
            // achava que ainda havia 1 numero disponivel).
            $numbersRifa = [];
            if ($this->numbers !== null && $this->numbers !== '') {
                foreach (explode(",", $this->numbers) as $numero) {
                    if ($numero !== '') {
                        $numbersRifa[] = $numero;
                    }
                }
            }

            return $numbersRifa;
        } else {
            return $this->hasMany(Raffle::class, 'product_id', 'id')->get();
        }
    }

    /**
     * Busca a rifa com TRAVA (SELECT ... FOR UPDATE).
     * Use dentro de uma transacao sempre que for ler e alterar a lista de numeros:
     * outras compras/liberacoes da mesma rifa esperam ate o commit.
     */
    public static function lockForNumbers($id)
    {
        return static::where('id', '=', $id)->lockForUpdate()->first();
    }

    public function participantesArray($limite)
    {
        $response = [];
        $participantes = Participante::where('product_id', '=', $this->id)->where('id', '!=', 5)->limit($limite)->get();
        foreach ($participantes as $participante) {
            array_push($response, $participante->id);
        }

        return implode(",", $response);
    }

    public function getAllNumbers()
    {
        $allNumbers = [];
        $numbersDisponiveis = $this->numbers();
        foreach ($numbersDisponiveis as $number) {
            array_push($allNumbers, [
                'key' => $number,
                'number' => $number,
                'status' => 'Disponivel'
            ]);
        }

        return $allNumbers;
    }

    public function saveNumbers($numbersArray)
    {
        $stringNumbers = implode(",", $numbersArray);
                
        // if($stringNumbers == ""){
        //     throw new \ErrorException('Erro encontrado, entre em contato com o administrador do sistema');
        // }
        $this->update([
            'numbers' => $stringNumbers
        ]);
        // $arquivo = 'numbers/' . $this->id . '.json';
        // $req = fopen($arquivo, 'w') or die('Cant open the file');
        // fwrite($req, json_encode($numbersArray));
        // fclose($req);

        // $arquivoDebug = 'numbers/' . $this->id . '-debug5.json';
        // $req = fopen($arquivoDebug, 'w') or die('Cant open the file');
        // fwrite($req, json_encode($numbersArray));
        // fclose($req);
    }

    public function qtdNumerosDisponiveis()
    {
        if($this->modo_de_jogo == 'numeros'){
            return $this->qtd - $this->qtdNumerosReservados() - $this->qtdNumerosPagos();
        }
        else{
            return $this->hasMany(Raffle::class, 'product_id', 'id')->where('status', '=', 'Disponível')->get()->count();
        }
    }

    public function randomNumbers($qtd)
    {
        $randomNumbers = DB::table('raffles')
            ->select('number')
            ->where('raffles.product_id', '=', $this->id)
            ->where('raffles.status', '=', 'Disponível')
            ->inRandomOrder()
            ->limit($qtd)
            ->get();

        return $randomNumbers;
    }

    public function numerosDisponiveis()
    {
        $response = [];
        $numeros = $this->hasMany(Raffle::class, 'product_id', 'id')->where('status', '=', 'Disponível')->get();

        foreach ($numeros as $numero) {
            array_push($response, $numero->number);
        }

        return $response;
    }

    public function qtdNumerosReservados()
    {
        if($this->modo_de_jogo == 'numeros'){
            return $this->participantes()->sum('reservados');
        }
        else{
            return $this->hasMany(Raffle::class, 'product_id', 'id')->where('status', '=', 'Reservado')->get()->count();
        }
    }

    public function numerosReservados()
    {
        return $this->hasMany(Raffle::class, 'product_id', 'id')->where('status', '=', 'Reservado')->get();
    }

    public function qtdNumerosPagos()
    {
        if($this->modo_de_jogo == 'numeros'){
            return $this->participantes()->sum('pagos');
        }
        else{
            return $this->hasMany(Raffle::class, 'product_id', 'id')->where('status', '=', 'Pago')->get()->count();
        }
    }

    public function porcentagem()
    {
        $numerosUtilizados = $this->qtdNumerosReservados() + $this->qtdNumerosPagos();
        $totalDaRifa = $this->qtd;
        
        $percentual = ($numerosUtilizados * 100) / $totalDaRifa;

        return round($percentual, 2);
    }

    public function participantes()
    {
        return $this->hasMany(Participante::class, 'product_id', 'id')->orderBy('id', 'desc')->get();
    }

    public function participantesReservados()
    {
        $numeros = Raffle::select('participant_id')
            ->where('product_id', '=', $this->id)
            ->where('status', '=', 'Reservado')
            ->groupBy('participant_id')
            ->get();

        return $numeros;
    }

    public function promocoes()
    {
        return $this->hasMany(Promocao::class, 'product_id', 'id')->orderBy('ordem', 'asc')->get();
    }

    public function promosAtivas()
    {
        $promocoes = $this->promocoes()->where('qtdNumeros', '>', 0);
        $result = [];
        foreach ($promocoes as $promocao) {
            array_push($result, [
                'numeros' => $promocao->qtdNumeros,
                'desconto' => $promocao->desconto
            ]);
        }

        return json_encode($result);
    }

    public function imagem()
    {
        return $this->hasOne(CreateProductimage::class, 'product_id', 'id')->first();
    }

    public function fotos()
    {
        return $this->hasMany(CreateProductimage::class, 'product_id', 'id')->limit(3)->get();
    }

    public function getParticipanteName($id)
    {
        $participante = Participante::find($id);

        return $participante->name;
    }

    public function getParticipantePhone($id)
    {
        $participante = Participante::find($id);

        return '(**) ***** - ' . substr($participante->telephone, -4);
    }

    public function numbersRelatorio()
    {
        if($this->modo_de_jogo == 'numeros'){
            $numbersRifa = $this->numbers();
            $numbersRelatorio = array_filter($numbersRifa, function($number){
                return $number['status'] != 'Disponivel';
            });
            return $numbersRelatorio;
        }
        else{
            return $this->hasMany(Raffle::class, 'product_id', 'id')->where('participant_id', '!=', null)->orderBy('number', 'asc')->get();
        }
        
    }

    public function medalhaRanking($posicao)
    {
        switch ($posicao) {
            case '0':
                return '🥇';
                break;
            case '1':
                return '🥈';
                break;
            case '2':
                return '🥉';
                break;
            default:
                return '🏅';
                break;
        }
    }

    public function ranking()
    {

        $ranking = DB::table('participant')
            ->select(DB::raw('SUM(participant.pagos) as totalReservas'), 'participant.telephone', 'participant.name')
            ->where('participant.product_id', '=', $this->id)
            ->where('participant.pagos', '>', 0)
            ->groupBy('participant.telephone')
            ->orderBy('totalReservas', 'desc')
            ->limit($this->qtd_ranking)
            ->get();
                                
        
        // $ranking = DB::table('raffles')
        //     ->select(DB::raw('COUNT(raffles.id) as totalReservas'), 'participant.telephone', 'participant.name')
        //     ->where('raffles.product_id', '=', $this->id)
        //     ->where('raffles.participant_id', '!=', null)
        //     ->where('raffles.status', '=', 'Pago')
        //     ->join('participant', 'participant.id', '=', 'raffles.participant_id')
        //     ->groupBy('participant.telephone')
        //     ->orderBy('totalReservas', 'desc')
        //     ->limit($this->qtd_ranking)
        //     ->get();

        return $ranking->toArray();
    }

    public function rankingAdmin()
    {

        $ranking = DB::table('participant')
            ->select(DB::raw('SUM(participant.pagos) as totalReservas'), DB::raw('SUM(participant.valor) as totalGasto'),'participant.telephone', 'participant.name')
            ->where('participant.product_id', '=', $this->id)
            ->where('participant.pagos', '>', 0)
            ->groupBy('participant.telephone')
            ->orderBy('totalReservas', 'desc')
            ->limit(8)
            ->get();

        // $ranking = DB::table('raffles')
        //     ->select(DB::raw('COUNT(raffles.id) as totalReservas'), 'participant.telephone', 'participant.name')
        //     ->where('raffles.product_id', '=', $this->id)
        //     ->where('raffles.participant_id', '!=', null)
        //     ->where('raffles.status', '=', 'Pago')
        //     ->join('participant', 'participant.id', '=', 'raffles.participant_id')
        //     ->groupBy('participant.telephone')
        //     ->orderBy('totalReservas', 'desc')
        //     ->limit(8)
        //     ->get();

        return $ranking->toArray();
    }

    public function descricao()
    {
        $desc = $this->hasOne(DescricaoProduto::class, 'product_id', 'id')->first();
        if ($desc) {
            return $desc->description;
        } else {
            return '';
        }
    }

    public function premios()
    {
        $premios = $this->hasMany(Premio::class, 'product_id', 'id')->orderBy('ordem', 'asc')->get();

        if ($premios->count() === 0) {
            for ($i = 1; $i <= 10; $i++) {
                Premio::create([
                    'product_id' => $this->id,
                    'ordem' => $i,
                    'descricao' => '',
                    'ganhador' => '',
                    'cota' => ''
                ]);
            }

            return $this->hasMany(Premio::class, 'product_id', 'id')->orderBy('ordem', 'asc')->get();
        } else {
            return $premios;
        }
    }

    public function status()
    {
        // teste 123
        switch ($this->status) {
            case 'Ativo':
                if ($this->porcentagem() >= 80) {
                    $status = '<span class="badge mt-2 blink" style="color: #fff; background-color: #f38e02">Corre que está acabando!</span>';
                } else {
                    $status = '<span class="badge mt-2 bg-success blink">Adquira já!</span>';
                }
                break;
            case 'Finalizado':
                if ($this->premios()->where('descricao', '!=', '')->where('ganhador', '!=', '')->count() == 0) {
                    $status = '<span class="badge bg-primary mt-2 blink" style="color: #fff">Esgotado! Aguarde sorteio!</span>';
                } else {
                    $status = '<span class="badge mt-2 bg-danger">Finalizado</span>';
                }

                break;
            default:
                $status = '';
                break;
        }

        return $status;
    }

    public function dataSorteio()
    {
        switch ($this->status) {
            case 'Ativo':
                if ($this->porcentagem() >= 80) {
                    $sorteioStatus = '<span class="badge mt-2 bg-warning" style="color: #000">' . date('d/m/Y', strtotime($this->draw_date)) . '</span>';
                } else {
                    $sorteioStatus = '<span class="badge mt-2 bg-success">' . date('d/m/Y', strtotime($this->draw_date)) . '</span>';
                }
                break;
            case 'Finalizado':
                if ($this->premios()->where('descricao', '!=', '')->where('ganhador', '!=', '')->count() == 0) {
                    $sorteioStatus = '<span class="badge mt-2" style="background: orange; color: #000">' . date('d/m/Y', strtotime($this->draw_date)) . '</span>';
                } else {
                    $sorteioStatus = '<span class="badge mt-2 bg-danger">' . date('d/m/Y', strtotime($this->draw_date)) . '</span>';
                }

                break;
            default:
                $sorteioStatus = '';
                break;
        }

        return $sorteioStatus;
    }

    public function getParticipanteById($id)
    {
        return Participante::find($id);
    }

    /**
     * Marca a compra como paga. Pode ser chamada varias vezes (webhook, agendador, tela do
     * PIX, admin): so a primeira chamada altera algo e envia a mensagem de confirmacao.
     */
    public function confirmPayment($participanteId)
    {
        $confirmouAgora = DB::transaction(function () use ($participanteId) {
            $participante = Participante::where('id', '=', $participanteId)->lockForUpdate()->first();
            if (!$participante) {
                return false;
            }

            $numbersParticipante = $participante->numbers();

            // Estrutura antiga / fazendinha (numeros na tabela raffles)
            Raffle::where('participant_id', '=', $participanteId)->update(['status' => 'Pago']);

            if ($participante->reservados == 0 && $participante->pagos > 0) {
                return false; // ja estava pago
            }

            $participante->update([
                'reservados' => 0,
                'pagos' => count($numbersParticipante)
            ]);

            return true;
        });

        if ($confirmouAgora) {
            try {
                $this->mensagemWPPRecebido($participanteId);
            } catch (\Throwable $th) {
                \Illuminate\Support\Facades\Log::error('Falha ao enviar WhatsApp de pagamento ' . $participanteId . ': ' . $th->getMessage());
            }
        }

        return $confirmouAgora;
    }

    public function mensagemWPPRecebido($participanteID)
    {
        $admin = User::find(1);
        $config = Environment::find(1);
        $participante = Participante::find($participanteID);
        $msgAdmin = AutoMessage::where('identificador', '=', 'recebido-admin')->first();
        $msgCliente = AutoMessage::where('identificador', '=', 'recebido-cliente')->first();
        $apiURL = env('URL_API_CRIAR_WHATS');

        if($config->token_api_wpp != null){

            // ============== Mensagem para o admin
            // ============================================== //
            if($msgAdmin->msg != null && $msgAdmin->msg != ''){
                $mensagem = $msgAdmin->getMessage($participante);
                $owerPhone = '55' . str_replace(["(", ")", "-", " "], "", $admin->telephone);
                (new Helper())->sendZap($owerPhone, $mensagem);
            }

            // ============== Mensagem para o cliente
            // ============================================== //
            if($msgCliente->msg != null && $msgCliente->msg != ''){
                $mensagem = $msgCliente->getMessage($participante);
                $customerPhone = '55' . str_replace(["(", ")", "-", " "], "", $participante->telephone);
                (new Helper())->sendZap($customerPhone, $mensagem);
            }
        }
    }

    public function afiliados()
    {
        return $this->hasMany(RifaAfiliado::class, 'product_id', 'id')->get();
    }

    public function checkAfiliado()
    {
        $user = Auth::user();

        $afiliado = $this->afiliados()->where('afiliado_id', '=', $user->id);

        if($afiliado->count() > 0){
            return true;
        }
        else{
            return false;
        }
    }

    public function getAfiliadoToken()
    {
        $afiliado = RifaAfiliado::where('product_id', '=', $this->id)->where('afiliado_id', '=', Auth::user()->id)->first();

        if($afiliado){
            return $afiliado->token;
        }
        else{
            return '';
        }
    }

    public function comprasAuto()
    {
        $compras = $this->hasMany(CompraAutomatica::class, 'product_id', 'id')->get();
        if($compras->count() == 0){
            $this->defaultComprasAuto();
            $compras = $this->hasMany(CompraAutomatica::class, 'product_id', 'id')->get();
        }
        
        return $compras;
    }

    public function defaultComprasAuto()
    {
        CompraAutomatica::create([
            'product_id' => $this->id,
            'qtd' => 5,
            'popular' => false
        ]);

        CompraAutomatica::create([
            'product_id' => $this->id,
            'qtd' => 10,
            'popular' => false
        ]);

        CompraAutomatica::create([
            'product_id' => $this->id,
            'qtd' => 30,
            'popular' => false
        ]);

        CompraAutomatica::create([
            'product_id' => $this->id,
            'qtd' => 50,
            'popular' => true
        ]);

        CompraAutomatica::create([
            'product_id' => $this->id,
            'qtd' => 0,
            'popular' => false
        ]);

        CompraAutomatica::create([
            'product_id' => $this->id,
            'qtd' => 0,
            'popular' => false
        ]);
    }

    public function getCompraMaisPopular()
    {
        $compras = $this->comprasAuto();
        if($compras->where('popular', '=', true)->count() > 0){
            return $compras->where('popular', '=', true)->first()->id;
        }
        else{
            return 0;
        }
        
    }

}
