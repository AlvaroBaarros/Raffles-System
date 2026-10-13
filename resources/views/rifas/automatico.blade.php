<style>
    .col-6 {
    width: 31.5% !important;
    }
    span.badge.bg-success.text-popular {
    font-size: 12px !important;
    font-weight: 400;
    border-radius: 0px 0px 10px 10px;
    background-color: #198754 !important;
}
}
.text-popular {
    margin-right: 3px !important;
}
    .btn-auto.btn-add-qtd.light.btn-popular {
    background-color: #34a772 !important;
    border: 2px solid #198754;
    }
    .btn-auto.btn-add-qtd.light {
    background-color: #181818 !important;
    }
    .body-compra-auto {
        border: none;
        border-radius: 10px;
        margin-top: 20px;
        padding: 0px;
    }

    .body-compra-auto.dark {
        background: #fff;
    }

    .title-compra-auto h5 {
        color: #000;
    }

    .title-compra-auto span {
        color: #000;
    }

    .title-compra-auto.dark h5 {
        color: #fff;
    }

    .title-compra-auto.dark span {
        color: #fff;
    }

    .btn-add-qtd {
        color: #000;
        background-color: #fff;
        border-radius: 0px;
        padding: 20px 10px 10px 10px;
        margin: 2px;
        border: 1px solid;
        width: 100%;
        
        max-width: 300px;
    }

    .btn-add-qtd.dark {
        background: rgba(0, 0, 0, .1) !important;
        border-color: rgba(0, 0, 0, .1) !important;
        color: #fff !important;
    }
    .btn-auto{
            background-color: #161616 !important;
    }
    .btn-popular {
    background-color: #34A772 !important;
    border: solid 2px #198754 !important;
    
}
</style>

<div class="card" style="border: none;border-radius: 10px;background-color: transparent;margin-bottom: 50px!important; margin-top: -35px;">
    <div class="card-body body-compra-auto {{ $config->tema }}" style="">
        <div class="" style="">
            <?php $resultNumber = $totalPago; ?>
        </div>
        <div class="title-compra-auto {{ $config->tema }}" style="margin-top: 45px;margin-bottom: 5px; background-color: #fff !important; text-align: center; font-size: 14px; padding: 10px 15px; border-radius: 10px; box-shadow: 0 4px 3px rgba(0,0,0,.08);">
            <span style="color: #7b7f83 !important; font-family: Montserrat,sans-serif !important; line-height: 1.2;">Quanto mais títulos comprar, maiores são as suas chances de ganhar!</span><br>
        </div>


        <div class="row d-flex justify-content-center">
            @foreach ($productModel->comprasAuto()->where('qtd', '>', 0) as $compra)
                <div class="col-6" style="padding: 1px !important;">
                    <div class="btn-auto btn-add-qtd {{ $config->tema }} {{ $compra->popular ? 'btn-popular' : '' }}" onclick="addQtd('{{ $compra->qtd }}')">
                        @if ($compra->popular)
                            <div style="text-align: center; margin-top: -21px;">
                                <button style="font-size: 9.5px; color: #fff; background-color: #198754; border: none; border-radius: 0px 0px 5px 5px; font-family: Montserrat,sans-serif !important;" >MAIS POPULAR</button>
                           </div> 
                        @endif
                        <span style="font-weight: 600; font-size: 28px; color: #fff !important; font-family: Montserrat,sans-serif !important;">+  {{ $compra->qtd < 10 ? '0' : '' }}{{ $compra->qtd }}</span><br>
                        <span style="font-size: 12px; color: #DEDEDE !important; font-family: Montserrat,sans-serif !important;">SELECIONAR</span>
                        
                    </div>
                </div>
            @endforeach
        </div>

        {{-- <div style="display: flex;justify-content: center;position:relative">
            <div class="btn-auto btn-add-qtd {{ $config->tema }}" onclick="addQtd('5')">
                + 5<br>
                <span style="font-size: 14px;font-weight: bold;">SELECIONAR</span>
            </div>
            <div class="btn-auto btn-add-qtd {{ $config->tema }}" onclick="addQtd('10')">
                + 10<br>
                <span style="font-size: 14px;font-weight: bold;">SELECIONAR</span>

            </div>
        </div>
        <div class="" style="display: flex;justify-content: center;">
            <div class="btn-auto btn-add-qtd {{ $config->tema }}" onclick="addQtd('30')">
                + 30<br>
                <span style="font-size: 14px;font-weight: bold;">SELECIONAR</span>
            </div>
            <div class="btn-auto btn-popular btn-add-qtd {{ $config->tema }}" onclick="addQtd('50')">
                + 50<br>
                <span style="font-size: 14px;font-weight: bold;">SELECIONAR</span>
                <span class="badge bg-success text-popular">MAIS POPULAR</span>
            </div>
        </div>
        <div class="" style="display: flex;justify-content: center;">
            <div class="btn-auto btn-add-qtd {{ $config->tema }}" onclick="addQtd('100')">
                + 100<br>
                <span style="font-size: 14px;font-weight: bold;">SELECIONAR</span>
            </div>
            <div class="btn-auto btn-add-qtd {{ $config->tema }}" onclick="addQtd('500')">
                + 500<br>
                <span style="font-size: 14px;font-weight: bold;">SELECIONAR</span>
            </div>
        </div> --}}
        <div class="" style="margin-top: 20px;margin-bottom: 20px;text-align: center;">
            <div class="amount">
                <div class="form-group"
                    style="margin-bottom: 0;display: flex;justify-content: center;flex-direction: inherit;align-items: center; background-color: #fff !important; padding: 7px 0px; border-radius: 10px; box-shadow: 0 4px 3px rgba(0,0,0,.08); margin-top: -15px;">
                    <button class="btn-amount-qtd" onclick="addQtd('-')"
                        style="color: #7b7f83;margin-right: 5px; border: solid 1.5px #7b7f83; font-size: 20px; width: 22px; height: 22px; border-radius: 100px;">-</button>
                    <input type="number"
                        style="text-align: center;color: #181818; width: 233px; border-radius: 10px; border: solid 1px #c7c7c7 !important; font-family: Montserrat,sans-serif !important; font-weight: 600; color: #7b7f83;"
                        id="numbersA" value="{{ $productModel->minimo }}" min="{{ $productModel->minimo }}"
                        max="{{ $productModel->maximo }}" onblur="numerosAleatorio();" onkeyup="numerosAleatorio()"
                        class="form-control" placeholder="Quantidade de cotas">
                    <button class="btn-amount-qtd" onclick="addQtd('+')"
                        style="color: #fff;margin-left: 5px; border: solid 1.5px #181818; font-size: 20px; width: 22px; height: 22px; border-radius: 100px; background-color: #181818;">+</button>
                </div>
                <button type="button" 
                    style="color: #fff;border: none;width: 100%;margin-top: 10px;font-weight: 600; font-family: Montserrat,sans-serif !important; background-color: #198754 !important; border-radius: 7px; padding: 13px 10px; margin-bottom: -40px;"
                    onclick="validarQtd()"><i class="bi bi-check2-circle"></i>&nbsp;Quero Participar<span id="numberSelectedTotalHome" style="color: #fff;float:right; font-weight: 600; font-family: Montserrat,sans-serif !important;"></span></button>
            </div>
        </div>
    </div>
</div>
@if (env('REQUIRED_DESCRIPTION'))
    <div class="" style="margin-top: -20px !important;">
    </div>
    <div class="card mt-3 desc {{ $config->tema }}" style="font-size: 14px !important; font-family: Montserrat,sans-serif !important; font-weight: 400; box-shadow: 0 4px 3px rgba(0,0,0,.08);">
        <p>
            {!! $productDescription !!}
        </p>
    </div>
@endif
{{-- Ranking de compradores (WDM) --}}
@if (count($ranking) > 0)
    <div class="card" style="border: none;border-radius: 10px;background-color: transparent;">
        <div class="card-body body-ranking {{ $config->tema }}" style="padding: 0px !important; background-color: transparent;">
            <div class="" style="">
                <?php $resultNumber = $totalPago; ?>
            </div>
            <div class="title-ranking {{ $config->tema }}" style="margin-bottom: 10px; background-color: transparent; margin-top: -5px;">
                <span style="font-size: 18px; font-family: Montserrat,sans-serif; font-weight: 600;">🏆 Top Compradores</span>
            </div>


            <div class="row" style=" background-color: transparent; padding: 0px 10px;">
                <div style="background-color: #fff; display: flex;justify-content:center;position:relative; border-radius: 10px; box-shadow: 0 4px 3px rgba(0,0,0,.08); padding: 0px 0px 10px 0px;">@foreach ($ranking as $key => $rk)
                    <div class="btn-auto item-ranking" style="background-color: #fff !important; font-family: Montserrat,sans-serif; border: solid 1px #198754 !important; width: 30% !important; font-size: 16px !important;">
                        {{ $key + 1 }}º {{ $productModel->medalhaRanking($key) }}<br>
                        <span style="font-size: 13px; line-height: 1.2; color: #898989;">{{ $rk->name }}</span>
                        <br>
                        <span style="font-size: 12px;">
                            {{ $rk->totalReservas }}</span><span style="font-size: 12px;"> Cotas</span>
                    </div>
                @endforeach
                </div>
            </div>
        </div>
    </div>
@endif

<div class="title-ranking {{ $config->tema }}" style="margin-bottom: 10px; background-color: transparent; margin-top: 5px;">
                <span style="font-size: 18px; font-family: Montserrat,sans-serif; font-weight: 600;">🔥 Cotas premiadas</span><span style="font-family: Montserrat,sans-serif; font-size: 14px; font-weight: 300; color: #5a5a5a;"> Achou ganhou!</span>
            </div>
<div style="background-color: #fff !important; padding: 10px 15px; border-radius: 10px; box-shadow: 0 4px 3px rgba(0,0,0,.08);">
    @foreach ($productModel->premios()->where('descricao', '!=', '') as $premio)
        
        <span >
            <span class="col-md-12 text-center">
                <button style="background-color: #157347; border-radius: 5px; padding: 5px 7px; color: #fff; font-size: 12px; font-family: Montserrat,sans-serif; margin-top: 5px; border: none !important;">{{ $premio->descricao }}</button>
            </span>
        </span>
    @endforeach

</div>
<div class="item socials d-flex align-items-center" style="margin-top: 10px; padding-bottom: 20px;">
  <span class="item socials d-flex align-items-center">
    <div class="a2a_kit a2a_kit_size_32 a2a_default_style">                            
    <a class="a2a_button_facebook"></a>
    <a class="a2a_button_telegram"></a>
    <a class="a2a_button_twitter"></a>
    <a class="a2a_button_whatsapp"></a>
    </div>
    <script async src="https://static.addtoany.com/menu/page.js"></script>
 </span>
</div>
