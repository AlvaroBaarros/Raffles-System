<?php

namespace App\Models;

use App\Payment_pix;
use App\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Participante extends Model
{
    protected $table = 'participant';

    protected $fillable = [
        'numbers',
        'reservados',
        'pagos',
        'conferido'
    ];

    public function reservados()
    {
        if($this->rifa()->modo_de_jogo == 'numeros'){
            $reservados = [];
            if($this->reservados > 0){
                $reservados = $this->numbers();
            }

            return $reservados;
        }
        else{
            return $this->hasMany(Raffle::class, 'participant_id', 'id')->where('status', '=', 'Reservado')->get();
        }
    }

    public function qtdReservados()
    {
        if($this->rifa()->modo_de_jogo == 'numeros'){
            return $this->reservados;
        }
        else{
            return $this->reservados()->count();
        }
    }

    public function numbers()
    {
        $numbers = json_decode($this->numbers);
        if (!is_array($numbers)) {
            $numbers = [];
        }
        sort($numbers);
        return $numbers;
    }

    /**
     * Cancela uma reserva NAO paga: devolve os numeros para a rifa e apaga a reserva.
     * Usa trava na rifa para nao conflitar com compras acontecendo ao mesmo tempo.
     *
     * @return bool true se a reserva foi liberada
     */
    public function liberarReserva($incluirPagas = false)
    {
        $participanteId = $this->id;

        return DB::transaction(function () use ($participanteId, $incluirPagas) {
            $participante = Participante::where('id', '=', $participanteId)->lockForUpdate()->first();
            if (!$participante) {
                return false;
            }

            // Nunca libera numeros de uma compra ja paga (exceto quando o admin pede explicitamente)
            if (!$incluirPagas) {
                if ($participante->reservados == 0 && $participante->pagos > 0) {
                    return false;
                }
                $pix = DB::table('payment_pix')->where('participant_id', '=', $participanteId)->first();
                if ($pix && ($pix->status == 'Aprovado' || $pix->status == 'Concluída')) {
                    return false;
                }
            }

            $rifa = \App\Models\Product::lockForNumbers($participante->product_id);

            if ($rifa && $rifa->modo_de_jogo == 'numeros') {
                $rifaNumbers = $rifa->numbers();
                $jaNaLista = array_flip($rifaNumbers);

                foreach ($participante->numbers() as $number) {
                    if (is_object($number) && isset($number->number)) {
                        $number = $number->number; // formato antigo
                    }
                    if (!is_scalar($number) || (string) $number === '') {
                        continue;
                    }
                    $number = (string) $number;
                    if (!isset($jaNaLista[$number])) {
                        $rifaNumbers[] = $number;
                        $jaNaLista[$number] = true;
                    }
                }

                sort($rifaNumbers);
                $rifa->saveNumbers($rifaNumbers);
            }

            Raffle::where('participant_id', '=', $participanteId)->update([
                'status' => 'Disponível',
                'participant_id' => null
            ]);

            DB::table('payment_pix')->where('participant_id', '=', $participanteId)->delete();
            $participante->delete();

            return true;
        });
    }

    /**
     * Data/hora em que a reserva expira (null se a rifa nao expira reservas).
     */
    public function reservaExpirada()
    {
        $rifa = $this->rifa();
        if (!$rifa || $rifa->expiracao <= 0) {
            return false;
        }

        $dataDeExpiracao = strtotime("+" . $rifa->expiracao . " minutes", strtotime($this->created_at));

        return $dataDeExpiracao <= time();
    }

    public function numbersResumo()
    {
        $numbers = $this->numbers();

        $numbersRelatorio = '';
        foreach ($numbers as $key => $number) {
            if($key > 0){
                $numbersRelatorio .= ', ';
            }
            $numbersRelatorio .= $number;
        }

        return $numbersRelatorio;
    }

    public function status()
    {
        if($this->reservados > 0){
            return 'Números Reservados';
        }
        else if($this->pagos > 0){
            return 'Compra Aprovada';
        }
    }

    public function statusBadge()
    {
        if($this->reservados > 0){
            return '<span class="badge bg-warning">Pendente</span>';
        }
        else if($this->pagos > 0){
            return '<span class="badge bg-success">Pago</span>';
        }
    }

    public function situacao()
    {
        if($this->qtdReservados() > 0){
            return 'reservado';
        }
        else if($this->qtdPagos() > 0){
            return 'pago';
        }
    }

    public function qtdPagos()
    {
        if($this->rifa()->modo_de_jogo == 'numeros'){
            $oldNumbers = $this->hasMany(Raffle::class, 'participant_id', 'id')->where('status', '=', 'Pago')->get();
            if($oldNumbers->count() > 0){
                return $oldNumbers->count();
            }
            else{
                return $this->pagos;
            }
            
        }
        else{
            return $this->pagos()->count();
        }
    }

    public function pagos()
    {
        if($this->rifa()->modo_de_jogo == 'numeros'){
            $oldNumbers = $this->hasMany(Raffle::class, 'participant_id', 'id')->where('status', '=', 'Pago')->get();

            if($oldNumbers->count() > 0 && count($this->numbers()) == 0){ // Estrutura antiga (antes do 1 milhao)
                $pagos = $this->hasMany(Raffle::class, 'participant_id', 'id')->where('status', '=', 'Pago')->get();
            }
            else{
                $pagos = [];
                if($this->pagos > 0){
                    $pagos = $this->numbers();
                }
            }

            return $pagos;
        }
        else{
            return $this->hasMany(Raffle::class, 'participant_id', 'id')->where('status', '=', 'Pago')->get();
        }
    }

    public function cotas()
    {
        return $this->hasMany(Raffle::class, 'participant_id', 'id')->get();
    }

    public function rifa()
    {
        return $this->hasOne(Product::class, 'id', 'product_id')->first();
    }

    public function order()
    {
        return $this->hasOne(Order::class, 'participant_id', 'id')->first();
    }

    public function totalReservas()
    {
        return $this->hasMany(Raffle::class, 'participant_id', 'id')->get();
    }

    public function sampleName()
    {
        $explode = explode(" ", $this->name);

        $name = $explode[0];

        if(array_key_exists(1, $explode)){
            $name .= ' ' . $explode[1];
        }

        return $name;
    }

    public function linkWpp()
    {
        $tel = "55" . str_replace(["(", ")", "-", " "], "", $this->telephone);
        $link = 'https://api.whatsapp.com/send?phone=' . $tel;

        return $link;
    }

    public function reciboWpp()
    {
        $link = $this->linkWpp();

        $social = DB::table('consulting_environments')->where('id', '=', 1)->first();

        $msg = '------- '.$social->name.' -------';
        $msg .= '%0A%0A';
        $msg .= 'Olá *'.$this->name.'*';
        $msg .= '%0A%0A';
        $msg .= 'Sua *compra '.$this->id.'* no valor de *R$ '.number_format($this->valor, 2, ",", ".").'* do sorteio *'.$this->rifa()->name.'* foi confirmada!';
        $msg .= '%0A%0A';
        $msg .= 'Você está concorrendo com os números:';
        foreach ($this->pagos() as $x) {
            $msg .= '%0A';
            $msg .= '*'.$x->number.'*';
        }

        $msg .= '%0A%0A';
        $msg .= 'Obrigado 🙏🏻 e boa sorte 🍀!';

        $response = $link . '&text=' . $msg;

        return $response;
    }

    public function linkPagamentoWpp()
    {
        $link = $this->linkWpp();

        $social = DB::table('consulting_environments')->where('id', '=', 1)->first();

        $msg = '------- '.$social->name.' -------';
        $msg .= '%0A%0A';
        $msg .= 'Olá *'.$this->name.'*';
        $msg .= '%0A%0A';
        $msg .= 'Sua *compra '.$this->id.'* no valor de *R$ '.number_format($this->valor, 2, ",", ".").'* do sorteio *'.$this->rifa()->name.'* esta com o pagamento pendente!';
        $msg .= '%0A%0A';
        $msg .= 'Segue link para efetuar o pagamento: ';
        $msg .= '%0A';
        $msg .= \Illuminate\Support\Facades\URL::signedRoute('pagarReserva', $this->id);
        $msg .= '%0A%0A';
        $msg .= 'Obrigado 🙏🏻 e boa sorte 🍀!';

        $response = $link . '&text=' . $msg;

        return $response;
    }

    public function expiracao()
    {
        $rifa = $this->rifa();
        $expiracao = $rifa->expiracao;
        $criacao = $this->created_at;

        $expiraEm = date('Y-m-d H:i', strtotime("+".$expiracao." minutes", strtotime($criacao)));

        return $expiraEm;
    }

    public function payment()
    {
        return $this->hasOne(Payment_pix::class, 'participant_id', 'id')->first();
    }
}
