<style>
    .body-ranking {
        background-color: #fff;
        border: none;
        border-radius: 10px;
        margin-top: 10px;
    }

    .body-ranking.dark {
        background: #222222;
    }

    .title-ranking h5 {
        color: #000;
    }

    .title-ranking span {
        color: #000;
    }

    .title-ranking.dark h5 {
        color: #fff;
    }

    .title-ranking.dark span {
        color: #fff;
    }

    .body-promo {
        background-color: #fff;
        border: none;
        border-radius: 10px;
        margin-top: 20px;
    }

    .body-promo.dark {
        background: #222222;
    }

    .title-promo.dark {
        color: #fff !important;
    }
</style>



{{-- Promoções --}}
@if ($productModel->promocoes()->where('qtdNumeros', '>', 0)->count() > 0)
    <div class="" style="">
        <span class="mt-1 title-promo {{ $config->tema }}" style="font-size: 18px; font-family: Montserrat,sans-serif !important; font-weight: 600;">
            📣 Promoção
            <span class="text-muted title-promo {{ $config->tema }}" style="font-family: Montserrat,sans-serif !important; font-size: 14px; font-weight: 300; color: #5a5a5a;">Compre mais
                barato!</span>
        </span>
    </div>
    <div class="card" style="border: none;border-radius: 10px;background-color: transparent; margin-top: -15px; margin-bottom: 30px;">
        <div class="card-body body-promo {{ $config->tema }}" style="padding: 10px 5px 5px 5px !important; border-radius: 10px !important; box-shadow: 0 4px 3px rgba(0,0,0,.08); margin-bottom: -20px !important;">
            <div class="row">
                @foreach ($productModel->promocoes()->where('qtdNumeros', '>', 0) as $promo)
                    @if ($productModel->type_raffles == 'manual')
                        <span class="col-6" style="margin-bottom: 8px;"  onclick="infoPromo()">
                            <button class="bg-success" style="color: #fff;text-align: center;border-radius:6px; font-family: Montserrat,sans-serif !important; font-size: 13px !important; border: none; width: 140px;">
                                    {{ $promo->qtdNumeros }} por R$
                                    {{ $promo->valorFormatted() }}
                            </button>
                        </span
                    @else
                        <span class="col-6" style="margin-bottom: 8px;"
                            onclick="addQtd('{{ $promo->qtdNumeros }}', '{{ $promo->valorFormatted() }}')">
                            <button class="bg-success" style="color: #fff;text-align: center;border-radius:6px; font-family: Montserrat,sans-serif !important; font-size: 13px !important; border: none; width: 140px;">
                                    {{ $promo->qtdNumeros }} por R$
                                    {{ $promo->valorFormatted() }}
                            </button>
                        </span>
                    @endif
                @endforeach
            </div>
        </div>
    </div>
@endif

{{-- Ver meus premios e Parcial --}}
<div class="mb-3" style="margin-bottom: -45px !important; margin-top: -20px;">

    <div class="row mt-2">
        
        <div class="{{ env('APP_URL') == 'rifasonline.link' ? 'col-md-12 col-12' : 'col-md-6 col-6' }}">
            
        </div>
        <div class="{{ env('APP_URL') == 'rifasonline.link' ? 'col-md-12 col-12 mt-2' : 'col-md-6 col-6' }}">


            
        </div>
    </div>

    @if ($productModel->parcial)
        <div class="card"
            style="border: none;border-radius: 10px;background-color: transparent; margin-top: 25px !important; margin-bottom: -20px !important; padding-bottom: 10px;">
            <div class="card-body body-compra-auto {{ $config->tema }}" style="">
                <div class="row justify-content-center">
                    <div class="col-md-12">
                        <div class="progress-sell">
                            <div class="progress">
                                <div class="progress-bar progress-bar-striped progress-bar-animated {{ env('APP_URL') == 'rifasonline.link' ? 'bg-secondary' : 'bg-success' }}"
                                    role="progressbar" style="width: {{ $productModel->porcentagem() }}%"
                                    aria-valuenow="10" aria-valuemin="0" aria-valuemax="100">
                                    {{ $productModel->porcentagem() }}%
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
