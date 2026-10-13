<?php

namespace App\Http\Controllers;

use App\Customer;
use App\Models\Participante;
use App\Models\Premio;
use App\Models\Product;
use App\Models\Raffle;
use App\Participant;
use App\Payment_pix;
use App\User;
use Carbon\Carbon;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Cache;
use App\Services\Pagamentos;
use QRcode;

class CheckoutController extends Controller
{
    public function index(Request $request)
    {
        //dd("AQUI");
        $telephoneConsulting = DB::table('products')
            ->select('users.telephone')
            ->join('users', 'users.id', '=', 'products.user_id')
            ->where('products.id', '=', $request->productID)
            ->first();

        return view('checkout', [
            'participant' => $request->participant,
            'cpf' => $request->cpf,
            'telephone' => $request->telephone,
            'numbers' => $request->numbers,
            'price' => $request->price,
            'product' => $request->product,
            'productID' => $request->productID,
            'drawdate' => $request->drawdate,
            'image' => $request->image,
            'PIX' => $request->PIX,
            'codePIX' => $request->codePIX,
            'imagePIX' => $request->imagePIX,
            'telephoneConsulting' => $telephoneConsulting,
            'countRaffles' => $request->countRaffles,
            'priceUnic' => $request->priceUnic,
        ]);
    }

    public function checkoutManualy(Request $request)
    {
        // SEGURANCA: esta rota so abre com link assinado (middleware 'signed') e os dados
        // da compra (valor, PIX, numeros) sao lidos do pedido salvo no banco, nao da URL.
        $participante = Participante::find($request->participant_id);

        //Validando se existe essa reserva
        if (!$participante) {
            return redirect()->route('inicio')->withErrors('Reserva inválida');
        }

        $order = $participante->order();
        $dados = $order ? (json_decode($order->dados, true) ?: []) : [];
        $dado = function ($chave) use ($dados) {
            return isset($dados[$chave]) ? $dados[$chave] : null;
        };

        $rifa = $participante->rifa();
        if (!$rifa) {
            return redirect()->route('inicio')->withErrors('Reserva inválida');
        }

        $telephoneConsulting = DB::table('products')
            ->select('users.telephone')
            ->join('users', 'users.id', '=', 'products.user_id')
            ->where('products.id', '=', $rifa->id)
            ->first();

        $numbers = DB::table('raffles')->select('number')->where('participant_id', '=', $participante->id)->get();

        $criacao = date('Y-m-d H:i:s', strtotime($participante->created_at));
        $minutosExpiracao = $rifa->expiracao;
        $dataDeExpiracao = date('Y-m-d H:i:s', strtotime("+" . $minutosExpiracao . " minutes", strtotime($criacao)));

        $entrada = DateTime::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s'));
        $saida = DateTime::createFromFormat('Y-m-d H:i:s', $dataDeExpiracao);

        $minutosRestantes = ceil(($saida->getTimestamp() - $entrada->getTimestamp()) / 60);

        $config = DB::table('consulting_environments')->where('id', '=', 1)->first();

        $products = Product::where('visible', '=', 1)->orderBy('id', 'desc')->get();
        $ganhadores = Premio::where('descricao', '!=', null)->where('ganhador', '!=', '')->get();

        $rifaDestaque = Product::where('status', '=', 'Ativo')->where('visible', '=', 1)->orderBy('id', 'desc')->first();

        $userData = [
            'rifa' => $rifa,
            'participante' => $participante,
            'participant' => $participante->name,
            'cpf' => $dado('cpf'),
            'email' => $dado('email'),
            'telephone' => $participante->telephone,
            'numbers' => $numbers,
            'price' => $dado('price'),
            'product' => $rifa->name,
            'productID' => $rifa->id,
            'drawdate' => $rifa->draw_date,
            'image' => $dado('image'),
            'PIX' => $dado('PIX'),
            'imagePIX' => $dado('imagePIX'),
            'telephoneConsulting' => $telephoneConsulting,
            'countRaffles' => $dado('countRaffles'),
            'priceUnic' => $dado('priceUnic'),
            'codePIX' => $dado('codePIX'),
            'qrCode' => $dado('qrCode'),
            'codePIXID' => $dado('codePIXID'),
            'minutosRestantes' => $minutosRestantes,
            'config' => $config,
            'user' => User::find(1),
            'products' => $products,
            'ganhadores' => $ganhadores,
            'rifaDestaque' => $rifaDestaque
        ];

        return view('new-checkout', $userData);
    }

    // SEGURANCA: checkPixPaymment() removido (rota de teste que criava cobrancas PIX reais).

    public function findPixStatus($id)
    {
        // O id chega como "<codigo do PIX>-<id da rifa>"
        $posicao = strrpos($id, '-');
        $realPixID = $posicao === false ? $id : substr($id, 0, $posicao);

        $naoPago = json_encode(['status' => false]);

        $paymentPix = DB::table('payment_pix')->where('key_pix', '=', $realPixID)->first();
        if (!$paymentPix) {
            return $naoPago;
        }

        $participante = Participante::find($paymentPix->participant_id);
        if (!$participante) {
            return $naoPago;
        }

        $rifa = $participante->rifa();

        // Ja confirmado (pelo webhook, pelo agendador ou pelo admin)
        if ($paymentPix->status == 'Aprovado' || $paymentPix->status == 'Concluída') {
            return $this->respostaPago($participante);
        }

        // Compra gratuita (valor zero calculado no servidor)
        if ($paymentPix->full_pix == 'gratis') {
            $rifa->confirmPayment($participante->id);
            DB::table('payment_pix')->where('id', '=', $paymentPix->id)->update(['status' => 'Aprovado']);

            return $this->respostaPago($participante->fresh());
        }

        // A tela consulta a cada 2 segundos; o banco/gateway e consultado no maximo a cada 10s por PIX
        if (!Cache::add('consulta_pix_' . md5($realPixID), 1, 10)) {
            return $naoPago;
        }

        $status = null;
        if ($rifa->gateway == 'asaas') {
            $status = Pagamentos::verificarAsaas($realPixID);
        } elseif ($rifa->gateway == 'mp') {
            $status = Pagamentos::verificarMercadoPago($realPixID);
        }
        // Paggue: a confirmacao chega somente pelo webhook assinado (/api/webhook-paggue)

        if ($status === 'approved') {
            return $this->respostaPago($participante->fresh());
        }

        return $naoPago;
    }

    private function respostaPago(Participante $participante)
    {
        return json_encode([
            'status' => true,
            'cotas' => view('layouts.cotas-checkout', ['participante' => $participante])->render()
        ]);
    }

    public function savePayedRaffles($participantID, $productID)
    {
        $getRaffles = DB::table('participant')
            ->select('product_id', 'raffles_id')
            ->where('id', $participantID)
            ->where('product_id', $productID)
            ->get();
        foreach ($getRaffles as $keyRaffle => $valRaffle) :
            CheckoutController::updatePayedRifflesByID($valRaffle->raffles_id, $valRaffle->product_id);
        endforeach;
    }

    public function updatePayedRifflesByID($id, $productID)
    {
        $updateRaffle = DB::table('raffles')
            ->where('id', $id)
            ->where('product_id', $productID)
            ->update([
                'status' => 'Pago'
            ]);
    }

    public function findPedidoStatus($id = NULL)
    {
        $resultRaffles = [];
        if ($id !== NULL) :
            $cleandID = explode("=", $id);
            if (isset($cleandID[0]) && isset($cleandID[1])) :
                $telInfo = $cleandID[0];
                $productID = $cleandID[1];

                $product = DB::table('products')
                    ->select('products.id', 'products.name', 'consulting_environments.key_pix', 'products.price', 'products.draw_date')
                    ->join('consulting_environments', 'consulting_environments.user_id', '=', 'products.user_id')
                    ->where('products.id', '=', $productID)
                    ->first();

                $participantDetail = DB::table('participant')
                    ->select('*')
                    ->where('participant.product_id', '=', $productID)
                    ->where('participant.telephone', '=', $telInfo)
                    ->first();

                $participant = DB::table('participant')
                    ->select('*')
                    ->join('raffles', 'raffles.id', 'participant.raffles_id')
                    ->where('participant.product_id', '=', $productID)
                    ->where('participant.telephone', '=', $telInfo)
                    ->where('raffles.status', '=', 'Pago')
                    ->get();

                $orderParticipants = DB::table('order')
                    ->select('order.id', 'order.key_pix', 'payment_pix.full_pix', 'payment_pix.status')
                    ->join('payment_pix', 'payment_pix.key_pix', 'order.key_pix')
                    ->join('participant', 'participant.id', 'payment_pix.participant_id')
                    ->join('raffles', 'raffles.id', 'participant.raffles_id')
                    ->where('participant.product_id', '=', $productID)
                    ->where('participant.telephone', '=', $telInfo)
                    ->where('raffles.status', '=', 'Reservado')
                    ->groupBy('order.id')
                    ->get();

                $orderPayedParticipants = DB::table('order')
                    ->select('order.id', 'order.key_pix', 'payment_pix.full_pix', 'payment_pix.status')
                    ->join('payment_pix', 'payment_pix.key_pix', 'order.key_pix')
                    ->join('participant', 'participant.id', 'payment_pix.participant_id')
                    ->join('raffles', 'raffles.id', 'participant.raffles_id')
                    ->where('participant.product_id', '=', $productID)
                    ->where('participant.telephone', '=', $telInfo)
                    ->where('raffles.status', '=', 'Pago')
                    ->groupBy('order.id')
                    ->get();

                $participantReserveds = DB::table('participant')
                    ->select('participant.id as user', 'raffles.*', 'payment_pix.*')
                    ->join('raffles', 'raffles.id', 'participant.raffles_id')
                    ->join('payment_pix', 'payment_pix.participant_id', 'participant.id')
                    ->where('participant.product_id', '=', $productID)
                    ->where('participant.telephone', '=', $telInfo)
                    ->where('raffles.status', '=', 'Reservado')
                    ->get();

                $participantPayed = DB::table('participant')
                    ->select('participant.id as user', 'raffles.*', 'payment_pix.*')
                    ->join('raffles', 'raffles.id', 'participant.raffles_id')
                    ->join('payment_pix', 'payment_pix.participant_id', 'participant.id')
                    ->where('participant.product_id', '=', $productID)
                    ->where('participant.telephone', '=', $telInfo)
                    ->where('raffles.status', '=', 'Pago')
                    ->get();

                if (!empty($participantReserveds)) {
                    foreach ($participantReserveds as $rafflesNumber) :
                        $resultParticipantID[] = $rafflesNumber->user;
                        $resultRaffles[] = $rafflesNumber->number;
                    endforeach;

                    $numbers = implode(",", $resultRaffles);
                    $price = count($resultRaffles) * floatval($product->price);
                    $resultPricePIX = number_format($price, 2, ".", ",");
                    $convertPriceBR = number_format($price, 2, ",", ".");
                }

                $telephoneConsulting = DB::table('products')
                    ->select('users.telephone')
                    ->join('users', 'users.id', '=', 'products.user_id')
                    ->where('products.id', '=', $productID)
                    ->first();

                $userData = [
                    'product' => $product,
                    'participantDetail' => $participantDetail,
                    'resultRafflesALLs' => $participant,
                    'orderParticipants' => $orderParticipants,
                    'participantReserveds' => $participantReserveds,
                    'orderPayedParticipants' => $orderPayedParticipants,
                    'participantPayed' => $participantPayed,
                    'priceReserveds' => @$resultPricePIX,
                    'numberReserveds' => @$numbers,
                    'telephone' => @$telephoneConsulting,
                    'priceBR' => @$convertPriceBR
                ];
                return view('consulting-reservation', $userData);
            endif;
        endif;
    }

    public function minhasReservas(Request $request)
    {
        $participante = Participante::where('telephone', '=', $request->telephone)->orderBy('created_at', 'desc')->get();
        $rifas = [];

        foreach ($participante as $reserva) {
            $rifa = $reserva->rifa();
            $rifas[$rifa->id] = $rifa->name;
        }
        

        $config = DB::table('consulting_environments')->where('id', '=', 1)->first();

        $data = [
            'reservas' => $participante,
            'rifas' => $rifas,
            'config' => $config
        ];

        return view('minhas-reservas', $data);
    }

    public function consultingReservation(Request $request)
    {
        $resultRaffles = [];
        $product = DB::table('products')
            ->select('products.id', 'products.name', 'consulting_environments.key_pix', 'products.price', 'products.draw_date')
            ->join('consulting_environments', 'consulting_environments.user_id', '=', 'products.user_id')
            ->where('products.id', '=', $request->productID)
            ->first();

        $participantDetail = DB::table('participant')
            ->select('*')
            ->where('participant.product_id', '=', $request->productID)
            ->where('participant.telephone', '=', $request->telephone)
            ->first();

        $participant = DB::table('participant')
            ->select('*')
            ->join('raffles', 'raffles.id', 'participant.raffles_id')
            ->where('participant.product_id', '=', $request->productID)
            ->where('participant.telephone', '=', $request->telephone)
            ->where('raffles.status', '=', 'Pago')
            ->get();

        $orderParticipants = DB::table('order')
            ->select('order.id', 'order.key_pix', 'payment_pix.full_pix', 'payment_pix.status')
            ->join('payment_pix', 'payment_pix.key_pix', 'order.key_pix')
            ->join('participant', 'participant.id', 'payment_pix.participant_id')
            ->join('raffles', 'raffles.id', 'participant.raffles_id')
            ->where('participant.product_id', '=', $request->productID)
            ->where('participant.telephone', '=', $request->telephone)
            ->where('raffles.status', '=', 'Reservado')
            ->groupBy('order.id')
            ->get();

        $orderPayedParticipants = DB::table('order')
            ->select('order.id', 'order.key_pix', 'payment_pix.full_pix', 'payment_pix.status')
            ->join('payment_pix', 'payment_pix.key_pix', 'order.key_pix')
            ->join('participant', 'participant.id', 'payment_pix.participant_id')
            ->join('raffles', 'raffles.id', 'participant.raffles_id')
            ->where('participant.product_id', '=', $request->productID)
            ->where('participant.telephone', '=', $request->telephone)
            ->where('raffles.status', '=', 'Pago')
            ->groupBy('order.id')
            ->get();

        $participantReserveds = DB::table('participant')
            ->select('participant.id as user', 'raffles.*', 'payment_pix.*')
            ->join('raffles', 'raffles.id', 'participant.raffles_id')
            ->join('payment_pix', 'payment_pix.participant_id', 'participant.id')
            ->where('participant.product_id', '=', $request->productID)
            ->where('participant.telephone', '=', $request->telephone)
            ->where('raffles.status', '=', 'Reservado')
            ->get();

        $participantPayed = DB::table('participant')
            ->select('participant.id as user', 'raffles.*', 'payment_pix.*')
            ->join('raffles', 'raffles.id', 'participant.raffles_id')
            ->join('payment_pix', 'payment_pix.participant_id', 'participant.id')
            ->where('participant.product_id', '=', $request->productID)
            ->where('participant.telephone', '=', $request->telephone)
            ->where('raffles.status', '=', 'Pago')
            ->get();

        if (!empty($participantReserveds)) {
            foreach ($participantReserveds as $rafflesNumber) :
                $resultParticipantID[] = $rafflesNumber->user;
                $resultRaffles[] = $rafflesNumber->number;
            endforeach;

            $numbers = implode(",", $resultRaffles);
            $price = count($resultRaffles) * floatval($product->price);
            $resultPricePIX = number_format($price, 2, ".", ",");
            $convertPriceBR = number_format($price, 2, ",", ".");
        }

        $telephoneConsulting = DB::table('products')
            ->select('users.telephone')
            ->join('users', 'users.id', '=', 'products.user_id')
            ->where('products.id', '=', $request->productID)
            ->first();

        $participante = Participante::where('telephone', '=', $request->telephone)->where('product_id', '=', $request->productID)->get();
        $rifas = [];

        foreach ($participante as $reserva) {
            $rifa = $reserva->rifa();
            $rifas[$rifa->id] = $rifa->name;
        }

        $config = DB::table('consulting_environments')->where('id', '=', 1)->first();

        $data = [
            'reservas' => $participante,
            'rifas' => $rifas,
            'config' => $config
        ];

        return view('minhas-reservas', $data);

        // $userData = [
        //     'product' => $product,
        //     'participantDetail' => $participantDetail,
        //     'resultRafflesALLs' => $participant,
        //     'orderParticipants' => $orderParticipants,
        //     'participantReserveds' => $participantReserveds,
        //     'orderPayedParticipants' => $orderPayedParticipants,
        //     'participantPayed' => $participantPayed,
        //     'priceReserveds' => @$resultPricePIX,
        //     'numberReserveds' => @$numbers,
        //     'telephone' => @$telephoneConsulting,
        //     'priceBR' => @$convertPriceBR
        // ];
        // return view('consulting-reservation', $userData);
    }

    public function consultingReservationTelephone(Request $request, $productID, $telephone)
    {

        $substr = substr($telephone, 0, 2);
        $ddd = '(' . $substr . ')';
        $substr1 = ' ' . substr($telephone, 2, 5) . '-';
        $substr2 = substr($telephone, 7);
        $resultTelephone = $ddd . $substr1 . $substr2;

        $product = DB::table('products')
            ->select('products.id', 'products.name', 'consulting_environments.key_pix', 'products.price', 'products.draw_date')
            ->join('consulting_environments', 'consulting_environments.user_id', '=', 'products.user_id')
            ->where('products.id', '=', $productID)
            ->first();


        $participantDetail = DB::table('participant')
            ->select('*')
            ->where('participant.product_id', '=', $productID)
            ->where('participant.telephone', '=', $resultTelephone)
            ->first();

        $participant = DB::table('participant')
            ->select('*')
            ->join('raffles', 'raffles.id', 'participant.raffles_id')
            ->where('participant.product_id', '=', $productID)
            ->where('participant.telephone', '=', $resultTelephone)
            ->where('raffles.status', '=', 'Pago')
            ->get();

        $orderParticipants = DB::table('order')
            ->select('order.id', 'order.key_pix', 'payment_pix.full_pix')
            ->join('payment_pix', 'payment_pix.key_pix', 'order.key_pix')
            ->join('participant', 'participant.id', 'payment_pix.participant_id')
            ->join('raffles', 'raffles.id', 'participant.raffles_id')
            ->where('participant.product_id', '=', $productID)
            ->where('participant.telephone', '=', $resultTelephone)
            ->where('raffles.status', '=', 'Reservado')
            ->groupBy('order.id')
            ->get();

        //dd($orderParticipants);

        $participantReserveds = DB::table('participant')
            ->select('participant.id as user', 'raffles.*', 'payment_pix.*')
            ->join('raffles', 'raffles.id', 'participant.raffles_id')
            ->join('payment_pix', 'payment_pix.participant_id', 'participant.id')
            ->where('participant.product_id', '=', $productID)
            ->where('participant.telephone', '=', $resultTelephone)
            ->where('raffles.status', '=', 'Reservado')
            ->get();

        /*$participantReserveds = DB::table('participant')
            ->select('participant.id as user', 'raffles.*', 'payment_pix.full_pix', 'order.id as order')
            ->join('raffles', 'raffles.id', 'participant.raffles_id')
            ->join('payment_pix', 'participant.id', 'payment_pix.participant_id')
            ->join('order', 'payment_pix.key_pix', 'order.key_pix')
            ->where('participant.product_id', '=', $productID)
            ->where('participant.telephone', '=', $resultTelephone)
            ->where('raffles.status', '=', 'Reservado')
            ->get();*/

        $telephoneConsulting = DB::table('products')
            ->select('users.telephone')
            ->join('users', 'users.id', '=', 'products.user_id')
            ->where('products.id', '=', $request->productID)
            ->first();

        if ($participantReserveds == '[]') {
        } else {
            foreach ($participantReserveds as $rafflesNumber) {
                $resultParticipantID[] = $rafflesNumber->user;
                $resultRaffles[] = $rafflesNumber->number;
            }

            $numbers = implode(",", $resultRaffles);

            $price = count($resultRaffles) * floatval($product->price);
            $resultPricePIX = number_format($price, 2, ".", ",");
            $convertPriceBR = number_format($price, 2, ",", ".");
        }

        return view('consulting-reservation', [
            'product' => $product,
            'participantDetail' => $participantDetail,
            'resultRafflesALLs' => $participant,
            'orderParticipants' => $orderParticipants,
            'participantReserveds' => $participantReserveds,
            'priceReserveds' => @$resultPricePIX,
            'numberReserveds' => @$numbers,
            'telephone' => $telephoneConsulting,
            'priceBR' => @$convertPriceBR
        ]);
    }

    public function consultingReservationManualy(Request $request)
    {

        //METODO consultingReservationTelephone E consultingReservation SÃO IGUAIS SEMPRE DEIXAR OS DOIS PARECIDOS

        $product = DB::table('products')
            ->select('products.id', 'products.name', 'consulting_environments.key_pix', 'products.price', 'products.draw_date')
            ->join('consulting_environments', 'consulting_environments.user_id', '=', 'products.user_id')
            ->where('products.id', '=', $request->productID)
            ->first();

        $participantDetail = DB::table('participant')
            ->select('*')
            ->where('participant.product_id', '=', $request->productID)
            ->where('participant.telephone', '=', $request->telephone)
            ->first();

        $participant = DB::table('participant')
            ->select('*')
            ->join('raffles', 'raffles.id', 'participant.raffles_id')
            ->where('participant.product_id', '=', $request->productID)
            ->where('participant.telephone', '=', $request->telephone)
            ->where('raffles.status', '=', 'Pago')
            ->get();

        //dd($participant);

        $orderParticipants = DB::table('order')
            ->select('order.id', 'order.key_pix', 'payment_pix.full_pix')
            ->join('payment_pix', 'payment_pix.key_pix', 'order.key_pix')
            ->join('participant', 'participant.id', 'payment_pix.participant_id')
            ->join('raffles', 'raffles.id', 'participant.raffles_id')
            ->where('participant.product_id', '=', $request->productID)
            ->where('participant.telephone', '=', $request->telephone)
            ->where('raffles.status', '=', 'Reservado')
            ->groupBy('order.id')
            ->get();

        //dd($orderParticipants);

        $participantReserveds = DB::table('participant')
            ->select('participant.id as user', 'raffles.*')
            ->join('raffles', 'raffles.id', 'participant.raffles_id')
            ->where('participant.product_id', '=', $request->productID)
            ->where('participant.telephone', '=', $request->telephone)
            ->where('raffles.status', '=', 'Reservado')
            ->get();

        if ($participantReserveds == '[]') {
        } else {
            foreach ($participantReserveds as $rafflesNumber) {

                $resultParticipantID[] = $rafflesNumber->user;
                $resultRaffles[] = $rafflesNumber->number;
            }

            $numbers = implode(",", $resultRaffles);

            $price = count($resultRaffles) * floatval($product->price);
            $resultPricePIX = number_format($price, 2, ".", ",");
            $convertPriceBR = number_format($price, 2, ",", ".");
        }

        $telephoneConsulting = DB::table('products')
            ->select('users.telephone')
            ->join('users', 'users.id', '=', 'products.user_id')
            ->where('products.id', '=', $request->productID)
            ->first();


        return view('consulting-reservation-manualy', [
            'product' => $product,
            'participantDetail' => $participantDetail,
            'resultRafflesALLs' => $participant,
            'orderParticipants' => $orderParticipants,
            'participantReserveds' => $participantReserveds,
            'priceReserveds' => @$resultPricePIX,
            'numberReserveds' => @$numbers,
            'telephone' => @$telephoneConsulting,
            'priceBR' => @$convertPriceBR
        ]);
    }

    public function consultingReservationTelephoneManualy(Request $request, $productID, $telephone)
    {

        $substr = substr($telephone, 0, 2);
        $ddd = '(' . $substr . ')';
        $substr1 = ' ' . substr($telephone, 2, 5) . '-';
        $substr2 = substr($telephone, 7);
        $resultTelephone = $ddd . $substr1 . $substr2;

        $product = DB::table('products')
            ->select('products.id', 'products.name', 'consulting_environments.key_pix', 'products.price', 'products.draw_date')
            ->join('consulting_environments', 'consulting_environments.user_id', '=', 'products.user_id')
            ->where('products.id', '=', $productID)
            ->first();


        $participantDetail = DB::table('participant')
            ->select('*')
            ->where('participant.product_id', '=', $productID)
            ->where('participant.telephone', '=', $resultTelephone)
            ->first();

        $participant = DB::table('participant')
            ->select('*')
            ->join('raffles', 'raffles.id', 'participant.raffles_id')
            ->where('participant.product_id', '=', $productID)
            ->where('participant.telephone', '=', $resultTelephone)
            ->where('raffles.status', '=', 'Pago')
            ->get();

        $orderParticipants = DB::table('order')
            ->select('order.id', 'order.key_pix', 'payment_pix.full_pix')
            ->join('payment_pix', 'payment_pix.key_pix', 'order.key_pix')
            ->join('participant', 'participant.id', 'payment_pix.participant_id')
            ->join('raffles', 'raffles.id', 'participant.raffles_id')
            ->where('participant.product_id', '=', $productID)
            ->where('participant.telephone', '=', $resultTelephone)
            ->where('raffles.status', '=', 'Reservado')
            ->groupBy('order.id')
            ->get();

        //dd($orderParticipants);

        $participantReserveds = DB::table('participant')
            ->select('participant.id as user', 'raffles.*')
            ->join('raffles', 'raffles.id', 'participant.raffles_id')
            ->where('participant.product_id', '=', $productID)
            ->where('participant.telephone', '=', $resultTelephone)
            ->where('raffles.status', '=', 'Reservado')
            ->get();

        /*$participantReserveds = DB::table('participant')
            ->select('participant.id as user', 'raffles.*', 'payment_pix.full_pix', 'order.id as order')
            ->join('raffles', 'raffles.id', 'participant.raffles_id')
            ->join('payment_pix', 'participant.id', 'payment_pix.participant_id')
            ->join('order', 'payment_pix.key_pix', 'order.key_pix')
            ->where('participant.product_id', '=', $productID)
            ->where('participant.telephone', '=', $resultTelephone)
            ->where('raffles.status', '=', 'Reservado')
            ->get();*/

        $telephoneConsulting = DB::table('products')
            ->select('users.telephone')
            ->join('users', 'users.id', '=', 'products.user_id')
            ->where('products.id', '=', $request->productID)
            ->first();

        if ($participantReserveds == '[]') {
        } else {
            foreach ($participantReserveds as $rafflesNumber) {
                $resultParticipantID[] = $rafflesNumber->user;
                $resultRaffles[] = $rafflesNumber->number;
            }

            $numbers = implode(",", $resultRaffles);

            $price = count($resultRaffles) * floatval($product->price);
            $resultPricePIX = number_format($price, 2, ".", ",");
            $convertPriceBR = number_format($price, 2, ",", ".");
        }

        return view('consulting-reservation-manualy', [
            'product' => $product,
            'participantDetail' => $participantDetail,
            'resultRafflesALLs' => $participant,
            'orderParticipants' => $orderParticipants,
            'participantReserveds' => $participantReserveds,
            'priceReserveds' => @$resultPricePIX,
            'numberReserveds' => @$numbers,
            'telephone' => $telephoneConsulting,
            'priceBR' => @$convertPriceBR
        ]);
    }

    // SEGURANCA: paymentPix() e paymentCredit() removidos. Nao eram usados pelas telas;
    // o primeiro deixava trocar o codigo PIX de qualquer compra e o segundo aceitava o
    // valor do cartao enviado pelo navegador.

    public function pagarReserva($id)
    {
        // Rota protegida por link assinado (middleware 'signed')
        $participante = Participante::find($id);
        if (!$participante || !$participante->order()) {
            return redirect()->route('inicio')->withErrors('Reserva não encontrada ou expirada.');
        }

        return redirect()->to(URL::signedRoute('checkoutManualy', ['participant_id' => $participante->id]));
    }

    public function getCustomer(Request $request)
    {
        // LGPD: devolve apenas o necessario para a tela (id e primeiro nome).
        // CPF, e-mail e nome completo nao sao mais enviados ao navegador.
        $customer = Customer::where('telephone', '=', $request->phone)->first();

        $response['customer'] = null;
        if ($customer) {
            $partes = explode(' ', trim($customer->nome));
            $response['customer'] = [
                'id' => $customer->id,
                'nome' => $partes[0],
                'telephone' => $customer->telephone,
            ];
        }

        return $response;
    }
}
