<!DOCTYPE html>
<html lang="pt-br">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="Content-Language" content="pt-br">
    <meta name="robots" content="noindex">
    <meta name="googlebot" content="noindex">
    <meta name="color-scheme" content="light only">
    <meta name="X-DarkMode-Default" value="false"/>
    @yield('ogContent')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4bw+/aepP/YC94hEpVNVgiZdgIC5+VKNBQNGCHeKRQN+PtmoHDEXuppvnDJzQIu9" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-HwwvtgBNo3bZJJLYd8oVXjrBZt8cqVSpeBNS5n7C8IVInixGAoxmnlMuBnhbgrkm" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/js/all.min.js" integrity="sha512-uKQ39gEGiyUJl4AI6L+ekBdGKpGw4xJ55+xyJG7YFlJokPNYegn9KwQ3P8A7aFQAUtUsAQHep+d/lrGqrbPIDQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <link rel="stylesheet" href="{{ mix('css/rifapress.css') }}">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/animejs/2.0.2/anime.min.js"></script>
    <script>
        $(function () {
            $('[data-toggle="tooltip"]').tooltip();

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
        });
    </script>

    <!-- Fotorama from CDNJS, 19 KB -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/fotorama/4.6.4/fotorama.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fotorama/4.6.4/fotorama.js"></script>

    <!--<script defer src="{{ mix('js/app.js') }}"></script>
    <script src="//{{ Request::getHost() }}:6001/socket.io/socket.io.js"></script>-->

    <title><?php echo @$data['social']->name; ?> @yield('title')</title>


    <meta name="facebook-domain-verification" content="<?php echo @$data['social']->verify_domain_fb; ?>"/>

    <?php echo @$data['social']->pixel; ?>


    <script src="https://sdk.mercadopago.com/js/v2"></script>

    <script>
        const mp = new MercadoPago("<?php echo @$data['social']->key_pix_public; ?>");
    </script>
    <link rel="stylesheet" href="{{ asset('css/menu2.css') }}">
</head>
<div style="padding: 50px; background-color: #000; margin-bottom: -100px;"></div>
<body style="background-color: #F1F1F1 !important;">
@section('sidebar')
@show

<?php
$subDomain = explode('.', request()->getHost());
?>

<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
<div id="loadingSystem" class="d-none"></div>


<nav class="navbar navbar-expand-lg  fixed-top px-0 py-3 " style="background-color:#000;">

    <div class="container header-menu" style="justify-content:space-evenly;align-items: center;">
        <div class="col-md-6 col-12 d-flex justify-content-between align-items-center">
            <div>
                <a class="" href="{{ route('inicio') }}"
                   style="color: #ffffff!important;font-family: 'Roboto Condensed', sans-serif;">
                    @if (@$data['social']->logo)
                        <img src="{{ asset('products/' . @$data['social']->logo) }}" alt="" width="100"
                             height="50">
                    @else
                        SEU SCRIPT
                    @endif
                </a>
            </div>

            <div>
                <a href="#" data-bs-toggle="modal" data-bs-target="#consultar-reservas"
                   style="text-decoration: none; font-size: 12px; color: #fff">
                    @if (env('APP_URL') == 'seuscript.com.br')
                        <span class="badge bg-success p-2" style="font-size: 10px;"><i
                                    class="fas fa-search"></i>&nbsp;MEUS NÚMEROS</span>
                    @else
                        <i class="bi bi-bag-check-fill"
                           style="margin-top: 10px;font-size: 35px;color: rgb(180, 180, 180) !important; opacity: 1;"></i>
                    @endif
                </a>

                <button type="button" aria-label="Menu" class="btn btn-link text-white" data-bs-toggle="modal"
                        data-bs-target="#mobileMenu" style="margin-top: -15px;"><i class="bi bi-list"
                                                                                   style="font-size: 40px;"></i>
                </button>
            </div>
        </div>
    </div>
    </div>
</nav>

<menu id="mobileMenu" class="modal fade modal-fluid" tabindex="-1" aria-labelledby="mobileMenuLabel"
      style="display: none; background-color: #000;" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen">
        <div class="modal-content bg-cor-primaria">
            <header class="app-header app-header-mobile--show">
                <div class="container container-600 h-100 d-flex align-items-center justify-content-between">
                    <a href="/">
                        @if (@$data['social']->logo)
                            <img src="{{ asset('products/' . @$data['social']->logo) }}" alt=""
                                 class="app-brand img-fluid">
                        @else
                            SEU SCRIPT
                        @endif
                    </a>
                    <div class="app-header-mobile">
                        <button type="button"
                                class="btn btn-link text-white menu-mobile--button pe-0 font-lgg"
                                data-bs-dismiss="modal" aria-label="Fechar"><i class="bi bi-x-circle"></i></button>
                    </div>
                </div>
            </header>
            <div class="modal-body" style="background: #000 !important">
                <div class="container container-600">
                    <nav class="nav-vertical nav-submenu font-xs mb-2">
                        <ul >
                            <li style="border-bottom: solid 1px #2b2b2b !important; border-radius: 0px !important;"><a class="text-white" alt="Página Principal" href="/"><i
                                            class="icone bi bi-house"></i><span>Início</span></a></li>
                            @if (env('AFILIADOS'))
                                <li style="border-bottom: solid 1px #2b2b2b !important; border-radius: 0px !important;"><a class="text-white" alt="Área de Afiliados"
                                       href="{{ route('afiliado.home') }}"><i
                                                class="icone fas fa-people-arrows"></i><span>Área de
                                                Afiliados</span></a>
                                </li>
                            @endif
                            <li style="border-bottom: solid 1px #2b2b2b !important; border-radius: 0px !important;"><a class="text-white" alt="Sorteios" href="/sorteios"><i
                                            class="icone bi bi-card-list"></i><span>Sorteios</span></a></li>
                           
                            <li style="border: none !important;"><a alt="Créditos" class="text-white" href="{{ route('ganhadores') }}"><i
                                            class="icone bi bi-blockquote-right"></i><span>Termos de Uso</span></a></li>
                            </li>
                        </ul>
                    </nav>
                    <div class="text-center">
                        <a href="/login" class="btn btn-primary w-100 rounded-pill">
                            <i class="icone bi bi-box-arrow-in-right"></i>
                            Entrar
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</menu>


    @if (@$data['social']->group_whats !== null)
        <div class="row  d-flex mt-5">
        
        </div>
    @endif


<!-- Modal  consultar -->
<div class="modal fade" id="consultar-reservas" tabindex="-1" aria-labelledby="exampleModalLabel"
     aria-hidden="true" style="z-index: 9999999; backdrop-filter: blur(10px);">
    <div class="modal-dialog">
        <div class="modal-content" style="border: none;">
            <div class="modal-header" style="background-color: #fff; border-bottom: solid 1px #c7c7c7;">
                <h5 class="modal-title" id="exampleModalLabel" style="color: #333; font-family: Montserrat,sans-serif !important; font-size: 20px !important; font-weight: 500 !important;">Buscar compras</h5>
                
            </div>
            <div class="modal-body" style="background-color: #fff; border-radius: 10px;">
                <div class="row">
                    <div class="col-md-12">
                        <form action="{{ route('minhasReservas') }}" method="POST" style="display: block;">
                            {{ csrf_field() }}
                            <p style="color: #323232; font-family: Montserrat,sans-serif !important; font-weight: 300 !important; font-size: 16px; margin-bottom: 0px; "><strong>Informe seu telefone</strong></p>
                            <input type="text" name="telephone" id="telephone"
                                   style="margin-bottom: 10px; background-color: #fff;border: solid 1px #c9c9c9; border-radius: 10px; color: #333; margin-right: 10px;"
                                   aria-describedby="passwordHelpBlock" maxlength="15" placeholder=""
                                   class="form-control" required>
                            <button type="submit" class="btn btn-danger" style="background-color: #0b5ed7 !important; Montserrat,sans-serif !important; font-weight: 600 !important; border: none; float: right;">Buscar compras</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@if (@$data['social']->group_whats != null)
    <a href="{{ @$data['social']->group_whats }}" class="botao-flutuante botao-flutuante-whats" target="_blank" style="font-size: 13px !important; background-color: #198754 !important; width: 100px; font-weight: 500; height: 30px; box-shadow: none;">
        <i class="fab fa-whatsapp"></i><span style="padding-left: 5px; ">Grupo</span>
    </a>
@endif

@if (@$data['social']->instagram != null)
    <a href="{{ @$data['social']->instagram }}" class="botao-flutuante botao-flutuante-insta" target="_blank" style="font-size: 13px !important; background-color: #f1236b !important; width: 100px; font-weight: 500; height: 30px; box-shadow: none;  margin-bottom: -15px !important;">
        <i class="fab fa-instagram"></i><span style="padding-left: 5px; ">Instagram</span>
    </a>
@endif

@yield('content')


<footer class="footer" style="text-align: center">
    
</footer>


<script>
    document.getElementById('telephone').addEventListener('input', function (e) {
        var aux = e.target.value.replace(/\D/g, '').match(/(\d{0,2})(\d{0,5})(\d{0,4})/);
        e.target.value = !aux[2] ? aux[1] : '(' + aux[1] + ') ' + aux[2] + (aux[3] ? '-' + aux[3] : '');
    });

    document.getElementById('telephone1').addEventListener('input', function (e) {
        var aux = e.target.value.replace(/\D/g, '').match(/(\d{0,2})(\d{0,5})(\d{0,4})/);
        e.target.value = !aux[2] ? aux[1] : '(' + aux[1] + ') ' + aux[2] + (aux[3] ? '-' + aux[3] : '');
    });

    function loading() {
        var el = document.getElementById('loadingSystem');
        el.classList.toggle("d-none");
    }
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous">
</script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</body>

</html>

<html lang="en">

<head>
    <title><?php echo @$data['social']->name; ?> @yield('title')</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!--===============================================================================================-->
    <link rel="icon" type="image/png" href="{{ asset('images/icons/favicon.ico') }}" />
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="{{ asset('vendor/bootstrap/css/bootstrap.min.css') }}">
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="{{ asset('fonts/font-awesome-4.7.0/css/font-awesome.min.css') }}">
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="{{ asset('vendor/animate/animate.css') }}">
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="{{ asset('vendor/css-hamburgers/hamburgers.min.css') }}">
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="{{ asset('vendor/select2/select2.min.css') }}">
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="{{ asset('css/util.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/main.css') }}">
    <!--===============================================================================================-->
</head>

<body  >
        
        <div class="limiter">
            
            <div class="container-login100" style="background-color: #f1f1f1 !important;">
                <div class="wrap-login100" style="max-width: 600px !important; background-color: #f1f1f1 !important; ">
                    
    
                    <form class="login100-form validate-form" method="POST" action="{{ route('afiliado.login') }}" style="background-color: #fff !important; border-radius: 10px !important;  margin-top: -100px !important; box-shadow: 0 4px 3px rgba(0,0,0,.08);">
                        @csrf
                        @if ($errors->any())
                            @foreach ($errors->all() as $error)
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    {{ $error }}
                                </div>
                            @endforeach
                        @endif
    
    
                        <h3 style="color: #333; font-family: Montserrat,sans-serif !important; border-bottom: solid 1px #e5e5e5 !important; font-size: 20px !important; font-weight: 500 !important; text-align: left !important; padding: 20px 10px;"> Login de Afiliado</h3>
    
                        <div class="wrap-input100 validate-input" data-validate="Valid email is required: ex@abc.xyz" style="padding: 20px 20px 0px 20px !important;">
                            <input class="input100" type="email" value="{{ old('email') }}" name="email"
                                placeholder="E-mail" style="background-color: #f1f1f1;border: solid 1px #c9c9c9; border-radius: 10px; color: #333; padding: 12px;" required>
                            <span class="focus-input100"></span>
                            <span class="symbol-input100">
                                
                            </span>
                        </div>
    
                        <div class="wrap-input100 validate-input" data-validate="Password is required" style="padding: 0px 20px 0px 20px !important;">
                            <input class="input100" type="password" name="password" placeholder="Senha" style="background-color: #f1f1f1;border: solid 1px #c9c9c9; border-radius: 10px; color: #333; padding: 12px;" required>
                            <span class="focus-input100" ></span>
                            <span class="symbol-input100">
                                
                            </span>
                        </div>
    
                        <div class="container-login100-form-btn" style="padding: 0px 20px !important;">
                            <button class="login100-form-btn" type="submit" style="background-color: #0d6efd;border: none !important; border-radius: 10px; color: #fff !important;">
                                Login
                            </button>
                        </div>
    
                        <div class="text-center p-t-12">
                            <a class="txt2" href="{{ route('afiliado.cadastro') }}">
                                Cadastre-se como afiliado
                                <i class="fa fa-long-arrow-right m-l-5" aria-hidden="true"></i>
                            </a>
                        </div>
    
                        <div class="text-center p-t-136">
                            {{-- <a class="txt2" href="#">
    							Create your Account
    							<i class="fa fa-long-arrow-right m-l-5" aria-hidden="true"></i>
    						</a> --}}
                        </div>
                    </form>
                </div>
            </div>
        </div>
    



    <!--===============================================================================================-->
    <script src="{{ asset('vendor/jquery/jquery-3.2.1.min.js') }}"></script>
    <!--===============================================================================================-->
    <script src="{{ asset('vendor/bootstrap/js/popper.js') }}"></script>
    <script src="{{ asset('vendor/bootstrap/js/bootstrap.min.js') }}"></script>
    <!--===============================================================================================-->
    <script src="{{ asset('vendor/select2/select2.min.js') }}"></script>
    <!--===============================================================================================-->
    <script src="{{ asset('vendor/tilt/tilt.jquery.min.js') }}"></script>
    <script>
        $('.js-tilt').tilt({
            scale: 1.1
        })
    </script>
    <!--===============================================================================================-->
    <script src="{{ asset('js/main.js"') }}></script>

</body>

</html>
