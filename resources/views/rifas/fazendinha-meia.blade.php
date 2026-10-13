{{-- <div class="d-flex justify-content-between font-weight-600 mb-4">
    <div class="seletor-item rounded d-flex justify-content-between box-shadow-08 font-xs">
        <div class="nome bg-white rounded-start text-dark p-2">
            Livres
        </div>
        <div class="num bg-success text-white p-2 rounded-end">
            {{ $totalDispo }}
        </div>
    </div>

    <div class="seletor-item rounded d-flex justify-content-between box-shadow-08 font-xs">
        <div class="nome bg-white rounded-start text-dark p-2">
            Reserv
        </div>
        <div class="num bg-warning text-white p-2 rounded-end">
            {{ $totalReser }}
        </div>
    </div>

    <div class="seletor-item rounded d-flex justify-content-between box-shadow-08 font-xs">
        <div class="nome bg-white rounded-start text-dark p-2">
            Pagos
        </div>
        <div class="num bg-info text-white p-2 rounded-end">
            {{ $totalPago }}
        </div>
    </div>
</div> --}}

<style>
    .grupo-fazendinha {
        cursor: pointer;
        width: 150px;
        height: 150px;
        background-size: cover;
        display: flex;
        margin: 4px;
        border-radius: 7px;
        background-color:#404040;
    }

    @media (max-width: 768px) {
        .grupo-fazendinha {
           /*height: 100px !important;*/
        }

        .le{
            height: 100% !important;
        }

        .ld{
            height: 100% !important;
        }
        
    }

    @media (max-width: 576px) {
        .grupo-fazendinha {
            flex: 0 0 30.33333% !important;
            max-width: 30.33333% !important;
            height: 116px !important;
        }
    }

    .reservado {
        background-color: #ffc10775;
        width: 100%;
        height: 100%;
    }

    .pago {
        background-color: #0094f0b3;
        width: 100%;
        height: 100%;
    }

    .selected-group {
        opacity: 0.5;
    }

    .le.selected-group {
        background-color: #000;
    }

    .ld.selected-group {
        background-color: #000;
    }

    .le {
        width: 50%;
        height: 100%;
        background-size: cover;
    }

    .ld {
        width: 50%;
        height: 100%;
        background-size: cover;
    }
</style>

<div class="row g-0 justify-content-center" style="margin-top: 80px;">
    @foreach ($productModel->numbers() as $numero)
        @if ($numero->groupSide() == 'le')
            <div class="col-4 grupo-fazendinha"
                style="background-image: url('{{ asset('images/bixos/' . $numero->onlyGroup() . '.webp') }}'); background-size: 100%;background-repeat: no-repeat">
                {{-- Numero LE --}}
                @if ($numero->statusFormated() == 'disponivel')
                    <div class="le" data-grupo="{{ $numero->grupoFazendinha() }}"
                        onclick="selectFazendinha('{{ $numero->number }}')" id="{{ $numero->number }}">
                    </div>
                @else
                    <div class="le {{ $numero->statusFormated() }}" onclick="info('{{ $numero->status }} por {{ $numero->participante()->name }}')">
                    </div>
                @endif

                {{-- Numero LD --}}
                @if ($numero->numeroLD()->statusFormated() == 'disponivel')
                    <div class="ld" data-grupo="{{ $numero->numeroLD()->grupoFazendinha() }}"
                        onclick="selectFazendinha('{{ $numero->numeroLD()->number }}')" id="{{ $numero->numeroLD()->number }}">
                    </div>
                @else
                    <div class="ld {{ $numero->numeroLD()->statusFormated() }}" onclick="info('{{ $numero->numeroLD()->status }} por {{ $numero->numeroLD()->participante()->name }}')">
                    </div>
                @endif
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
