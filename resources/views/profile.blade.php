@extends('layouts.admin')

@section('content')
    <section class="content-header" style="background-color: #f5f5f5 !important;">
        <div class="container-fluid">
            
        </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content" >
        <div class="container-fluid" >
            <!-- Main content -->
            <div class="row" >
                <div class="col-md-3" >

                    <!-- Profile Image -->
                    
                    <!-- /.card -->

                    <script>
                        function submitFoto() {
                            $('#form-logo').submit();
                        }

                        function alteraLogo() {
                            $('#input-logo').click();
                        }
                    </script>

                    <div class="card card-primary card-outline" style="border: none; box-shadow: none;">
                        <div class="card-body box-profile">
                            <div class="text-center">
                                @if ($users->logo)
                                    <img src="{{ asset('products/' . $users->logo) }}" alt="" width="200">
                                @endif
                            </div>

                            <h3 class="profile-username text-center"><button class="btn btn-sm btn-success"
                                    onclick="alteraLogo()" style="background-color: #a002d1; border: none;"><span><i class="bi bi-arrow-left-right"></i></span> Alterar Logo</button></h3>
                                    <center><p>3 x 1.5 (cm)</p></center>

                            <form class="d-none" action="{{ route('alterarLogo') }}" enctype="multipart/form-data"
                                method="POST" id="form-logo">
                                @csrf
                                <input type="file" id="input-logo" name="logo" onchange="submitFoto()">
                            </form>

                            <p class="text-muted text-center"></p>
                        </div>
                        <!-- /.card-body -->
                    </div>
                </div>
                <!-- /.col -->
                <div class="col-md-9" >
                    <div class="card" style="border:none; box-shadow: none;">
                        <div class="card-header p-2" style="background-color: #fff;">
                            <ul class="nav nav-pills">
                                <li class="nav-item" style="margin: 3px;"><a class="nav-link active" href="#settings"
                                        data-toggle="tab" style="background-color: #a002d1; color: #fff;">Perfil</a></li>
                                <li class="nav-item" style="margin: 3px;"><a class="nav-link" href="#pixel"
                                        data-toggle="tab" style="background-color: #d760ff; color: #fff;">Pixel Facebook</a></li>
                            </ul>
                        </div ><!-- /.card-header -->
                        <div class="card-body" >
                            <div class="tab-content">
                                <div class="active tab-pane" id="settings">
                                    <form action="{{ route('updateProfile') }}" method="POST" class="form-horizontal">
                                        {{ csrf_field() }}
                                        <div class="form-group">
                                            <label>Nome</label>
                                            <input type="text" class="form-control" name="name"
                                                value="{{ $users->name }}" placeholder="Nome">
                                        </div>
                                        <div class="form-group">
                                            <label>Nome da plataforma</label>
                                            <input type="text" class="form-control" name="platform"
                                                value="{{ $users->platform }}" placeholder="Plataforma">
                                        </div>
                                        
                                        <div class="form-group">
                                            <label>Suporte - Whatsapp</label>
                                            <input type="text" class="form-control" name="telephone"
                                                value="{{ $users->telephone }}" placeholder="Telefone">
                                        </div>
                                        <div class="form-group">
                                            <label>Grupo Whatsapp</label>
                                            <input type="text" class="form-control" name="group_whats"
                                                value="{{ $users->group_whats }}" placeholder="">
                                        </div>
                                        <div class="form-group">
                                            <label>E-mail</label>
                                            <input type="email" class="form-control" name="email"
                                                value="{{ $users->email }}" placeholder="E-mail">
                                        </div>
                                        <div class="form-group">
                                            <label>Access Token public (Mercado Pago)</label>
                                            <input type="text" class="form-control" name="key_public"
                                                value="{{ $users->key_pix_public }}" placeholder="">
                                        </div>
                                        <div class="form-group">
                                            <label>Access Token (Mercado Pago)</label>
                                            <input type="text" class="form-control" name="key"
                                                value="{{ $users->key_pix }}" placeholder="">
                                        </div>
                                        
                                        <div class="form-group bg-info rounded p-2" style="background-color: #a002d1 !important;">
                                            <strong style="color: #fff">IMPORTANTE!</strong>
                                            <br>
                                            Inserir o link abaixo na aba Webhook no site da Paggue. Ele é o responsável pela
                                            baixa automática.
                                            <br><br>
                                            {{ route('api.notificaoPaggue') }}
                                        </div>
                                        <div class="form-group">
                                            <label>CLIENT KEY (Paggue)</label>
                                            <input type="text" class="form-control" name="paggue_client_key"
                                                value="{{ $users->paggue_client_key }}" placeholder="">
                                        </div>
                                        <div class="form-group">
                                            <label>CLIENT SECRET (Paggue)</label>
                                            <input type="text" class="form-control" name="paggue_client_secret"
                                                value="{{ $users->paggue_client_secret }}" placeholder="">
                                        </div>
                                        
                                        <div class="form-group">
                                            <label>Instagram</label>
                                            <input type="text" class="form-control" name="instagram"
                                                value="{{ $users->instagram }}" placeholder="instagram">
                                        </div>
                                        <div class="form-group">
                                            <label>Senha</label>
                                            <input type="text" class="form-control" name="senha"
                                                placeholder="Senha">
                                        </div>
                                        <div class="form-group">
                                            <button type="submit" class="btn btn-danger" style="background-color: #a002d1; border: none;">Alterar</button>
                                        </div>
                                    </form>
                                </div>
                                <!-- /.tab-pane -->
                                <div class="tab-pane" id="pixel">
                                    <form action="{{ route('pixel') }}" method="POST" class="form-horizontal">
                                        {{ csrf_field() }}
                                        <div class="form-group">
                                            <label>Verificação do domínio Facebook</label>
                                            <input type="text" class="form-control" name="verify"
                                                value="{{ $users->verify_domain_fb }}"
                                                placeholder="Código de verificação do domínio" required>
                                        </div>
                                        <div class="form-group">
                                            <label>Pixel</label>

                                            <textarea class="form-control" name="pixel" rows="20" style="resize: none;" required>{{ $users->pixel }}</textarea>
                                        </div>
                                        <div class="form-group">
                                            <button type="submit" class="btn btn-danger">Cadastrar</button>
                                        </div>
                                    </form>
                                </div>
                                <!-- /.tab-pane -->
                            </div>
                            <!-- /.tab-content -->
                        </div><!-- /.card-body -->
                    </div>
                    <!-- /.card -->
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->
        </div>
        <!--/. container-fluid -->
    </section>
    <!-- /.content -->
@endsection
