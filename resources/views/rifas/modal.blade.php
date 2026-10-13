<!-- Modal FINALIZAR RESERVA (CHECKOUT) -->
{{-- <div class="modal fade" id="staticBackdrop" data-backdrop="static" data-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true" style="z-index: 999999999;">
    <div class="modal-dialog">
        <form action="{{ route('bookProductManualy') }}" method="POST">
            {{ csrf_field() }}
            <div class="modal-content" style="border: none;">
                <div class="modal-header" style="background-color: #939393;color: #fff;">
                    <h5 class="modal-title" id="staticBackdropLabel">FINALIZAR RESERVA</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: #fff;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" style="background: #efefef;color: #939393;">
                    <div class="form-group">
                        <input type="hidden" name="tokenAfiliado" value="{{ $tokenAfiliado }}">
                        @if ($type_raffles == 'manual')
                            <label>Pagamento referente à participação na ação entre amigos
                                <b>{{ $product[0]->product }}</b> com os números:</label>
                        @else
                            <label>Pagamento referente à participação na ação entre amigos
                                <b>{{ $product[0]->product }}.</b></label>
                        @endif
                        <div class="row">
                            <div class="col-md-12">
                                <div class="numberSelected" id="numberSelectedModal"
                                    style="overflow-y: auto;width: 190px;"></div>
                            </div>
                        </div>
                        @if (str_starts_with($productModel->modo_de_jogo, 'fazendinha'))
                            <input type="hidden" class="form-control" name="" id="qtdNumbers">
                        @else
                            @if ($type_raffles == 'manual')
                                <input type="hidden" class="form-control" name="qtdNumbers" id="qtdNumbers"
                                    value="">
                                <input type="hidden" class="form-control" name="rifaManual" id="qtdNumbers"
                                    value="1">
                            @else
                                <input type="hidden" class="form-control" name="qtdNumbers" id="qtdNumbers">
                            @endif
                        @endif

                        <input type="hidden" class="form-control" name="productName" value="{{ $product[0]->name }}">
                        <input type="hidden" class="form-control" name="productID" value="{{ $product[0]->id }}">
                        <input type="hidden" class="form-control" name="numberSelected" id="numberSelectedInput">
                        @if ($type_raffles == 'manual')
                            <small class="form-text" style="color: green;"><b>Valor a pagar: <small
                                        style="font-size: 15px;" id="numberSelectedTotalModal"></small></b></small>
                        @else
                            <small class="form-text" style="color: green;"><b>Valor a pagar: <small
                                        style="font-size: 15px;" id="numberSelectedTotalModal"></small></b></small>
                        @endif
                    </div>
                    <!--<legend>Por favor, preencha os dados abaixo:</legend>-->
                    <div class="form-group">
                        <label>NOME COMPLETO</label>
                        <input type="text" class="form-control"
                            style="background-color: #fff;border: none;color: #333;" name="name"
                            placeholder="Informe seu nome completo" required>
                    </div>
                    @if (!env('HIDE_EMAIL'))
                        <div class="form-group">
                            <label>E-mail (opcional)</label>
                            <input type="email" class="form-control"
                                style="background-color: #fff;border: none;color: #333;" name="email" id="email"
                                placeholder="Informe o seu e-mail" maxlength="50" required>
                        </div>
                    @endif
                    <div class="form-group {{ $productModel->gateway == 'asaas' ? '' : 'd-none' }}">
                        <label>CPF (somente números)</label>
                        <input type="number" class="form-control"
                            style="background-color: #fff;border: none;color: #333;" name="cpf" id="cpf"
                            placeholder="Informe o seu CPF" maxlength="50" required>
                    </div>
                    <div class="form-group">
                        <label>CELULAR (Whatsapp)</label>
                        <input type="text" class="form-control numbermask"
                            style="background-color: #fff;border: none;color: #333;" name="telephone" id="telephone1"
                            placeholder="Informe seu telefone com DDD" maxlength="15" required>
                    </div>
                    <input type="hidden" id="promo" name="promo">
                    <!--<small class="form-text text-muted">Reservando seu(s) número(s), você declara que leu e concorda com nossos <a href="{{ url('terms-of-use') }}">Termos de Uso</a>.</small>-->
                </div>
                <div class="modal-footer" style="background: #939393;color: #fff;">
                    <!--<button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>-->
                    <button type="submit" onClick="this.form.submit(); this.disabled=true; this.innerHTML='PROCESSANDO...'; "
                        class="btn btn-success"
                        style="width: 100%;min-height: 60px;border: none;color: #fff;font-weight: bold;width: 100%;background-color: green">PROSSEGUIR</button>
                </div>
            </div>
        </form>
    </div>
</div> --}}

<div class="modal fade" id="staticBackdrop" data-backdrop="MODA" data-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true" style="z-index: 999999; backdrop-filter: blur(10px);">
    <div class="modal-dialog modal-lg" style="max-width: 500px !important; padding-top: 10%;">
        <form action="{{ route('bookProductManualy') }}" id="form-checkout" method="POST">
            {{ csrf_field() }}
            <div class="modal-content" style="border: none; ">
                <div class="modal-header" style="background-color: #fff;color: #333; font-family: Montserrat,sans-serif !important; border-bottom: solid 1px #e5e5e5 !important; font-size: 20px !important; font-weight: 400 !important;">
                    <h5 class="modal-title" id="staticBackdropLabel">Checkout</h5>
                    
                </div>
                <div class="modal-body" style="background: #fff;color: #939393;">
                    @if ($type_raffles == 'manual')
                        <small class="form-text d-none" style="color: green;"><b>Valor a pagar: <small style="font-size: 15px;"
                                    id="numberSelectedTotalModal"></small></b></small>
                    @else
                        <small class="form-text d-none" style="color: green;"><b>Valor a pagar: <small style="font-size: 15px;"
                                    id="numberSelectedTotalModal"></small></b></small>
                    @endif

                    <div class="form-group">
                        <input type="hidden" name="tokenAfiliado" value="{{ $tokenAfiliado }}">
                        <div class="row">
                            <div class="col-md-12 d-none">
                                <div class="numberSelected" id="numberSelectedModal"
                                    style="overflow-y: auto;width: 190px;"></div>
                            </div>
                        </div>
                        @if (str_starts_with($productModel->modo_de_jogo, 'fazendinha'))
                            <input type="hidden" class="form-control" name="" id="qtdNumbers">
                        @else
                            @if ($type_raffles == 'manual')
                                <input type="hidden" class="form-control" name="qtdNumbers" id="qtdNumbers"
                                    value="">
                                <input type="hidden" class="form-control" name="rifaManual" id="qtdNumbers"
                                    value="1">
                            @else
                                <input type="hidden" class="form-control" name="qtdNumbers" id="qtdNumbers">
                            @endif
                        @endif

                        <input type="hidden" id="qtdManual">
                        <input type="hidden" class="form-control" name="productName" value="{{ $product[0]->name }}">
                        <input type="hidden" class="form-control" name="productID" value="{{ $product[0]->id }}">
                        <input type="hidden" class="form-control" name="numberSelected" id="numberSelectedInput">
                    </div>

                    <div class="form-group"
                        style="background-color: cff4fc;padding: 10px;border-radius: 5px;color: #055160; border: solid 1px #9eeaf9 !important; font-family: Montserrat,sans-serif !important; font-size: 14px !important; font-weight: 400 !important;">
                        <span>Você está adquirindo <strong id="qtd-checkout">33</strong> cota(s) da ação 
                            <strong id="rifa-checkout">20 MIL NA CONTA</strong>.</span>
                    </div>

                    <div class="form-group d-flex d-none" id="div-customer" style="margin-top: 10px; margin-bottom: 10px;">
                        <div>
                            <img src="{{ asset('images/default-user.jpg') }}"
                                style="width: 50px; height: 50px;border-radius: 10px;">
                        </div>

                        <div class="ml-2" style="margin: 10px 0px;">
                            <h4 id="customer-name" style="color: #333; font-weight: 400 !important; font-family: Montserrat,sans-serif !important; font-size: 14px !important; padding-left: 10px !important;">Mario Souza</h4>
                            <h5 id="customer-phone" style="color: #333; font-weight: 400 !important; font-family: Montserrat,sans-serif !important; font-size: 14px !important; padding-left: 10px !important;">(15) 99770-6933</h5>
                        </div>
                    </div>

                    <div class="form-group" id="div-telefone">
                        <label style="color: #333; font-family: Montserrat,sans-serif !important; font-size: 13px !important; font-weight: 400 !important; padding-top: 10px; "><strong>Informe seu telefone</strong></label>
                        <input type="text" class="form-control numbermask keydown"
                            style="background-color: #fff;border: solid 1px #c9c9c9; border-radius: 10px; color: #333;" name="telephone" id="telephone1"
                            placeholder="" maxlength="15" required>
                        <input type="hidden" name="telephone" id="phone-cliente">
                        <input type="hidden" id="customer" name="customer">
                    </div>

                    <div class="form-group d-none" id="div-nome">
                        <label style="color: #333; font-family: Montserrat,sans-serif !important; font-size: 13px !important; font-weight: 400 !important;"><strong>Nome Completo</strong></label>
                        <input type="text" class="form-control"
                            style="background-color: #fff;border: solid 1px #c9c9c9; border-radius: 10px; color: #333;" name="name" id="name"
                            required>
                    </div>

                    <div class="form-group" id="div-info"
                        style="background-color: #fff3cd;padding: 10px;border-radius: 7px;color: #664d03; font-family: Montserrat,sans-serif !important; font-size: 13px !important; font-weight: 300 !important; margin-top: 7px; margin-bottom: 7px; border: solid 1px #ffe69c; padding: 7px !important;">
                        <span><i class="bi bi-exclamation-circle"></i>&nbsp;<span id="info-footer">Informe seu telefone para
                                continuar.</span></span>
                    </div>

                    <button class="btn btn-block btn-primary" id="btn-checkout-action" onclick="checkCustomer()"
                        type="button" style="width: 100% !important;" ><strong id="btn-checkout" style="font-family: Montserrat,sans-serif !important; font-weight: 600 !important; ">Continuar </strong><span><i class="bi bi-arrow-right"></i></span></button>

                    <center>
                        <button class="btn btn-sm btn-outline-secondary mt-2 d-none" id="btn-alterar"
                            onclick="clearModal()">Alterar Telefone</button>
                    </center>
                    <input type="hidden" id="promo" name="promo">
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    $('#staticBackdrop').on('hide.bs.modal', function() {
        clearModal()
    })

    $('input.keydown').on('keydown', function(e) {
        var code = e.which || e.keyCode;

        if (code == 13) {
            event.preventDefault();
            checkCustomer()
        }
    });



    function checkCustomer() {
        var phone = $('#telephone1').val()
        if (phone == null || phone == '') {
            alert('Informe o telefone para continuar!');
            $('#telephone1').focus();
            return;
        } else if (phone.length < 15) {
            alert('Informe um telefone válido!');
            $('#telephone1').select();
            return;
        }

        loading()
        $.ajax({
            url: "{{ route('getCustomer') }}",
            type: 'POST',
            dataType: 'json',
            data: {
                "phone": phone
            },
            success: function(response) {
                loading()
                if (response.customer == null) {
                    novoCliente(phone);
                } else {
                    findCustomer(response.customer)
                }
            },
            error: function(error) {
                Swal.fire(
                    'Erro Desconhecido!',
                    '',
                    'error'
                )
            }
        })
    }

    function finalizarCompra() {
        $('#form-checkout').submit();
    }

    function findCustomer(customer) {
        document.getElementById('customer-name').innerHTML = customer.nome;
        document.getElementById('customer-phone').innerHTML = customer.telephone;
        document.getElementById('name').value = customer.nome;
        document.getElementById('phone-cliente').value = customer.telephone;

        document.getElementById('customer').value = customer.id;
        document.getElementById('div-customer').classList.toggle('d-none');
        document.getElementById('btn-checkout').innerHTML = 'Concluir reserva ';
        document.getElementById('btn-checkout-action').setAttribute("onclick", "loading()")
        document.getElementById('btn-checkout-action').type = 'submit'
        document.getElementById('btn-alterar').innerHTML = 'Alterar Conta';
        document.getElementById('btn-alterar').classList.remove('d-none');
        document.getElementById('div-info').classList.add('d-none');
        document.getElementById('div-telefone').classList.add('d-none');
    }

    function clearModal() {
        document.getElementById('telephone1').value = '';
        document.getElementById('telephone1').disabled = false;
        document.getElementById('div-nome').classList.add('d-none');
        document.getElementById('info-footer').innerHTML = 'Informe seu telefone para continuar.';
        document.getElementById('btn-checkout').innerHTML = 'Continuar';
        document.getElementById('btn-checkout-action').setAttribute("onclick", "checkCustomer()")
        document.getElementById('btn-alterar').classList.add('d-none');
        document.getElementById('btn-checkout-action').type = 'button'
        document.getElementById('phone-cliente').value = ''
        document.getElementById('customer').value = 0;
        document.getElementById('div-customer').classList.add('d-none');
        document.getElementById('div-info').classList.remove('d-none');
        document.getElementById('div-telefone').classList.remove('d-none');
    }

    function novoCliente(phone) {
        document.getElementById('telephone1').disabled = true;
        document.getElementById('div-nome').classList.toggle('d-none');
        document.getElementById('info-footer').innerHTML = 'Informe os dados corretos para recebimento das premiações.';
        document.getElementById('btn-checkout').innerHTML = 'Concluir cadastro e pagar ';
        document.getElementById('btn-checkout-action').setAttribute("onclick", "loading()")
        document.getElementById('btn-checkout-action').type = 'submit'
        document.getElementById('btn-alterar').classList.innerHTML = 'Alterar Telefone';
        document.getElementById('btn-alterar').classList.toggle('d-none');
        document.getElementById('phone-cliente').value = phone
        document.getElementById('customer').value = 0;
    }
</script>


<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true"
    style="z-index: 9999999; backdrop-filter: blur(10px);">
    <div class="modal-dialog">
        <div class="modal-content" style="border: none;">
            <div class="modal-header" style="background-color: #fff;">
                <h5 class="modal-title" id="exampleModalLabel" style="color: #333; font-family: Montserrat,sans-serif !important; font-size: 20px !important; font-weight: 500 !important;">Buscar compras</h5>
                
            </div>
            <div class="modal-body" style="background-color: #fff; border-radius: 10px;">
                <div class="row">
                    <div class="col-md-12">
                        <form action="{{ route('consultingReservation') }}" method="POST" style="display: block;">
                            {{ csrf_field() }}
                            <p style="color: #323232; font-family: Montserrat,sans-serif !important; font-weight: 300 !important; font-size: 16px; margin-bottom: 0px; "><strong>Informe seu telefone</strong></p>
                            <input type="hidden" name="productID" value="{{ $product[0]->id }}">
                            <input type="text" id="telephone3" name="telephone"
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
<!-- Modal -->
<div class="modal fade" id="exampleModal1" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content" style="border: none;">
            <div class="modal-header" style="background-color: #020f1e;">
                <h5 class="modal-title" id="exampleModalLabel" style="color: #fff;">DÚVIDAS FREQUENTES</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                    style="color: #fff;background-color: red!important;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="background-color: #020f1e;color: #ffffff;">
                <b style="text-transform: uppercase;">- É confiável?</b><br>
                <span style="color: #999999;">R: Sim, sorteio pela milhar da loteria federal.</span><br>
                <b style="text-transform: uppercase;">- Que dia é o sorteio?</b><br>
                <span style="color: #999999;">R: Após a venda de todas as cotas, no site você pode acompanhar as
                    vendas!</span><br>
                <b style="text-transform: uppercase;">- Como participar da nossa rifa?</b><br>
                <span style="color: #999999;">R: Existe duas formas compra automática e compra manual.</span><br>
                <b style="text-transform: uppercase;">- Forma de pagamento</b><br>
                <span style="color: #999999;">R: Somente PIX Copia e Cola ou CNPJ</span><br>
                <b style="text-transform: uppercase;">- Se eu escolher o veículo</b><br>
                <span style="color: #999999;">R: Vamos entregar na sua garagem o prêmio.</span>
            </div>
            <div class="modal-footer" style="background-color: #020f1e;">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>

{{-- Modal Premios --}}
<div class="modal fade" id="modal-premios" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true"
    style="z-index: 9999999;">
    <div class="modal-dialog">
        <div class="modal-content" style="border: none;">
            <div class="modal-header" style="background-color: #020f1e;">
                <h5 class="modal-title" id="exampleModalLabel" style="color: #fff;">PRÊMIOS</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"
                    style="color: #fff;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="">
                <div class="col-md-12 text-center">
                    Estes são os prêmios disponíveis no sorteio <strong>{{ $productModel->name }}</strong>
                </div>
                <hr>
                @foreach ($productModel->premios()->where('descricao', '!=', '') as $premio)
                    <div class="row mt-4">
                        <div class="col-md-12 text-center">
                            <label><strong>Prêmio {{ $premio->ordem }}: </strong>{{ $premio->descricao }}</label>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>



