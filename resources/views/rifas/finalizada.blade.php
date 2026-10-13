<div style="height: 80px; overflow: hidden; background-color: #198754; border-radius: 10px; margin-top: 10px; margin-bottom: 50px;">
    <div class="card mt-3"
        style="border: none;border-radius: 10px;background: transparent;;padding:10px;margin-bottom: 100px;margin-top: -5px !important;">
        @if ($productModel->premios()->where('descricao', '!=', '')->where('ganhador', '!=', '')->count() == 0)
            <h2 style="text-align: center;font-size: 20px; font-family: Montserrat,sans-serif !important; font-weight: 500; color: #fff; padding-top: 20px;">
                Aguardando Sorteio!
            </h2>
        @else
            
        @endif
    
    
    
        @if (env('APP_URL') == 'rifasonline.link')
            <h4>
                Aguardando sorteio pela loteria federal
            </h4>
        @endif
    
        @if ($productModel->premios()->where('descricao', '!=', '')->where('ganhador', '!=', '')->count() == 1)
            
            @foreach ($productModel->premios()->where('descricao', '!=', '') as $premio)
                <div class="row mt-2 " style="display: inline-block; margin-top: 0px !important; padding-bottom: 50px; padding-top: 10px;" >
                    
                    
                    <span class="col-md-4">
                        <label style="font-size: 20px; font-family: Montserrat,sans-serif !important; font-weight: 500; color: #fff; padding-left: 30px;">{{ $premio->ganhador }} <i class="bi bi-check-circle" style="color: #AAE1C0; margin-top: 5px !important;"></i></label>
                    </span><br>
                    <span class="col-md-4">
                        <label style="font-size: 12px; font-family: Montserrat,sans-serif !important; font-weight: 400; color: #AAE1C0; padding-left: 30px;">Ganhador(a) com o número da sorte {{ $premio->cota }}</label>
                    </span>
                </div>
                
            @endforeach
        @endif
    </div>
</div>


    