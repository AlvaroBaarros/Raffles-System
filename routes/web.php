<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// SUBDOMAINs para os consultores

use App\Http\Controllers\AfiliadoController;
use App\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['check'])->group(function () {
    Route::get('login', 'Auth\LoginController@showLoginForm')->name('login');
    Route::post('login', 'Auth\LoginController@login');
    Route::post('logout', 'Auth\LoginController@logout')->name('logout');

    Route::get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm');
    Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->middleware('throttle:5,1');
    Route::get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm');
    Route::post('password/reset', 'Auth\ResetPasswordController@reset');
    Route::get('ganhadores', 'ProductController@ganhadores')->name('ganhadores');


    // Rotas area de afiliados
    Route::prefix('area-afiliado')->group(function () {
        Route::get('/', [AfiliadoController::class, 'index'])->name('afiliado.home');
        Route::get('/rifas-ativas', [AfiliadoController::class, 'rifasAtivas'])->name('afiliado.rifas');
        Route::get('/cadastro', [AfiliadoController::class, 'cadastro'])->name('afiliado.cadastro');
        Route::post('/novo-cadastro', [AfiliadoController::class, 'novo'])->name('afiliado.novo')->middleware('throttle:10,1');
        Route::post('/login', [AfiliadoController::class, 'login'])->name('afiliado.login')->middleware('throttle:10,1');
        Route::get('/logout', [AfiliadoController::class, 'logout'])->name('afiliado.logout');

        Route::group(['middleware' => ['auth', 'isAfiliado']], function () {
            Route::get('/pagamentos', [AfiliadoController::class, 'pagamentos'])->name('afiliado.pagamentos');
            Route::get('/afiliar-se/{idRifa}', [AfiliadoController::class, 'afiliar'])->name('afiliado.afiliarSe');
            Route::get('/solicitar-saque', [AfiliadoController::class, 'solicitarSaque'])->name('afiliado.solicitarSaque');
        });
    });

    Route::group(['middleware' => ['auth', 'isAdmin']], function () {
        Route::get('home', 'HomeAdminController@index')->name('home');
        Route::get('adicionar-sorteio', 'ProductAdminController@index')->name('adminProduct');
        Route::post('add-sorteio', 'ProductAdminController@addProduct')->name('addProduct');
        Route::post('duplicar-sorteio', 'ProductAdminController@duplicar')->name('duplicarProduct');
        Route::put('update/{id}', 'MySweepstakesController@update')->name('update');
        Route::delete('destroy', 'ProductAdminController@destroy')->name('destroy');
        Route::post('agendar-sorteio', 'ProductAdminController@drawDate')->name('drawDate');
        Route::post('previsao-sorteio', 'ProductAdminController@drawPrediction')->name('drawPrediction');
        Route::get('meus-sorteios', 'MySweepstakesController@index')->name('mySweepstakes');
        Route::any('liberar-reservas', 'MySweepstakesController@releaseReservedRafflesNumbers')->name('releaseReservedRafflesNumbers');
        Route::any('pagar-reservas', 'MySweepstakesController@pagarReservas')->name('pagarReservas');
        Route::any('reservar-numeros', 'MySweepstakesController@reservarNumeros')->name('reservarNumeros');
        Route::get('carrega-sorteio', 'MySweepstakesController@getRaffles')->name('getRaffles');
        Route::post('altera-sorteio', 'MySweepstakesController@editRaffles')->name('editRaffles');
        Route::post('altera-produto', 'ProductAdminController@alterProduct')->name('alterProduct');
        Route::get('perfil', 'MySweepstakesController@profile')->name('profile');
        Route::post('perfil', 'MySweepstakesController@updateProfile')->name('updateProfile');
        Route::post('alterar-logo', 'ProductAdminController@alterarLogo')->name('alterarLogo');
        Route::post('pixel', 'MySweepstakesController@pixel')->name('pixel');
        Route::post('remover-reservas', 'MySweepstakesController@removeReserved')->name('removeReserved');
        Route::post('altera-status-produto', 'ProductAdminController@alterStatusProduct')->name('alterStatusProduct');
        Route::post('altera-winner-produto', 'ProductAdminController@alterWinnerProduct')->name('alterWinnerProduct');
        Route::post('altera-tipo-produto', 'ProductAdminController@alterTypeRafflesProduct')->name('alterTypeRafflesProduct');
        Route::post('favoritar-produto', 'ProductAdminController@favoritarRifa')->name('favoritarRifa');
        Route::patch('edit-product/{id}', 'MySweepstakesController@updateProduct')->name('updateProduct');
        Route::post('add-foto-rifa', 'ProductAdminController@addFoto')->name('addFoto');

        Route::post('/excluir-foto', 'MySweepstakesController@excluirFoto')->name('excluirFoto');
        Route::get('imprimir-resumo-compra/{id}', 'MySweepstakesController@imprimirResumoCompra')->name('imprimirResumoCompra');



        //  WDW
        Route::post('/ranking-admin', 'ProductController@rankingAdmin')->name('ranking.admin');
        Route::post('/definir-ganhador', 'ProductController@definirGanhador')->name('definirGanhador');
        Route::post('/informar-ganhadores', 'ProductController@informarGanhadores')->name('informarGanhadores');
        Route::post('/ver-ganhadores', 'ProductController@verGanhadores')->name('verGanhadores');

        // WDM - Compras
        Route::get('/compras/{idRifa}', 'MySweepstakesController@compras')->name('rifa.compras');
        Route::put('/compras/{idRifa}', 'MySweepstakesController@comprasBusca')->name('rifa.comprasBusca');
        Route::post('/liberar-todas-reservas', 'MySweepstakesController@liberarTodasReservas')->name('compras.liberarReservas');
        Route::post('random-numbers', 'MySweepstakesController@randomNumbers')->name('compras.randomNumbers');
        Route::post('/criar-compra', 'MySweepstakesController@criarCompra')->name('compras.criar');
        Route::post('/build-modal-detalhes-compra', 'MySweepstakesController@detalhesCompra')->name('compras.detalhes');

        // WDM - Whatsapp mensagens
        Route::get('/wpp-mensagens', 'HomeAdminController@wpp')->name('wpp.index');
        Route::post('/wpp-mensagens/salvar', 'HomeAdminController@wppSalvar')->name('wpp.salvar');

        // Ganhadores
        Route::get('/admin-ganhadores', 'MySweepstakesController@ganhadores')->name('painel.ganhadores');
        Route::post('/add-foto-ganhador', 'MySweepstakesController@addFotoGanhador')->name('ganhador.addFoto');

        // WDM - Tutoriais
        Route::get('/tutoriais', 'MySweepstakesController@tutoriais')->name('tutoriais');
        Route::get('/tutoriais/cadastro', 'MySweepstakesController@cadastroVideos');
        Route::post('/tutoriais/cadastro', 'MySweepstakesController@salvarVideo')->name('dev.salvarVideo');
        Route::get('/tutoriais/excluir-video/{id}', 'MySweepstakesController@excluirVideo')->name('dev.excluirVideo');

        // WDM - Relatorios Painel Home
        Route::get('/resumo-lucro', 'MySweepstakesController@resumoLucro')->name('resumo.lucro');
        Route::get('/resumo-rifas-ativas', 'MySweepstakesController@resumoRifasAtivas')->name('resumo.rifasAtivas');
        Route::get('/resumo-pendentes', 'MySweepstakesController@resumoPendentes')->name('resumo.pendentes');
        Route::post('/resumo-pendentes-search', 'MySweepstakesController@resumoPendentesSearc')->name('resumo.pendentesSearch');
        Route::get('/resumo-ranking', 'MySweepstakesController@resumoRanking')->name('resumo.ranking');
        Route::post('/resumo-ranking/selected', 'MySweepstakesController@resumoRankingSelect')->name('resumo.rankingSelect');

        Route::get('lista-afiliados', 'MySweepstakesController@listaAfiliados')->name('afiliados');
        Route::get('solicitacao-pagamento', 'MySweepstakesController@solicitacaoPgto')->name('painel.solicitacaoAfiliados');
        Route::get('confirmar-pgto-afiliado/{solicitacaoId}', 'MySweepstakesController@confirmarPgtoAfiliado')->name('painel.confirmarPgtoAfiliado');
        Route::get('excluir-afiliado/{id}', 'MySweepstakesController@excluirAfiliado')->name('painel.excluirAfiliado');

        // Send MSG API Criar Whats
        Route::post('/api-wpp/send-message', 'MySweepstakesController@sendMessageAPIWhats')->name('apiWhats.sendMessage');
    });

    // Link de pagamento enviado por WhatsApp: precisa ser um link assinado (nao da para trocar o id)
    Route::get('/pagar-reserva/{id}', 'CheckoutController@pagarReserva')->name('pagarReserva')->middleware('signed');

    // Consultas publicas por telefone: limitadas para impedir varredura de dados (LGPD)
    Route::post('/get-customer', 'CheckoutController@getCustomer')->name('getCustomer')->middleware('throttle:15,1');

    Route::get('/', 'ProductController@index')->name('inicio');
    Route::get('/sorteios', 'ProductController@sorteios')->name('sorteios');
    Route::get('sorteio/{id}/{tokenAfiliado?}', 'ProductController@product')->name('product');
    Route::get('resumo-rifa/{id}', 'MySweepstakesController@resumoRifa')->name('resumoRifa');
    Route::get('resumo-rifa-pdf/{id}', 'MySweepstakesController@resumoPDF')->name('resumoRifaPDF')->middleware('throttle:20,1');
    Route::post('buscar-numeros', 'ProductController@getRaffles')->name('getRafflesAjax');
    // A rota antiga 'cadastra-participante' apontava para um metodo inexistente (erro 500).
    // Agora as duas usam o mesmo fluxo de compra.
    Route::post('cadastra-participante', 'ProductController@bookProductManualy')->name('bookProduct')->middleware('throttle:15,1');
    Route::post('cadastra-participante1', 'ProductController@bookProductManualy')->name('bookProductManualy')->middleware('throttle:15,1');
    Route::get('regulamento', 'RegulationController@index')->name('regulation');
    Route::post('participantes', 'ProductController@participants')->name('participants');
    // SEGURANCA: removidas 'pagamento-pix' e 'pagamento-credito' (nao eram usadas pelas telas).
    // A primeira deixava qualquer pessoa trocar o codigo PIX de qualquer compra e a segunda
    // aceitava o valor do cartao enviado pelo navegador.
    Route::post('pesquisa-numeros', 'ProductController@searchNumbers')->name('searchNumbers')->middleware('throttle:15,1');
    Route::post('pesquisa-pix', 'ProductController@searchPIX')->name('searchPIX')->middleware('throttle:15,1');
    Route::get('checkout', 'CheckoutController@index')->name('checkout');
    // Tela do PIX: so abre com link assinado gerado pelo proprio sistema
    Route::get('checkout-manualy', 'CheckoutController@checkoutManualy')->name('checkoutManualy')->middleware('signed');
    Route::any('checkout-success/{id}', 'CheckoutController@findPixStatus')->name('findPixStatus')->middleware('throttle:60,1');
    Route::any('checkout-visualizar-pedidos/{id}', 'CheckoutController@findPedidoStatus')->name('findPedidoStatus')->middleware('throttle:15,1');
    Route::post('consultar-reserva', 'CheckoutController@consultingReservation')->name('consultingReservation')->middleware('throttle:15,1');
    Route::get('reserva/{productID}/{telephone}', 'CheckoutController@consultingReservationTelephone')->name('consultingReservationTelephone')->middleware('throttle:15,1');
    Route::post('minhas-reservas/', 'CheckoutController@minhasReservas')->name('minhasReservas')->middleware('throttle:15,1');

    Route::get('terms-of-use', 'TermsOfUse@index')->name('terms');

    Route::post('/random-participant', 'ProductController@randomParticipant')->name('randomParticipant');

    // SEGURANCA: removidas as rotas publicas de manutencao/teste:
    // /reset-pass, /pull-wdm, /migrate, /update, /update-footer, /refresh-raffle/{id},
    // /refresh-wdm, /refresh-only-raffle/{id}, /atualizar-rifa/{id}, /refresh-participante/{id},
    // /check-payment, /update-raffle-wdm, /leo-update, /teste123 e /checkout-pixsuccess.
});
