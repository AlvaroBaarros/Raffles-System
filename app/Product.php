<?php

namespace App;

use App\Models\DescricaoProduto;
use App\Models\Participante;
use App\Models\Raffle;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class Product extends Model
{
    protected $fillable  = ['id', 'name', 'slug', 'price', 'status', 'type_raffles', 'winner', 'draw_prediction', 'draw_date', 'visible', 'favoritar', 'minimo', 'maximo', 'expiracao', 'qtd_ranking', 'parcial', 'gateway', 'subname', 'qtd_zeros', 'modo_de_jogo', 'numbers', 'ganho_afiliado'];
    public function produt_image()
    {
        return $this->hasMany('App\CreateProductimage', 'product_id', 'id');
    }

    public function imagem()
    {
        return $this->hasOne(CreateProductimage::class, 'product_id', 'id')->first();
    }

    public function descricao()
    {
        $desc = $this->hasOne(DescricaoProduto::class, 'product_id', 'id')->first();
        if($desc){
            return $desc->description;
        }
        else{
            return '';
        }
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

    public function numbers()
    {
        if ($this->modo_de_jogo == 'numeros') {
            // Mesma regra do App\Models\Product: ignora itens vazios
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

    public function confirmPayment($participanteId)
    {
        // Uma unica implementacao (com trava e sem confirmar duas vezes): App\Models\Product
        $rifa = \App\Models\Product::find($this->id);

        return $rifa ? $rifa->confirmPayment($participanteId) : false;
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

                (new Helpers\Helper())->sendZap($owerPhone, $mensagem);
            }

            // ============== Mensagem para o cliente
            // ============================================== //
            if($msgCliente->msg != null && $msgCliente->msg != ''){
                $mensagem = $msgCliente->getMessage($participante);
                $customerPhone = '55' . str_replace(["(", ")", "-", " "], "", $participante->telephone);

                try {
                    $data = [
                        'receiver'  => $customerPhone,
                        'msgtext'   => $mensagem,
                        'token'     => $config->token_api_wpp,
                    ];
                    
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
                    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
                    curl_setopt($ch, CURLOPT_URL, $apiURL);
                    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
                    $response = curl_exec($ch);
                    curl_close($ch);
                } catch (\Throwable $th) {
                    
                }
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
}   
