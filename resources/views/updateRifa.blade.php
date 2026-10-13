@extends('layouts.admin')

@section('content')
    <div class="container">
        <center>
            <h3>Atualizar Rifas</h3>
        </center>
        @foreach ($rifas as $rifa)
            <div class="row" id="div-rifa-{{ $rifa->id }}">
                <p>{{ $rifa->name }}</p>
                <span id="info-part-{{ $rifa->id }}"></span>
                <button class="btn btn-sm btn-secondary" data-rifa="{{ $rifa->id }}"
                    onclick="refreshRaffle(this)">Atualizar Rifa</button>
                <button class="btn btn-sm btn-success" data-rifa="{{ $rifa->id }}"
                    onclick="update(this)">Zerar</button>
                <button class="btn btn-sm btn-info" data-rifa="{{ $rifa->id }}"
                    data-participantes="{{ $rifa->participantesArray(1000000) }}" onclick="updatePart(this)">Participantes</button>
                <button class="btn btn-sm btn-danger" data-rifa="{{ $rifa->id }}"
                    onclick="remover(this)">Remover</button>
                <hr>
                <hr>
            </div>
        @endforeach
    </div>

    <script>
        function refreshRaffle(el) {
            el.disabled = true;
            el.innerHTML = 'Procesando...';

            var rifa = el.dataset.rifa;

            $.ajax({
                url: "/atualizar-rifa/" + rifa,
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        el.innerHTML = 'SUCESSO!';
                    }
                    else{
                        alert('erro');
                    }
                },
                error: function(error) {

                }
            })
        }

        function update(el) {
            el.disabled = true;
            el.innerHTML = 'Procesando...';

            var rifa = el.dataset.rifa;

            $.ajax({
                url: "/refresh-only-raffle/" + rifa,
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        el.innerHTML = 'SUCESSO!';
                    }
                },
                error: function(error) {

                }
            })
        }

        async function updatePart(el) {
            el.disabled = true;
            el.innerHTML = 'Procesando...';

            var rifa = el.dataset.rifa;
            var divRifa = document.getElementById(`div-rifa-${rifa}`)
            var info = document.getElementById(`info-part-${rifa}`);

            var participantes = el.dataset.participantes;
            participantes = participantes.split(",")

            var contador = 0;
            info.innerHTML = `Atualizando ${contador} de ${participantes.length}`

            for (const item of participantes) {
                await $.ajax({
                    url: "/refresh-participante/" + item,
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            contador++;
                            info.innerHTML = `Atualizando ${contador} de ${participantes.length}`
                        } else {
                            alert('erro ao atualizar participante ' + element);
                        }
                    },
                    error: function(error) {

                    }
                });
            }


            // participantes.forEach(element => {
            //     $.ajax({
            //         url: "/refresh-participante/" + element,
            //         type: 'GET',
            //         dataType: 'json',
            //         success: function(response) {
            //             if (response.success) {
            //                 contador++;
            //                 info.innerHTML = `Atualizando ${contador} de ${participantes.length}`
            //             } else {
            //                 alert('erro ao atualizar participante ' + element);
            //             }
            //         },
            //         error: function(error) {

            //         }
            //     });
            // });

            el.innerHTML = 'FINALIZADO !!';
            //divRifa.remove();
        }

        function remover(el) {
            var rifa = el.dataset.rifa;
            var divRifa = document.getElementById(`div-rifa-${rifa}`)
            divRifa.remove()
        }
    </script>
@endsection

{{-- $.ajax({
    url: "/refresh-only-raffle/" + rifa,
    type: 'POST',
    dataType: 'json',
    data: {
        "id": idRifa
    },
    success: function(response) {

    },
    error: function(error) {
        Swal.fire(
            'Erro!',
            error,
            'danger'
        )
    }
})
} --}}
