<style>
    .title-rifa-destaque {
        
        border-bottom-right-radius: 20px;
        border-bottom-left-radius: 20px;
        padding-bottom: 10px;
    }

    .title-rifa-destaque.dark {
        background: #222222;
    }

    .title-rifa-destaque.dark h1 {
        color: #fff;
    }

    .title-rifa-destaque.dark p {
        color: #fff;
    }

    .valor.dark {
        color: #fff;
    }

    .desc {
        border: none;
        border-radius: 10px;
        background-color: #fff;
        max-height: 250px;
        padding: 10px;
        margin-bottom: 0px;
        overflow: scroll
    }

    .desc.dark{
        background: #222222;
    }

    .desc.dark p{
        color: #fff;
    }

    .data-sorteio.dark{
        color: #fff !important;
    }
</style>

<div id="carouselExampleIndicators" class="carousel slide" data-bs-ride="carousel" >
    <div class="carousel-inner" style="margin-top: -20px;">
        @foreach ($productModel->fotos() as $key => $foto)
            <div class="carousel-item {{ $key === 0 ? 'active' : '' }}" style="margin-top: 30px; max-width: 700px !important; max-height: 360px !important; "
                id="slide-foto-{{ $foto->id }}">
                <img src="/products/{{ $foto->name }}"
                    style="border-top-right-radius: 20px;border-top-left-radius: 20px; max-width: 700px !important; max-height: 360px !important;"
                    class="d-block w-100" alt="...">
            
            </div>
            
        @endforeach
    </div>

    <div class="title-rifa-destaque {{ $config->tema }}" style="background-color: #fff; margin-bottom: 12px; box-shadow: 0 4px 3px rgba(0,0,0,.08); border-radius: 0px 0px 10px 10px !important;">
        <h1 style="font-family: Montserrat,sans-serif; text-transform: uppercase; font-weight: 500 !important;">{{ $productModel->name }}</h1>
        <p style="font-family: Montserrat,sans-serif !important; font-weight: 400 !important;">{{ $productModel->subname }}</p>
        <div style="width: 100%;">
            {!! $productModel->status() !!}
            
        </div>
    </div>
</div>

<button type="button" data-bs-toggle="modal" data-bs-target="#exampleModal" onclick="openModal()"
                class="btn btn-secondary btn-sm bg-secondary"
                style="font-size: 12px; width: 100%; background-color: #6c757d !important; {{ env('APP_URL') == 'rifasonline.link' ? 'background-color: red !important' : 'background: #6c757d !important' }}; border-radius: 7px; !important; margin-top: -2px !important; Montserrat,sans-serif; font-size: 14px !important; font-weight: 500;">
                <i class="bi bi-cart"></i>&nbsp;Ver meus números
</button>
<div class="container mt-2">
    <div class="text-center">
        @if ($productModel->draw_date)
                
                <span class="data-sorteio {{ $config->tema }} ml-1" style="Montserrat,sans-serif; font-size: 13px !important; font-weight: 400;">
                    Sorteio <span style="background-color:#f1f1f1; border-radius: 5px; box-shadow: 0 4px 3px rgba(0,0,0,.08)!important; Montserrat,sans-serif; font-size: 11px !important; font-weight: 500; padding: 4px 8px;">{{ date('d/m/Y', strtotime($productModel->draw_date)) }}
                    {{-- {!! $product->dataSorteio() !!} --}}</span>
                </span>
            @endif
        <span class="valor {{ $config->tema }}" style="Montserrat,sans-serif; font-size: 13px !important; font-weight: 400;">Por Apenas</span>
        <span class="badge p-2" style="background: #000; color: #d1d1d1; Montserrat,sans-serif; font-size: 14px !important; font-weight: 600;">R$
            {{ $product[0]->price }}</span>
        
    </div>
</div>

