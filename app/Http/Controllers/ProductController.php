<?php

namespace App\Http\Controllers;

use App\AutoMessage;
use App\Helpers\Helper;
use App\Participant;
use App\Product;
use App\CreateProductimage;
use App\Customer;
use App\Environment;
use App\GanhosAfiliado;
use App\Models\Order;
use App\Models\Participante;
use App\Models\Premio;
use App\Models\Product as ModelsProduct;
use App\Models\Raffle;
use App\RifaAfiliado;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Services\Pagamentos;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use QRcode;

class ProductController extends Controller
{
    public function index()
    {
        $ganhadores = Premio::where('descricao', '!=', null)->where('ganhador', '!=', '')->get();

        $products = ModelsProduct::where('visible', '=', 1)->orderBy('id', 'desc')->get();

        $winners = ModelsProduct::where('status', '=', 'Finalizado')->where('visible', '=', 1)->where('winner', '!=', null)->get();
        
        $config = DB::table('consulting_environments')->where('id', '=', 1)->first();

        return view('welcome', [
            'products' => $products,
            'winners' => $winners,
            'ganhadores' => $ganhadores,
            'user' => User::find(1),
            'productModel' => ModelsProduct::find(4),
            'config' => $config
        ]);
    }

    public function sorteios()
    {
        $ganhadores = Premio::where('descricao', '!=', null)->where('ganhador', '!=', '')->get();

        $products = ModelsProduct::where('visible', '=', 1)->orderBy('id', 'desc')->get();

        $winners = ModelsProduct::where('status', '=', 'Finalizado')->where('visible', '=', 1)->where('winner', '!=', null)->get();
        
        $config = DB::table('consulting_environments')->where('id', '=', 1)->first();

        return view('sorteios', [
            'products' => $products,
            'winners' => $winners,
            'ganhadores' => $ganhadores,
            'user' => User::find(1),
            'productModel' => ModelsProduct::find(4),
            'config' => $config
        ]);
    }

    public function product($slug, $tokenAfiliado = null)
    {
        //dd($idProductURL);
        $productSlug = DB::table('products')
            ->select('id')
            ->where('products.slug', '=', $slug)
            ->get();
        if ($productSlug->count() == 0) {
            abort(404);
        }
        $productID = $productSlug[0]->id;

        // Verificando se sorteio ja expirou para finalizar automatico.
        $this->verificaSorteio($productID);



        //dd($productID);
        $user = DB::table('users')
            ->select('users.name', 'users.telephone', 'products.type_raffles')
            ->leftJoin('products', 'products.user_id', 'users.id')
            ->leftJoin('consulting_environments', 'consulting_environments.user_id', 'users.id')
            ->where('products.id', '=', $productID)
            ->first();
        $imagens = DB::table('products_images')
            ->select('products_images.name')
            ->join('products', 'products.id', '=', 'products_images.product_id')
            ->where('products.id', '=', $productID)
            ->get();
        //dd($imagens);



        $productDetail = DB::table('products')
            ->select('products.id', 'products.name', 'products.subname', 'products.product', 'products.price', 'products_images.name as image', 'products.status', 'products.draw_date', 'products.draw_prediction', 'products.winner')
            ->leftJoin('products_images', 'products.id', 'products_images.product_id')
            ->where('products.id', '=', $productID)
            ->orderBy('products_images.name', 'ASC')
            ->get();

        $bookProduct = DB::table('products')
            ->select('products.name', 'products.price', 'raffles.number', 'raffles.status', 'products.status as statusProduct', 'participant.name as participant', 'participant.created_at')
            ->join('raffles', 'products.id', '=', 'raffles.product_id')
            ->leftJoin('participant', 'raffles.id', 'participant.raffles_id')
            ->where('products.id', '=', $productID)
            ->get();

        $productDescription = DB::table('product_description')
            ->select('product_description.description', 'product_description.video')
            ->join('products', 'products.id', '=', 'product_description.product_id')
            ->where('products.id', '=', $productID)
            ->first();
        //dd($productDescription->description);


        //TOTAIS DE NÚMEROS
        $totalNumbers = DB::table('products')
            ->select('raffles.status')
            ->join('raffles', 'products.id', '=', 'raffles.product_id')
            ->where('products.id', '=', $productID)
            ->count();

        $totalDispo = DB::table('products')
            ->select('raffles.status')
            ->join('raffles', 'products.id', '=', 'raffles.product_id')
            ->where('products.id', '=', $productID)
            ->where('raffles.status', '=', 'Disponível')
            ->count();

        $totalReser = DB::table('products')
            ->select('raffles.status')
            ->join('raffles', 'products.id', '=', 'raffles.product_id')
            ->where('products.id', '=', $productID)
            ->where('raffles.status', '=', 'Reservado')
            ->count();

        $totalPago = DB::table('products')
            ->select('raffles.status')
            ->join('raffles', 'products.id', '=', 'raffles.product_id')
            ->where('products.id', '=', $productID)
            ->where('raffles.status', '=', 'Pago')
            ->count();

        $valueProduct = DB::table('products')
            ->select('price')
            ->where('products.id', '=', $productID)
            ->first();

        $value50 = str_replace(',', '.', $valueProduct->price) * 50;
        $value100 = str_replace(',', '.', $valueProduct->price) * 100;
        $value150 = str_replace(',', '.', $valueProduct->price) * 150;
        $value200 = str_replace(',', '.', $valueProduct->price) * 200;

        $result50 = $value50 - ($value50 * 10 / 100);
        $result100 = $value100 - ($value100 * 10 / 100);
        $result150 = $value150 - ($value150 * 10 / 100);
        $result200 = $value200 - ($value200 * 10 / 100);

        $productModel = ModelsProduct::find($productID);

        $config = DB::table('consulting_environments')->where('id', '=', 1)->first();

        $arrayProducts = [
            'tokenAfiliado' => $tokenAfiliado,
            'imagens' => $imagens,
            'product' => $productDetail,
            'bookProduct' => $bookProduct,
            'productDescription' => $productDescription ? $productDescription->description : '',
            'productDescriptionVideo' => $productDescription ? $productDescription->video : '',
            'totalNumbers' => $totalNumbers,
            'totalDispo' => $totalDispo,
            'totalReser' => $totalReser,
            'totalPago' => $totalPago,
            'user' => $user->name,
            'telephone' => $user->telephone,
            'type_raffles' => $user->type_raffles,
            'result50' => number_format($result50, 2, ",", "."),
            'result100' => number_format($result100, 2, ",", "."),
            'result150' => number_format($result150, 2, ",", "."),
            'result200' => number_format($result200, 2, ",", "."),
            'productModel' => $productModel,
            'ranking' => $productModel->ranking(),
            'config' => $config,
            'user' => User::find(1),
        ];


        return view('product-detail', $arrayProducts);
    }

    public function verificaSorteio($productId)
    {
        
        // Verificando se sorteio ja expirou ou se ja foi vendido todas as cotas para finalizar automatico.
        $product = ModelsProduct::find($productId);
        if($product->qtdNumerosDisponiveis() == 0){
            $product->status = 'Finalizado';
            $product->update();
        }
    }

    public function randomParticipant()
    {
        $userRandom = Participant::inRandomOrder()->select('name')->first();
        $resultUserRandom = explode(' ', $userRandom->name);

        return json_encode($resultUserRandom);
    }

    public function getRaffles(Request $request)
    {
        $bookProduct = DB::table('products')
            ->select('products.name', 'products.price', 'raffles.number', 'raffles.status', 'products.status as statusProduct', 'participant.name as participant', 'participant.created_at', 'products.qtd_zeros')
            ->join('raffles', 'products.id', '=', 'raffles.product_id')
            ->leftJoin('participant', 'raffles.id', 'participant.raffles_id')
            ->where('products.id', '=', $request->idProductURL)
            ->get();
        
        $rifa = ModelsProduct::find($request->idProductURL);
        $numbers = $rifa->numbers();

        foreach ($rifa->participantes() as $participante) {
            $statusParticipante = $participante->pagos > 0 ? 'pago' : 'reservado';
            foreach ($participante->numbers() as $value) {
                array_push($numbers, $value . '-' . $statusParticipante . '-' . $participante->name);
            }
        }

        sort($numbers);

        foreach ($numbers as $number) {
            $bg = env('APP_URL') == 'rifasonline.link' ? '#A0A1A3' : '#585858';
            $ex = explode("-", $number);
            $number = $ex[0];
            
            $status = 'disponivel';
            if(isset($ex[1])){
                $status = $ex[1];
                $nome = $ex[2];
            }

            if($status == 'disponivel'){
                $resultRaffles[] = "<a href='javascript:void(0);' class='number filter ".$status."' onclick=\"selectRaffles('" . $number . "', '" . $number . "')\" style='background-color: ".$bg.";color: #000;' id=" . $number . ">" . $number . "</a>";
            }
            else if($status == 'reservado'){
                $nome = 'Reservado por ' . $nome;
                $resultRaffles[] = "<a href='javascript:void(0);' class='number filter ".$status."' onclick=\"infoParticipante('" . $nome . "')\" style='background-color: rgb(13,202,240);color: #000;' id=" . $number . ">" . $number . "</a>";
            }
            else if($status == 'pago'){
                $nome = 'Pago por ' . $nome;
                $resultRaffles[] = "<a href='javascript:void(0);' class='number filter ".$status."' onclick=\"infoParticipante('" . $nome . "')\" style='background-color: #28a745;color: #000;' id=" . $number . ">" . $number . "</a>";
            }

            

            // if ($rifa->status === 'Ativo') {
            //     if ($number['status'] === 'Disponivel') {
            //         $resultRaffles[] = "<a href='javascript:void(0);' class='number filter disponivel' onclick=\"selectRaffles('" . $number['number'] . "', '" . $number['key'] . "')\" style='background-color: #585858;color: #000;' id=" . $number['number'] . ">" . $number['number'] . "</a>";
            //     } else if ($number['status']  == "Reservado") {
            //         $resultRaffles[] = "<a href='javascript:void(0);' class='number filter reservado' onclick=\"timeNumbers(this)\" style='background-color: #d7d5d5;color: #000;display:none;' id=" . $number['number'] . " data-toggle='tooltip' data-placement='top' data-html='true' title='Reservado por: " . $rifa->getParticipanteById($number['participant_id']) . "'><input type='hidden' id='createdAt" . $number['number'] . "' value=''>" . $number['number'] . "</a>";
            //     } else if ($number['status'] == "Pago") {
            //         $resultRaffles[] = "<a href='javascript:void(0);' class='number filter pago' style='background-color: #28a745;color: #fff;display:none;' id='" . $number['number'] . "' data-toggle='tooltip' data-placement='top' title='Pago por: " . $rifa->getParticipanteById($number['participant_id']) . "'>" . $number['number'] . "</a>";
            //     }
            // } else if ($rifa->status === 'Agendado') {
            //     if ($number['status'] === 'Disponivel') {
            //         $resultRaffles[] = "<a href='javascript:void(0);' class='number filter disponivel' onclick=\"alert('Sorteio agendado não é mais possível reservar!')\" style='background-color: #f1f1f1;color: #000;' id=" . $number['number'] . ">" . $number['number'] . "</a>";
            //     } else if ($number['status']  == "Reservado") {
            //         $resultRaffles[] = "<a href='javascript:void(0);' class='number filter reservado' style='background-color: #0F9EE2;color: #fff;' id=" . $number['number'] . " data-toggle='tooltip' data-placement='top' data-html='true' title='Reservado por: " . $rifa->getParticipanteById($number['participant_id']) . "'><input type='hidden' id='createdAt" . $number['number'] . "' value=''>" . $number['number'] . "</a>";
            //     } else if ($number['status'] == "Pago") {
            //         $resultRaffles[] = "<a href='javascript:void(0);' class='number filter pago' style='background-color: #28a745;color: #fff;' id='" . $number['number'] . "' data-toggle='tooltip' data-placement='top' title='Pago por: " . $rifa->getParticipanteById($number['participant_id']) . "'>" . $number['number'] . "</a>";
            //     }
            // } else if ($rifa->status === 'Finalizado') {
            //     if ($number['status'] === 'Disponivel') {
            //         $resultRaffles[] = "<a href='javascript:void(0);' class='number filter disponivel' onclick=\"alert('Sorteio finalizado não é mais possível reservar!')\" style='background-color: #f1f1f1;color: #fff;' id=" . $number['number'] . ">" . $number['number'] . "</a>";
            //     } else if ($number['status']  == "Reservado") {
            //         $resultRaffles[] = "<a href='javascript:void(0);' class='number filter reservado' style='background-color: #0F9EE2;color: #fff;' id=" . $number['number'] . " data-toggle='tooltip' data-placement='top' data-html='true' title='Reservado por: " . $rifa->getParticipanteById($number['participant_id']) . "'><input type='hidden' id='createdAt" . $number['number'] . "' value=''>" . $number['number'] . "</a>";
            //     } else if ($number['status'] == "Pago") {
            //         $resultRaffles[] = "<a href='javascript:void(0);' class='number filter pago' style='background-color: #28a745;color: #fff;' id='" . $number['number'] . "' data-toggle='tooltip' data-placement='top' title='Pago por: " . $rifa->getParticipanteById($number['participant_id']) . "'>" . $number['number'] . "</a>";
            //     }
            // } else {
            //     $resultRaffles[] = null;
            // }
        }


        // foreach ($bookProduct as $raffles) {

        //     // Mostrando a qtd de zeros qe esta configurado no painel
        //     if ($raffles->qtd_zeros != null || $raffles->qtd_zeros == 0) {
        //         $number = intval($raffles->number); // retira os 0 convertendo para inteiro
        //         $number = strval($number);          // converte novamente para string
        //         for ($i = 0; $i < $raffles->qtd_zeros; $i++) {
        //             $number = '0' . $number;
        //         }

        //         $number = $raffles->number;
        //     } else {
        //         $number = $raffles->number;
        //     }

        //     if ($raffles->statusProduct === 'Ativo') {
        //         if ($raffles->status === 'Disponível') {
        //             $resultRaffles[] = "<a href='javascript:void(0);' class='number filter disponivel' onclick=\"selectRaffles('" . $number . "')\" style='background-color: #585858;color: #000;' id=" . $number . ">" . $number . "</a>";
        //         } else if ($raffles->status  == "Reservado") {
        //             $resultRaffles[] = "<a href='javascript:void(0);' class='number filter reservado' onclick=\"timeNumbers(this)\" style='background-color: #d7d5d5;color: #000;display:none;' id=" . $number . " data-toggle='tooltip' data-placement='top' data-html='true' title='Reservado por: " . $raffles->participant . "'><input type='hidden' id='createdAt" . $number . "' value=''>" . $number . "</a>";
        //         } else if ($raffles->status == "Pago") {
        //             $resultRaffles[] = "<a href='javascript:void(0);' class='number filter pago' style='background-color: #28a745;color: #fff;display:none;' id='" . $number . "' data-toggle='tooltip' data-placement='top' title='Pago por: " . $raffles->participant . "'>" . $number . "</a>";
        //         }
        //     } else if ($raffles->statusProduct === 'Agendado') {
        //         if ($raffles->status === 'Disponível') {
        //             $resultRaffles[] = "<a href='javascript:void(0);' class='number filter disponivel' onclick=\"alert('Sorteio agendado não é mais possível reservar!')\" style='background-color: #f1f1f1;color: #000;' id=" . $number . ">" . $number . "</a>";
        //         } else if ($raffles->status  == "Reservado") {
        //             $resultRaffles[] = "<a href='javascript:void(0);' class='number filter reservado' style='background-color: #0F9EE2;color: #fff;' id=" . $number . " data-toggle='tooltip' data-placement='top' data-html='true' title='Reservado por: " . $raffles->participant . "'><input type='hidden' id='createdAt" . $number . "' value=''>" . $number . "</a>";
        //         } else if ($raffles->status == "Pago") {
        //             $resultRaffles[] = "<a href='javascript:void(0);' class='number filter pago' style='background-color: #28a745;color: #fff;' id='" . $number . "' data-toggle='tooltip' data-placement='top' title='Pago por: " . $raffles->participant . "'>" . $number . "</a>";
        //         }
        //     } else if ($raffles->statusProduct === 'Finalizado') {
        //         if ($raffles->status === 'Disponível') {
        //             $resultRaffles[] = "<a href='javascript:void(0);' class='number filter disponivel' onclick=\"alert('Sorteio finalizado não é mais possível reservar!')\" style='background-color: #f1f1f1;color: #fff;' id=" . $number . ">" . $number . "</a>";
        //         } else if ($raffles->status  == "Reservado") {
        //             $resultRaffles[] = "<a href='javascript:void(0);' class='number filter reservado' style='background-color: #0F9EE2;color: #fff;' id=" . $number . " data-toggle='tooltip' data-placement='top' data-html='true' title='Reservado por: " . $raffles->participant . "'><input type='hidden' id='createdAt" . $number . "' value=''>" . $number . "</a>";
        //         } else if ($raffles->status == "Pago") {
        //             $resultRaffles[] = "<a href='javascript:void(0);' class='number filter pago' style='background-color: #28a745;color: #fff;' id='" . $number . "' data-toggle='tooltip' data-placement='top' title='Pago por: " . $raffles->participant . "'>" . $number . "</a>";
        //         }
        //     } else {
        //         $resultRaffles[] = null;
        //     }
        // }

        return json_encode($resultRaffles);
    }

    public function formatMoney($value)
    {
        $value = str_replace('.', "", $value);
        $value = str_replace(',', ".", $value);

        return $value;
    }

    //REVERSA OS NÚMEROS DO SORTEIO X SEM INTEGRAÇÃO COM O PIX
    public function bookProductManualy(Request $request)
    {
        // Etapa 1: escolhe e reserva os numeros dentro de uma transacao com TRAVA na rifa
        // (lockForUpdate). Assim duas compras ao mesmo tempo nunca pegam o mesmo numero.
        DB::beginTransaction();
        try {
            $reserva = $this->reservarNumerosDaCompra($request);
        } catch (ValidationException $e) {
            DB::rollBack();
            throw $e;
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error('Erro ao reservar numeros: ' . $th->getMessage() . ' em ' . $th->getFile() . ':' . $th->getLine());
            return Redirect::back()->withErrors('Não foi possível concluir a reserva. Tente novamente.');
        }

        if (!is_array($reserva)) {
            // alguma validacao falhou: desfaz tudo e mostra a mensagem
            DB::rollBack();
            return $reserva;
        }

        DB::commit();

        // Etapa 2: gera a cobranca no banco (fora da trava, para nao travar outras compras
        // enquanto o gateway responde). Se falhar, os numeros voltam a ficar disponiveis.
        try {
            return $this->gerarCobrancaDaCompra($request, $reserva);
        } catch (\Throwable $th) {
            Log::error('Erro ao gerar cobranca da compra ' . $reserva['participanteId'] . ': ' . $th->getMessage() . ' em ' . $th->getFile() . ':' . $th->getLine());
            $this->desfazerReserva($reserva['participanteId']);
            return Redirect::back()->withErrors('Não foi possível gerar o PIX. Tente novamente.');
        }
    }

    /**
     * Reserva os numeros. Retorna um array com os dados da reserva ou um redirect com erro.
     * Deve ser chamado dentro de uma transacao.
     */
    private function reservarNumerosDaCompra(Request $request)
    {
        $qtdNumbers = (int) $request->qtdNumbers;

        if ($qtdNumbers > 10000) {
            return Redirect::back()->withErrors('Você só pode comprar no máximo 10.000 números por vez');
        }

        if (!$request->name && !$request->customer) {
            return Redirect::back()->withErrors('Campo nome é obrigatório!');
        }
        if (!$request->telephone) {
            return Redirect::back()->withErrors('Campo telefone é obrigatório!');
        }

        // TRAVA: a partir daqui nenhuma outra compra desta rifa avanca ate o commit
        $prod = ModelsProduct::lockForNumbers($request->productID);
        if (!$prod) {
            return Redirect::back()->withErrors('Sorteio não encontrado!');
        }

        // Cliente: so reaproveita o cadastro se o telefone for o mesmo
        $customer = null;
        if ($request->customer) {
            $customer = Customer::find($request->customer);
            if ($customer && $customer->telephone !== $request->telephone) {
                $customer = null;
            }
        }
        if (!$customer) {
            if (!$request->name) {
                return Redirect::back()->withErrors('Campo nome é obrigatório!');
            }
            $customer = Customer::create([
                'nome' => $request->name,
                'telephone' => $request->telephone
            ]);
        }
        $nomeParticipante = $customer->nome ? $customer->nome : $request->name;

        // Link de afiliado: o token precisa ser desta rifa
        $afiliado = null;
        if ($request->tokenAfiliado) {
            $afiliado = RifaAfiliado::where('token', '=', $request->tokenAfiliado)
                ->where('product_id', '=', $prod->id)
                ->first();
        }

        // Para o gateway ASAAS é obrigatorio o CPF
        if ($prod->gateway == 'asaas') {
            $request->validate([
                'cpf' => 'required',
            ]);
        }

        $codeKeyPIX = DB::table('consulting_environments')
            ->select('key_pix', 'token_asaas', 'paggue_client_key')
            ->where('user_id', '=', 1)
            ->first();

        if (($prod->gateway == 'mp' && !$codeKeyPIX->key_pix)
            || ($prod->gateway == 'asaas' && !$codeKeyPIX->token_asaas)
            || ($prod->gateway == 'paggue' && !$codeKeyPIX->paggue_client_key)) {
            return Redirect::back()->withErrors('Administrador precisa adicionar a integração com o banco!');
        }

        if ($prod->status == "Agendado") {
            return Redirect::back()->withErrors('Sorteio agendado não é mais possível reservar!');
        }
        if ($prod->status != "Ativo") {
            return Redirect::back()->withErrors('Sorteio finalizado não é mais possível reservar!');
        }

        $selecionados = null;
        $numbersRifaAtualizados = null;

        if (str_starts_with($prod->modo_de_jogo, 'fazendinha')) {
            $resutlNumbers = $this->listaDeNumeros($request->numberSelected);
        } elseif ($prod->type_raffles == 'manual' || $prod->type_raffles == 'mesclado') {
            // Escolha manual: cada numero escolhido PRECISA estar disponivel
            $disponiveis = array_flip($prod->numbers());

            $selecionados = [];
            foreach ($this->listaDeNumeros($request->numberSelected) as $value) {
                $expl = explode("-", $value);
                $numero = trim(end($expl));

                if ($numero === '' || !isset($disponiveis[$numero])) {
                    return Redirect::back()->withErrors('O número ' . $numero . ' não está mais disponível. Por favor escolha outros números :)');
                }

                unset($disponiveis[$numero]);
                $selecionados[] = $numero;
            }

            $resutlNumbers = $selecionados;
            $numbersRifaAtualizados = array_map('strval', array_keys($disponiveis));
        } else {
            if ($qtdNumbers < 1) {
                return Redirect::back()->withErrors('Informe a quantidade de números.');
            }

            $disponiveis = $prod->numbers();

            if (count($disponiveis) < $qtdNumbers) {
                return Redirect::back()->withErrors('Quantidade indisponível para a rifa selecionada. A quantidade disponível é: ' . count($disponiveis));
            }

            shuffle($disponiveis);
            $selecionados = array_slice($disponiveis, 0, $qtdNumbers);
            $restantes = array_slice($disponiveis, $qtdNumbers);
            sort($restantes);

            $resutlNumbers = $selecionados;
            $numbersRifaAtualizados = $restantes;
        }

        if (count($resutlNumbers) == 0) {
            return Redirect::back()->withErrors('Escolha pelo menos um número.');
        }

        // Validando minimo e maximo de compra da rifa
        if ($prod->minimo && count($resutlNumbers) < $prod->minimo) {
            return Redirect::back()->withErrors('Você precisa comprar no mínimo ' . $prod->minimo . ' números');
        }
        if ($prod->maximo && count($resutlNumbers) > $prod->maximo) {
            return Redirect::back()->withErrors('Você só pode comprar no máximo ' . $prod->maximo . ' números');
        }

        // SEGURANCA: o valor e calculado aqui no servidor (antes vinha do campo "promo" do navegador)
        $price = $this->calcularValorCompra($prod, count($resutlNumbers));

        // Validando valor abaixo de 5.00 para gateway ASAAS
        if ($prod->gateway == 'asaas' && $price < 5) {
            return Redirect::back()->withErrors('Sua aposta deve ser de no mínimo R$ 5,00');
        }

        // Verifica se algum numero escolhido ja possui reserva (rifas com numeros na tabela raffles)
        $verifyReserved = DB::table('raffles')
            ->where('raffles.product_id', '=', $prod->id)
            ->whereIn('raffles.number', $resutlNumbers)
            ->where('raffles.status', '<>', 'Disponível')
            ->count();

        if ($verifyReserved > 0) {
            return Redirect::back()->withErrors('Acabaram de reservar um ou mais numeros escolhidos, por favor escolha outros números :)');
        }

        if ($numbersRifaAtualizados !== null) {
            $prod->saveNumbers($numbersRifaAtualizados);
        }

        $participanteId = DB::table('participant')->insertGetId([
            'customer_id' => $customer->id,
            'name' => $nomeParticipante,
            'telephone' => $request->telephone,
            'email' => '',
            'cpf' => '',
            'valor' => number_format($price, 2, ".", ""),
            'reservados' => count($resutlNumbers),
            'product_id' => $prod->id,
            'numbers' => json_encode($selecionados !== null ? $selecionados : $resutlNumbers),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);

        // Atualiza os numeros escolhidos para reservados (rifas com numeros na tabela raffles)
        DB::table('raffles')
            ->where('product_id', '=', $prod->id)
            ->whereIn('raffles.number', $resutlNumbers)
            ->where('raffles.status', '=', 'Disponível')
            ->update([
                'status' => 'Reservado',
                'participant_id' => $participanteId,
                'updated_at' => Carbon::now()
            ]);

        return [
            'participanteId' => $participanteId,
            'productId' => $prod->id,
            'price' => $price,
            'quantidade' => count($resutlNumbers),
            'afiliado' => $afiliado,
            'nome' => $nomeParticipante,
        ];
    }

    private function gerarCobrancaDaCompra(Request $request, array $reserva)
    {
        $prod = ModelsProduct::find($reserva['productId']);
        $participante = $reserva['participanteId'];
        $price = $reserva['price'];

        $resultPrice = number_format($price, 2, ",", ".");      // exibicao: 1.234,56
        $resultPricePIX = number_format($price, 2, ".", "");    // gateway:  1234.56

        $gateway = $this->gerarPIX($prod, $resultPricePIX, $request->email, $reserva['nome'], $participante, $request->cpf, $request->telephone);

        if (isset($gateway['error'])) {
            $this->desfazerReserva($participante);
            return back()->withErrors($gateway['error']);
        }

        if (empty($gateway['codePIXID'])) {
            $this->desfazerReserva($participante);
            return back()->withErrors('Não foi possível gerar o PIX. Tente novamente.');
        }

        $codePIXID = $gateway['codePIXID'];
        $codePIX = $gateway['codePIX'];
        $qrCode = $gateway['qrCode'];

        DB::table('payment_pix')->insert([
            'key_pix' => $codePIXID,
            'full_pix' => $codePIX,
            'status' => 'Pendente',
            'participant_id' => $participante,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);

        $order = Order::create([
            'key_pix' => $codePIXID,
            'participant_id' => $participante,
            'valor' => $price,
        ]);

        $product = DB::table('products')
            ->select('products.*', 'products_images.name as image')
            ->leftJoin('products_images', 'products.id', 'products_images.product_id')
            ->where('products.id', '=', $prod->id)
            ->first();

        $priceUnicFormat = str_replace(',', '.', $product->price);
        $percentage = 5;
        $percentagePriceUnic = ($percentage / 100) * $priceUnicFormat;
        $resultPriceUnic = $priceUnicFormat + $percentagePriceUnic + 0.50;

        if ($reserva['afiliado'] != null) {
            GanhosAfiliado::create([
                'product_id' => $prod->id,
                'participante_id' => $participante,
                'afiliado_id' => $reserva['afiliado']->afiliado_id,
                'valor' => $price * $prod->ganho_afiliado / 100,
                'pago' => false
            ]);
        }

        $dadosSave = [
            'participant_id' => $participante,
            'participant' => $reserva['nome'],
            'cpf' => $request->cpf,
            'email' => $request->email,
            'telephone' => $request->telephone,
            'price' => $resultPrice,
            'product' => $product->name,
            'productID' => $product->id,
            'drawdate' => $product->draw_date,
            'image' => $product->image,
            'PIX' => $resultPricePIX,
            'countRaffles' => $reserva['quantidade'],
            'priceUnic' => number_format($resultPriceUnic, 2, ".", ","),
            'codePIX' => $codePIX,
            'qrCode' => $qrCode,
            'codePIXID' => $codePIXID
        ];

        $order->dados = json_encode($dadosSave);
        $order->update();

        try {
            $this->mensagemWPPCompra($participante);
        } catch (\Throwable $th) {
            Log::error('Falha ao enviar WhatsApp da compra ' . $participante . ': ' . $th->getMessage());
        }

        // Link assinado: so o sistema consegue gerar o endereco da tela do PIX
        return redirect()->to(URL::signedRoute('checkoutManualy', ['participant_id' => $participante]));
    }

    /**
     * Valor total da compra, com a mesma regra de desconto que a tela mostra:
     * vale o desconto da ultima promocao (por ordem) cuja quantidade minima foi atingida.
     */
    private function calcularValorCompra(ModelsProduct $prod, $quantidade)
    {
        $valorNumero = (float) str_replace(",", ".", $prod->price);
        $total = $valorNumero * $quantidade;

        $percentDesconto = 0;
        foreach ($prod->promocoes() as $promo) {
            if ($promo->qtdNumeros > 0 && $quantidade >= (int) $promo->qtdNumeros) {
                $percentDesconto = (float) $promo->desconto;
            }
        }

        if ($percentDesconto > 0) {
            $total = $total - ($total * $percentDesconto / 100);
        }

        return round(max($total, 0), 2);
    }

    private function listaDeNumeros($texto)
    {
        $lista = [];
        foreach (explode(",", (string) $texto) as $item) {
            $item = trim($item);
            if ($item !== '') {
                $lista[] = $item;
            }
        }

        return $lista;
    }

    private function desfazerReserva($participanteId)
    {
        try {
            $participante = Participante::find($participanteId);
            if ($participante) {
                $participante->liberarReserva();
            }
        } catch (\Throwable $th) {
            Log::error('Erro ao desfazer reserva ' . $participanteId . ': ' . $th->getMessage());
        }
    }

    public function mensagemWPPCompra($participanteID)
    {
        $admin = User::find(1);
        $config = Environment::find(1);
        $participante = Participante::find($participanteID);
        $msgAdmin = AutoMessage::where('identificador', '=', 'compra-admin')->first();
        $msgCliente = AutoMessage::where('identificador', '=', 'compra-cliente')->first();
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

    public function gerarPIX(ModelsProduct $product, $resultPricePIX, $email, $name, $participante, $cpf, $telefone)
    {

        if($resultPricePIX == 0){
            $response['codePIXID'] = uniqid();
            $response['codePIX'] = 'gratis';
            $response['qrCode'] = '';

            return $response;
        }

        $codeKeyPIX = DB::table('consulting_environments')
            ->select('key_pix', 'token_asaas', 'paggue_client_key', 'paggue_client_secret')
            ->where('user_id', '=', 1)
            ->first();

        if ($product->gateway == 'mp') {
            \MercadoPago\SDK::setAccessToken($codeKeyPIX->key_pix);

            $resultPricePIX = str_replace(",", "", $resultPricePIX);

            $payment = new \MercadoPago\Payment();
            $payment->transaction_amount = $resultPricePIX;
            $payment->description = "Participação da ação " . $product->id . ' - ' . $product->name;
            $payment->payment_method_id = "pix";


            $payment->payer = array(
                "email" => $email ? $email : "teste.nienow@email.com",
                "first_name" => $name,
                "identification" => array(
                    "type" => "hash",
                    "number" => date('YmdHis')
                )
            );

            $payment->notification_url = env('APP_NAME') == 'local' ? '' : route('api.notificaoMP');
            $payment->external_reference = $participante;
            $payment->save();

            $object = (object) $payment;

            if (isset($object->error->message) == 'payer.email must be a valid email') {
                $response['error'] = 'Erro ao gerar o QR Code!';
                return $response;
            }

            if (isset($object->error->message) == 'Invalid user identification number') {
                $response['error'] = 'CPF inválido!';
                return $response;
            }

            $codePIXID = $object->id;
            $codePIX = $object->point_of_interaction->transaction_data->qr_code;
            $qrCode = $object->point_of_interaction->transaction_data->qr_code_base64;

            $response['codePIXID'] = $codePIXID;
            $response['codePIX'] = $codePIX;
            $response['qrCode'] = $qrCode;

            return $response;
        } else if ($product->gateway == 'asaas') {
            $idCliente = $this->getOrCreateClienteAsaas($name, $email, $cpf, $telefone);

            $minutosExpiracao = $product->expiracao;
            $dataDeExpiracao = date('Y-m-d H:i:s', strtotime("+" . $minutosExpiracao . " minutes"));

            $client = new \GuzzleHttp\Client([
                'headers' => [
                    'Content-Type' => 'application/json',
                    'access_token' => $codeKeyPIX->token_asaas
                ]
            ]);

            $pixURL = 'https://www.asaas.com/api/v3/payments';
            $requestPIX = $client->post($pixURL, [
                'form_params' => [
                    "externalReference" => $participante,
                    "description" => "Participação da ação " . $product->id . ' - ' . $product->name,
                    "customer" =>  $idCliente,
                    "billingType" =>  "PIX",
                    'dueDate' => date('Y-m-d', strtotime($dataDeExpiracao)),
                    "value" =>  $resultPricePIX,
                ]
            ]);

            $responsePIX = json_decode($requestPIX->getBody()->getContents());

            // Capturando QR Code gerado
            $QRURL = $pixURL . '/' . $responsePIX->id . '/pixQrCode';
            $reqQR = $client->get($QRURL);
            $respQR = json_decode($reqQR->getBody()->getContents());

            $response['codePIXID'] = $responsePIX->id;
            $response['codePIX'] = $respQR->payload;
            $response['qrCode'] = $respQR->encodedImage;

            return $response;
        } else if ($product->gateway == 'paggue') {
            include(app_path() . '/ThirdParty/phpqrcode/qrlib.php');

            $payload = array(
                "client_key"    => $codeKeyPIX->paggue_client_key,
                "client_secret" => $codeKeyPIX->paggue_client_secret
            );

            $paggue_curl = curl_init();

            curl_setopt_array($paggue_curl, array(
                CURLOPT_URL => 'https://ms.paggue.io/payments/api/auth/login',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_SSL_VERIFYPEER => 0,
                CURLOPT_SSL_VERIFYHOST => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => http_build_query($payload),
            ));

            $auth_response = json_decode(curl_exec($paggue_curl));

            curl_close($paggue_curl);

            $paggue_token = $auth_response->access_token;
            $paggue_company_id = $auth_response->user->companies[0]->id;

            // Faz a requisição do pagamento
            $payload = array(
                "payer_name" => $name,
                "amount" => (int) round(((float) $resultPricePIX) * 100),
                "external_id" => $participante,
                "description" => "Participação da ação " . $product->id . ' - ' . $product->name,
            );


            $headers = array();
            $headers[] = 'Accept: application/json';
            $headers[] = 'Authorization: Bearer ' . $paggue_token;
            $headers[] = 'Content-Type: application/json';
            $headers[] = 'X-Company-ID: ' . $paggue_company_id;

            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://ms.paggue.io/payments/api/billing_order',
                CURLOPT_RETURNTRANSFER => true,
                curl_setopt($curl, CURLOPT_POST, 1),
                CURLOPT_POSTFIELDS => json_encode($payload),
                CURLOPT_HTTPHEADER => $headers
            ));

            $payment_response = json_decode(curl_exec($curl));

            curl_close($curl);

            ob_start();
            \QRCode::png($payment_response->payment, null);
            $imageString = base64_encode(ob_get_contents());
            ob_end_clean();

            $response['codePIXID'] = $payment_response->hash;
            $response['codePIX'] = $payment_response->payment;
            $response['qrCode'] = $imageString;

            return $response;
        } else {
            $response['codePIXID'] = '';
            $response['codePIX'] = '';
            $response['qrCode'] = '';

            return $response;
        }
    }

    public function getOrCreateClienteAsaas($nome, $email, $cpf, $telefone)
    {
        $codeKeyPIX = DB::table('consulting_environments')
            ->select('key_pix', 'token_asaas')
            ->where('user_id', '=', 1)
            ->first();

        $clientURL = 'https://www.asaas.com/api/v3/customers';
        $client = new \GuzzleHttp\Client([
            'headers' => [
                'Content-Type' => 'application/json',
                'access_token' => $codeKeyPIX->token_asaas
            ]
        ]);

        $params = [
            'query' => [
                'cpfCnpj' => $cpf,
            ]
        ];

        $request = $client->get($clientURL, $params);

        $response = json_decode($request->getBody()->getContents());

        if (count($response->data) > 0) {
            $idCliente = $response->data[0]->id;
        } else {
            $requestClient = $client->post($clientURL, [
                'form_params' => [
                    "name" => $nome,
                    "email" => $email,
                    "cpfCnpj" =>  $cpf,
                    "mobilePhone" => $telefone
                ]
            ]);

            $responseCliente = json_decode($requestClient->getBody()->getContents());
            $idCliente = $responseCliente->id;
        }

        return $idCliente;
    }

    public function participants(Request $request)
    {
        //dd($request->product);

        $participants = Participant::select('name')
            ->join('raffles', 'participant.raffles_id', 'raffles.id')
            ->where('raffles.status', '=', 'Pago')
            ->where('raffles.product_id', '=', $request->product)
            ->inRandomOrder()
            ->count();

        //dd($teste->name);

        return $participants;
    }

    public function searchNumbers(Request $request)
    {
        $substr = substr($request->telephone, 0, 2);
        $ddd = '(' . $substr . ')';
        $substr1 = ' ' . substr($request->telephone, 2, 5) . '-';
        $substr2 = substr($request->telephone, 7);
        $resultTelephone = $ddd . $substr1 . $substr2;

        $numbersPaid = DB::table('participant')
            ->select('raffles.number', 'raffles.status', 'products.name')
            ->join('raffles', 'participant.raffles_id', 'raffles.id')
            ->join('products', 'products.id', 'raffles.product_id')
            ->where('participant.telephone', '=', $resultTelephone)
            ->where('products.id', '=', $request->productID)
            ->where('raffles.status', '=', 'Pago')
            ->get();

        return $numbersPaid;
    }

    public function searchPIX(Request $request)
    {
        $substr = substr($request->telephone, 0, 2);
        $ddd = '(' . $substr . ')';
        $substr1 = ' ' . substr($request->telephone, 2, 5) . '-';
        $substr2 = substr($request->telephone, 7);
        $resultTelephone = $ddd . $substr1 . $substr2;

        $pix = DB::table('participant')
            ->select('raffles.number', 'key_pix')
            ->leftJoin('payment_pix', 'participant.id', 'payment_pix.participant_id')
            ->join('raffles', 'participant.raffles_id', 'raffles.id')
            ->where('participant.telephone', '=', $resultTelephone)
            ->where('participant.product_id', '=', $request->product)
            ->get();

        return $pix;
    }

    public function callbackPaymentMercadoPago(Request $request)
    {
        // SEGURANCA: antes esta rota marcava como pago qualquer id recebido, sem consultar o
        // Mercado Pago. Agora ela apenas repassa o id para a verificacao oficial.
        return $this->notificacoesMP($request);
    }

    public function ganhadores()
    {

        // $winners = DB::table('products')
        //     ->select('*')
        //     ->where('products.status', '=', 'Finalizado')
        //     ->where('products.visible', '=', 1)
        //     ->orderBy('products.id', 'desc')
        //     ->get();

        $winners = ModelsProduct::where('status', '=', 'Finalizado')->get();

        return view('ganhadores', [
            'winners' => $winners
        ]);
    }

    public function notificacoesMP(Request $request)
    {
        // O Mercado Pago avisa por ?id=...&topic=payment (IPN) ou por {"type":"payment","data":{"id":...}}
        $tipo = $request->input('type', $request->input('topic'));
        $id = $request->input('data.id', $request->input('data_id', $request->input('id')));

        if ($tipo !== null && $tipo !== 'payment') {
            return response('OK', 200)->header('Content-Type', 'text/plain');
        }

        try {
            Pagamentos::verificarMercadoPago($id);
        } catch (\Throwable $th) {
            Log::error('Webhook Mercado Pago: ' . $th->getMessage());
            return response('Erro', 500)->header('Content-Type', 'text/plain');
        }

        return response('OK', 200)->header('Content-Type', 'text/plain');
    }

    public function rankingAdmin(Request $request)
    {
        $rifa = ModelsProduct::find($request->id);

        $data = [
            'rifa' => $rifa,
            'ranking' => $rifa->rankingAdmin()
        ];

        $response['html'] = view('ranking-admin', $data)->render();

        return $response;
    }

    public function definirGanhador(Request $request)
    {
        $rifa = ModelsProduct::find($request->id);

        $data = [
            'rifa' => $rifa,
        ];

        $response['html'] = view('layouts.definir-ganhador', $data)->render();

        return $response;
    }

    public function verGanhadores(Request $request)
    {
        $rifa = ModelsProduct::find($request->id);

        $data = [
            'rifa' => $rifa,
        ];

        $response['html'] = view('layouts.ver-ganhadores', $data)->render();

        return $response;
    }

    public function informarGanhadores(Request $request)
    {
        try {
            $rifa = ModelsProduct::find($request->idRifa);
            $premios = $rifa->premios();
            $ganhadores = [];
            

            if($rifa->modo_de_jogo == 'numeros'){
                foreach ($request->cotas as $key => $cota) {
                    foreach ($rifa->participantes() as $participante) {
                        $numbersParticipante = $participante->numbers();
                        $find = array_search($cota, $numbersParticipante);
                        if(is_int($find)){
                            array_push($ganhadores, $participante->name);
                            $premios->where('ordem', '=', $key)->first()->update([
                                'ganhador' => $participante->name,
                                'telefone' => $participante->telephone,
                                'cota' => $cota,
                                'participant_id' => $participante->id
                            ]);
                            break;
                        }
                    }
                }
            }
            else{
                foreach ($request->cotas as $key => $cota) {
                    $numero = $rifa->numbers()->where('number', '=', $cota)->first();
                    $participante = $numero->participante();
                    $premios->where('ordem', '=', $key)->first()->update([
                        'ganhador' => $participante->name,
                        'telefone' => $participante->telephone,
                        'cota' => $cota
                    ]);
                }
            }
            

            return redirect()->back()->with(['success' => 'Ganhadores ('.implode(',', $ganhadores).') informados com sucesso!', 'sorteio' => $request->idRifa]);
        } catch (\Throwable $th) {
            return redirect()->back()->withErrors('Erro ao informar ganhadores');
        }
    }

    public function notificacoesPaggue(Request $request)
    {
        // SEGURANCA: o webhook so e aceito com a assinatura HMAC valida da Paggue.
        // (O corpo da requisicao nao e mais gravado em public/webhook_paggue.json.)
        if (!Pagamentos::assinaturaPaggueValida($request)) {
            return response('Assinatura invalida', 401)->header('Content-Type', 'text/plain');
        }

        if ((string) $request->input('status') !== '1') {
            return response('OK', 200)->header('Content-Type', 'text/plain');
        }

        $pix = DB::table('payment_pix')->where('key_pix', '=', (string) $request->input('hash'))->first();

        if (!$pix || (string) $pix->participant_id !== (string) $request->input('external_id')) {
            Log::error('Webhook Paggue: pagamento ' . $request->input('hash') . ' nao corresponde a nenhuma compra.');
            return response('OK', 200)->header('Content-Type', 'text/plain');
        }

        $participante = Participante::find($pix->participant_id);
        if ($participante && $request->input('amount') !== null) {
            $valorCompra = (float) str_replace(',', '', (string) $participante->valor);
            if (abs($valorCompra * 100 - (float) $request->input('amount')) > 1) {
                Log::error('Webhook Paggue: valor pago diferente do valor da compra ' . $participante->id);
            }
        }

        Pagamentos::confirmar($pix);

        return response('OK', 200)->header('Content-Type', 'text/plain');
    }
}
