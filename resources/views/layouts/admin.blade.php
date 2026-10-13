<!-- Stored in resources/views/layouts/master.blade.php -->

<html style="height: auto;">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <meta name="robots" content="noindex">
    <meta name="googlebot" content="noindex">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('/plugins/fontawesome-free/css/all.min.css') }}">
    <link href="{{ asset('/dist/css/adminlte.min.css') }}" rel="stylesheet">
    <link href="{{ asset('/plugins/summernote/summernote-bs4.min.css') }}" rel="stylesheet">
    <link href="{{ asset('/plugins/codemirror/codemirror.css') }}" rel="stylesheet">
    <link href="{{ asset('/plugins/codemirror/theme/monokai.css') }}" rel="stylesheet">
    {{-- <link href="{{ asset('/plugins/simplemde/simplemde.min.css') }}" rel="stylesheet"> --}}

    <link href="{{ asset('/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css') }}"
        rel="stylesheet">
    <link href="{{ asset('/plugins/bootstrap-colorpicker/css/bootstrap-colorpicker.min.css') }}" rel="stylesheet">

    <link href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/buttons/2.2.3/css/buttons.dataTables.min.css" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    <title><?php echo @$data['social']->name; ?></title>

    <style>
        #loadingSystem {
            background: rgba(206, 206, 206, 0.5) url("../../images/loading.gif") no-repeat scroll center center;
            background-size: 150px 150px;
            height: 100%;
            left: 0;
            overflow: visible;
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 9999999;
        }
    </style>
</head>

<body class="sidebar-mini layout-fixed layout-navbar-fixed" style="height: auto;">

    <div id="loadingSystem" class="d-none"></div>
    <div class="wrapper" >
        <!-- Navbar -->
        <nav class="main-header navbar navbar-expand navbar-white navbar-light" style="border: none !important;">
            <!-- Left navbar links -->
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="bi bi-list" style="color: #a002d1;"></i></a>
                </li>
            </ul>

            <!-- Right navbar links -->
            <ul class="navbar-nav ml-auto">
                
                <li class="nav-item" >
                    <a class="nav-link" href="{{ route('inicio') }}" >
                        <span class="badge bg-primary" style="background-color: #a002d1 !important; color: #fff !important;"><i class="bi bi-eye"></i> Ver site</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link">
                        <form name="logout" action="{{ route('logout') }}" method="POST">
                            {{ csrf_field() }}
                            <span class="badge badge-warning" onclick="javascript:logout.submit()" style="background-color: #a002d1 !important; color: #fff !important;"><i class="bi bi-arrow-90deg-right"></i> Sair</span>
                        </form>
                    </a>
                </li>
                {{-- <li class="nav-item">
                    <a class="nav-link" data-toggle="dropdown" href="#">
                        <form name="logout" action="{{ route('logout') }}" method="POST">
                            {{ csrf_field() }}
                            <span class="badge badge-warning navbar-badge" onclick="javascript:logout.submit()"
                                style="font-size: 14px;">SAIR</span>
                        </form>
                    </a>
                </li> --}}
            </ul>
        </nav>
        <!-- /.navbar -->

        <!-- Main Sidebar Container -->
        <aside class="main-sidebar sidebar-dark-primary elevation-4" style="background: #fff; box-shadow: none !important;">
            <!-- Brand Logo -->
            

            <!-- Sidebar -->
            <div class="sidebar" style="background-color: #fff !important;">
                <!-- Sidebar Menu -->
                <nav class="mt-2">
                    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                        data-accordion="false">
                        <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
                        <li class="nav-item menu-open">
                            <a href="#" class="nav-link active" style="background-color: #a002d1 !important;">
                                <i class="nav-icon fas fa-tachometer-alt"></i>
                                <p style=" font-weight: 600; font-family: Montserrat,sans-serif !important; font-size: 14px !important;">
                                    Painel
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="{{ route('home') }}" class="nav-link" id="home">
                                        <i class="bi bi-house-fill" style="color: #a002d1 !important; font-size: 20px !important;"></i>
                                        <p style="color: #707070 !important; padding-left: 10px; font-weight: 600; font-family: Montserrat,sans-serif !important; font-size: 14px !important;">Home</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('profile') }}" class="nav-link" id="perfil">
                                        <i class="bi bi-person-fill" style="color: #a002d1 !important; font-size: 20px !important;"></i>
                                        <p style="color: #707070 !important; padding-left: 10px; font-weight: 600; font-family: Montserrat,sans-serif !important; font-size: 14px !important;">Meu perfil</p>
                                    </a>
                                </li>
                                {{-- <li class="nav-item">
                                    <a href="{{route('adminProduct')}}" class="nav-link" id="adicionar-sorteio">
                                        <i class="far fa-circle nav-icon" style="color: #a002d1 !important; font-size: 20px !important;"></i>
                                        <p style="color: #707070 !important;">Adicionar Sorteio</p>
                                    </a>
                                </li> --}}
                                <li class="nav-item">
                                    <a href="{{ route('mySweepstakes') }}" class="nav-link" id="meus-sorteios">
                                        <i class="bi bi-ticket-fill" style="color: #a002d1 !important; font-size: 20px !important;"></i>
                                        <p style="color: #707070 !important; padding-left: 10px; font-weight: 600; font-family: Montserrat,sans-serif !important; font-size: 14px !important;">Minhas Rifas</p>
                                    </a>
                                </li>
                                @if (!env('HIDE_GANHADORES'))
                                    <li class="nav-item">
                                        <a href="{{ route('painel.ganhadores') }}"
                                            class="nav-link {{ request()->is('admin-ganhadores*') ? 'active' : '' }}"
                                            id="meus-sorteios">
                                            <i class="bi bi-trophy-fill" style="color: #a002d1 !important; font-size: 20px !important;"></i>
                                            <p style="color: #707070 !important; padding-left: 10px; font-weight: 600; font-family: Montserrat,sans-serif !important; font-size: 14px !important;">Ganhadores</p>
                                        </a>
                                    </li>
                                @endif
                                
                                <li class="nav-item">
                                    
                                </li>
                                @if (env('AFILIADOS'))
                                    <li class="nav-item">
                                        <a href="{{ route('afiliados') }}"
                                            class="nav-link {{ request()->is('lista-afiliados*') ? 'active' : '' }}"
                                            id="wpp-msgs">
                                            <i class="fas fa-people-arrows" style="color: #a002d1 !important; font-size: 20px !important;"></i>
                                            <p style="color: #707070!important; padding-left: 10px; font-weight: 600; font-family: Montserrat,sans-serif !important; font-size: 14px !important;">Afiliados</p>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{ route('painel.solicitacaoAfiliados') }}"
                                            class="nav-link {{ request()->is('solicitacao-pagamento*') ? 'active' : '' }}"
                                            id="wpp-msgs">
                                            <i class="fas fa-dollar-sign" style="color: #a002d1 !important; font-size: 20px !important;"></i>
                                            <p style="color: #707070 !important; padding-left: 10px; font-weight: 600; font-family: Montserrat,sans-serif !important; font-size: 14px !important;">Solicitação de Pagamento</p>
                                        </a>
                                    </li>
                                @endif
                            </ul>
                        </li>
                    </ul>
                </nav>
                <!-- /.sidebar-menu -->
            </div>
            <!-- /.sidebar -->
        </aside>

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            @yield('content')
        </div>
        <!-- /.content -->

        <div id="sidebar-overlay"></div>

        <!-- Control Sidebar -->
        <aside class="control-sidebar control-sidebar-dark" style="background: #010140 !important">
            <!-- Control sidebar content goes here -->
        </aside>
        <!-- /.control-sidebar -->
    </div>
    <!-- ./wrapper -->


    <script src="{{ asset('/plugins/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('/dist/js/adminlte.min.js') }}"></script>
    <script src="{{ asset('/plugins/summernote/summernote-bs4.min.js') }}"></script>
    <script src="{{ asset('/plugins/codemirror/codemirror.js') }}"></script>
    <script src="{{ asset('/plugins/codemirror/mode/css/css.js') }}"></script>
    <script src="{{ asset('/plugins/codemirror/mode/xml/xml.js') }}"></script>
    <script src="{{ asset('/plugins/codemirror/mode/htmlmixed/htmlmixed.js') }}"></script>
    {{-- <script src="{{ asset('/build/js/Layout.js') }}"></script> --}}
    {{-- <script src="{{ asset('/build/js/adminlte.js') }}"></script> --}}

    <script src="{{ asset('/plugins/moment/moment.min.js') }}"></script>
    <script src="{{ asset('/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js') }}"></script>

    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>

    <script src="https://cdn.datatables.net/buttons/2.2.3/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.print.min.js"></script>

    @stack('scripts')

    <script>
        $(function(e) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
        })

        var url_atual = window.location.pathname;

        if (url_atual == '/home') {
            var d = document.getElementById("home");
            d.className += " active";
        } else if (url_atual == '/adicionar-sorteio') {
            var d = document.getElementById("adicionar-sorteio");
            d.className += " active";
        } else if (url_atual == '/meus-sorteios') {
            var d = document.getElementById("meus-sorteios");
            d.className += " active";
        } else if (url_atual == '/perfil') {
            var d = document.getElementById("perfil");
            d.className += " active";
        }

        //console.log(url_atual);
    </script>

    <!--<script>
        $(function() {
            // Summernote
            $('#summernote').summernote()

            // CodeMirror
            CodeMirror.fromTextArea(document.getElementById("codeMirrorDemo"), {
                mode: "htmlmixed",
                theme: "monokai"
            });
        })
    </script>-->

    <script src="https://code.jquery.com/jquery-3.6.3.js" integrity="sha256-nQLuAZGRRcILA+6dMBOvcRh5Pe310sBpanc6+QBmyVM="
        crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"
        integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous">
    </script>

    <script src="https://code.jquery.com/jquery-3.6.4.min.js"
        integrity="sha256-oP6HI9z1XaZNBrJURtCoUT5SUnxFr8s3BzRl+cbzUq8=" crossorigin="anonymous"></script>
    @stack('datetimepicker')

    <script src="{{ asset('plugins/summernote/summernote-bs4.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(function(e) {
            document.querySelectorAll('.summernote').forEach((el) => {
                $('#' + el.id).summernote({
                    toolbar: [
                        // [groupName, [list of button]]
                        ['style', ['bold', 'italic', 'underline', 'clear', 'fontname']],
                        ['font', ['strikethrough', 'superscript', 'subscript']],
                        ['fontsize', ['fontsize']],
                        ['color', ['color']],
                        ['para', ['ul', 'ol', 'paragraph']],
                        ['height', ['height']],
                        ['misc', ['fullscreen']],
                        ['link']
                    ]
                })
            });


            $('#summernote').summernote({
                toolbar: [
                    // [groupName, [list of button]]
                    ['style', ['bold', 'italic', 'underline', 'clear', 'fontname']],
                    ['font', ['strikethrough', 'superscript', 'subscript']],
                    ['fontsize', ['fontsize']],
                    ['color', ['color']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['height', ['height']],
                    ['misc', ['fullscreen']],
                    ['link']
                ]
            })
        })

        function loading() {
            var el = document.getElementById('loadingSystem');
            el.classList.toggle("d-none");
        }
    </script>
    @include('partials.marca-debian')
</body>

</html>
