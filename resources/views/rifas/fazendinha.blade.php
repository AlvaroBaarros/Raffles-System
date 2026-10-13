<style>
    .grupo-fazendinha {
        cursor: pointer;
        
       
    }

    @media (max-width: 768px) {

        .pago{
            height: 100%;
        }

        .reservado{
            height: 100%;
        }
    }

    .reservado {
        background-color: #ffc10775;
        width: 100%;
        height: 90%;
    }

    .pago {
        background-color: #0094f0b3;
        width: 100%;
        height: 90%;
    }

    .selected-group {
        opacity: 0.5;
    }

    .h150 {
        height: 150px;
        background-size: 50%;
        background-repeat: no-repeat
    }
</style>

<div class="row g-0 justify-content-center" style="margin-top: 70px;">
    @foreach ($productModel->numbers() as $numero)
        @if ($numero->statusFormated() == 'disponivel')
            <div class="col-4 grupo-fazendinha {{ 'fazenda-'. $numero->statusFormated() }}" data-grupo="{{ $numero->grupoFazendinha() }}"
                 onclick="selectFazendinha('{{ $numero->number }}')" id="{{ $numero->number }}" >
                <img style="width: 100%;" src="{{ asset('images/bixos/' . $numero->number . '.webp') }}">
            </div>
        @else
            <div class="col-4" title="{{ $numero->status }} por {{ $numero->participante()->name }}" onclick="info('{{ $numero->status }} por {{ $numero->participante()->name }}')" class="grupo-fazendinha {{ 'fazenda-'. $numero->statusFormated() }}" data-grupo="{{ $numero->grupoFazendinha() }}" id="{{ $numero->number }}">
                <img style="width: 100%; border-radius: 7px;" src="{{ asset('images/bixos/' . $numero->number . '.webp') }}">
                <div class="{{ $numero->statusFormated() }}"></div>
            </div>
        @endif
    @endforeach
</div>


<div class="d-flex justify-content-center">
    <div class="payment" id="payment" style="display: none; max-width: 550px !important;margin-bottom: 10px;">
        <div class="row justify-content-center">
            <div class="col-md-12 col-9" style="background-color: #fff; color: #000; border-radius: 10px;">
                <div class="container">
                    <div class="row">
                        <div class="col-12 text-center" style="width: 100%;">
                            <span id="numberSelected" class="scrollmenu"></span>
                        </div>
                    </div>
                    <div class="row"
                        style="text-align: center;background-color: #fff; margin-top: 5px; justify-content-center; margin-bottom: 10px; ">
                        <div class="col-12 d-flex justify-content-center">
                            <center style="width: 100%; box-shadow: 0 4px 3px rgba(0,0,0,.08);">
                                <button type="button" 
                                    style="color: #fff;border: none;width: 100% !important;margin-top: 0px;font-weight: 600; font-family: Montserrat,sans-serif !important; background-color: #198754 !important; border-radius: 7px; padding: 13px 10px;" onclick="openModalCheckout()"><i class="bi bi-check2-circle"></i>&nbsp;Quero Participar
                                    <span style="font-size: 14px; float:right"><span
                                            id="numberSelectedTotal"></span></span></button>
                            </center>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<br><br><br>

<script>
    function info(msg){
        Swal.fire(msg)
    }
</script>
