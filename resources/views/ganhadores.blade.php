@extends('layouts.app')

<link rel="manifest" href="/manifest.json">
<script type="text/javascript" src="sw.js"></script>

<style>
    body {
        /*background-color: #181818 !important;*/
        /*background-color: #132439 !important;*/
        background-color: #000 !important;
        background-size: cover;
        width: 100%;
        background-position: bottom;
        background-repeat: no-repeat;
        padding-top:40px;


        /*background-image: url('images/background-asfalto.jpg');*/
    }
</style>


@section('title', 'Page Title')


@section('sidebar')

@stop



@section('content')
    <div class="container rounded-top-5" id="" style="background-color:#e3e3e3;padding-bottom:75px;min-height: 100vh; max-width: 600px;" >
        <div style="padding-top: 15px; padding-left: 20px;"><i class="icone bi bi-blockquote-right"></i><span style="font-family: Montserrat,sans-serif; font-weight: 600; font-size: 20px; padding-left: 10px;">Termos de Uso</span></a></li></div>
        <div style="padding-top: 15px; padding-left: 20px;"><p style="font-family: Montserrat,sans-serif !important;">
        <strong>Da Participação nas Rifas</strong><br>
        Toda e qualquer pessoa maior de 18 anos pode participar de ações disponíveis.
        </p></div>
        <div style="padding-top: 15px; padding-left: 20px;"><p style="font-family: Montserrat,sans-serif !important;">
        <strong>Adiamento da Rifa</strong><br>
        Caso a Rifa não tenha mais de 100% dos bilhetes Pagos Pode ser Adiado o sorteio ate que o mesmo seja completamente vendido.
        </p></div>
        <div style="padding-top: 15px; padding-left: 20px;"><p style="font-family: Montserrat,sans-serif !important;">
        <strong>Da Definição do Ganhador</strong><br>
        O ganhador do sorteio será sempre o participante que tiver seu nome e celular gravados no bilhete premiado. No caso de pagamento em duplicidade, ou seja, em casos raros onde duas pessoas clicam no mesmo bilhete simultaneamente o ganhador do sorteio será o participante que tiver o nome e celular gravados no bilhete no momento do sorteio. O participante que, no caso de compra em duplicidade não tiver o nome gravado no bilhete deverá solicitar reembolso do valor pago pelo bilhete em questão.
        </p></div>
        <div style="padding-top: 15px; padding-left: 20px;"><p style="font-family: Montserrat,sans-serif !important;">
        <strong>Do Cancelamento da Rifa</strong><br>
        O site poderá, sem aviso prévio, cancelar qualquer ação que viole um ou mais termos aqui dispostos.
        </p></div>
        <div style="padding-top: 15px; padding-left: 20px;"><p style="font-family: Montserrat,sans-serif !important;">
        <strong>Do Reembolso</strong><br>
        O reembolso de bilhetes ocorre exclusivamente em casos de cancelamento da ação, ou seja, o criador da ação ou O site não se obriga a reembolsar participantes que adquirirem bilhetes presencialmente ou on-line caso a ação não tenha sido cancelada.
        </p></div>
        <div style="padding-top: 15px; padding-left: 20px;"><p style="font-family: Montserrat,sans-serif !important;">
        <strong>Das Proibições</strong><br>
        É completamente vedado o uso de perfis falsos visando criar ações sem a genuína intenção de realizá-las seguindo os termos aqui dispostos.
        </p></div>
        
        <div style="padding-top: 15px; padding-left: 20px;"><p style="font-family: Montserrat,sans-serif !important;">
        É completamente vedado a participação do organizador em sua própria ação.O site pode alterar a qualquer momento estes termos visando melhorar a qualidade do serviço oferecido.
        </p></div>
        <div style="padding-top: 15px; padding-left: 20px;"><p style="font-family: Montserrat,sans-serif !important;">
        O site informará a todos os usuários sempre que ocorrer uma alteração nos termos de uso e o usuário deverá aceitar as mudanças para continuar utilizando o site.
        </p></div>
    </div>
    

@stop