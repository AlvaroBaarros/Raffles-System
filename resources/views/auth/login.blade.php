@extends('layouts.app')

@section('content')
<div style="backdrop-filter: blur(10px);">
    <div class="container" style="max-width: 300px; background-color: #fff; border-radius: 10px; padding-bottom: 20px;">
        <div class="title">
            <h3 style="color: #333; font-family: Montserrat,sans-serif !important; border-bottom: solid 1px #e5e5e5 !important; font-size: 20px !important; font-weight: 500 !important; text-align: left !important; padding: 20px 10px; "> Login</h3>
        <div><p style="color: #9fa4a5; font-family: Montserrat,sans-serif !important; font-size: 16px !important; font-weight: 300 !important;">Por favor, entre com seus dados.</p></div>
        </div>
        

        <form class="form-signin" method="POST" action="{{ route('login') }}" style="text-align: center;">
            {{ csrf_field() }}

            <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                

                <div class="col-md-12"
                    style="max-width: 250px;
    display: block;
    margin-right: auto;
    margin-left: auto;">
                    <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" placeholder="E-mail" style="background-color: #fff;border: solid 1px #c9c9c9; border-radius: 10px; color: #333; padding: 12px;"
                        required>

                    @if ($errors->has('email'))
                        <span class="help-block">
                            <strong>{{ $errors->first('email') }}</strong>
                        </span>
                    @endif
                </div>
            </div>

            <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                

                <div class="col-md-12"
                    style="max-width: 250px;
    display: block;
    margin-right: auto;
    margin-left: auto;">
                    <input id="password" type="password" class="form-control" name="password" placeholder="Senha" style="background-color: #fff;border: solid 1px #c9c9c9; border-radius: 10px; color: #333; padding: 12px; margin-top: 10px;" required>

                    @if ($errors->has('password'))
                        <span class="help-block">
                            <strong>{{ $errors->first('password') }}</strong>
                        </span>
                    @endif
                </div>
            </div>

            <div class="form-group">
                <div style="margin-top: 15px;" class="">
                    <button type="submit" class="btn btn-outline-primary" style="background-color: #0d6efd;border: none !important; border-radius: 10px; color: #fff !important;">
                        Continuar
                    </button>
                </div>
            </div>

        </form>
    </div>
</div>
@endsection
