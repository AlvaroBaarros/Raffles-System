@extends('layouts.admin')


<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
    integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw=="
    crossorigin="anonymous" referrerpolicy="no-referrer" />
@section('content')
    <style>
        .content-wrapper 
            { 
            background-color: #f5f5f5 !important;
            
        }
        section { 
            background-color: #f5f5f5 !important;
            
        }
        .dashboard-itens {
            display: flex;
            justify-content: left;
        }

        .dashboard-item {
            position: relative;
            width: 23%;
            height: 135px;
            background-color: #292727;
            border-radius: 10px;
            cursor: pointer;
        }

        @media (max-width: 768px) {
            .dashboard-itens {
                flex-direction: column;
            }

            .dashboard-item {
                width: 100%;
                margin-bottom: 20px;
            }
        }



        .dashboard-item.profit {
            background-color: #0966cf73;
            border: 1px solid #0966cf73;
        }

        .dashboard-item.request {
            background-color: #fff;
            border: none;
            
        }

        .dashboard-item.pending_request {
            background-color: #fff;
            border: none;
        }

        .dashboard-item.pending_entry {
            background-color: #fff;
           
            border: none;
            
        }

        .dashboard-item-body {
            padding: 10px;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            font-size: 1.5rem;
            
        }

        .dashboard-item-body p {
            margin-right: 10px;
            color: #f5f5f5;
            box-sizing: border-box;
            font-family: 'Montserrat', sans-serif;

        }

        .dashboard-item-body p:first-child {
            font-size: 1.1rem;
            margin-bottom: 5px;
        }

        .dashboard-item-body p:nth-child(2) {
            font-size: 1.1rem;
            margin-bottom: 5px;
            font-weight: bold;
        }

        .dashboard-item-body i {
            position: absolute;
            font-size: 6rem;
            top: 20px;
            right: 11px;
            opacity: .2;
            color: #fff;
            
        }

        .blink {
            margin-top: 5px;
            animation: animate 1.5s linear infinite;
        }

        @keyframes animate {
        0% {
            opacity: 0;
        }

        50% {
            opacity: 0.7;
        }

        100% {
            opacity: 0;
        }
    }
    </style>
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                
            </div>
        </div><!-- /.container-fluid -->
    </section>



    <section class="content" >
        <div class="container-fluid" >
            <div class="dashboard-itens" >
                

                <div class="dashboard-item request block-copy" onclick="link('{{ route('resumo.rifasAtivas') }}')" style="margin-right: 10px; height: 90px;">
                    <div class="dashboard-item-body" >
                        <p style="color: #333;">Rifas Ativas</p>
                        <p style="color: #333;">{{ $rifas->count() }}</p>
                        <i class="fa-solid fa-receipt blink" style="font-size: 35px; color: #a002d1;"></i>
                    </div>
                </div>

                <div class="dashboard-item pending_request block-copy" onclick="link('{{ route('resumo.pendentes') }}')" style="margin-right: 10px; height: 90px;">
                    <div class="dashboard-item-body">
                        <p style="color: #333;">Pedidos Pendentes</p>
                        <p style="color: #333;">{{ $participantes->where('reservados', '>', 0)->count() }}</p>
                        <i class="fa-solid fa-hourglass blink" style="font-size: 35px; color: #a002d1;"></i>
                    </div>
                </div>

                <div class="dashboard-item pending_entry block-copy" onclick="link('{{ route('resumo.ranking') }}')" style=" height: 90px;">
                    <div class="dashboard-item-body">
                        <p style="color: #333;">Ranking</p>
                        {{-- <p style="color: #333;">R$ {{ number_format($participantes->where('reservados', '>', 0)->sum('valor'), 2, ",", ".") }}</p> --}}
                        <i class="fas fa-medal blink" style="font-size: 35px; color: #a002d1;"></i>
                    </div>
                </div>
            </div>
        </div>
        </div>
    @endsection

    <script>
        function link(url) {
            window.location.href = url;
        }
    </script>
