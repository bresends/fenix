<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <title>{{ config('app.name', 'Solicitação de segunda chamada') }}</title>
    <meta charset="utf-8">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet"/>

    <!-- Scripts -->
    <link href="{{ public_path('css/pdf.css') }}" rel="stylesheet">

</head>
<body class="font-sans antialiased">

<nav class="nav-container">
    <img src="{{url('/images/logo.png')}}" alt="Image"/>
    <p>Estado de Goiás</p>
    <p>Corpo de Bombeiros Militar</p>
</nav>

<main>
    <h1>{{$record->user->platoon}}</h1>
    <h1>Solicitação de Segunda Chamada de Avaliação</h1>
    <p>Eu, {{$military->sei}}, ciente das regras e condições estabelecidas na Norma de Ensino nº 01 do Comando da
        Academia e Ensino Bombeiro Militar, apresento solicitação para realização de segunda chamada da disciplina
        de {{$record->discipline_name}} do

        @if (str_contains($record->user->platoon->value, 'CFO'))
            Curso de Formação de Oficiais - {{$record->user->platoon}},
        @elseif(str_contains($record->user->platoon->value, 'CHOA'))
            Curso de Habilitação de Oficiais de Administração - {{$record->user->platoon}},
        @else
            Curso de Formação de Praças - Pelotão {{$record->user->platoon}},
        @endif
        conforme exposto abaixo:</p>


    <h3>Tipo de avaliação: <span>Avaliação {{$record->type}}</span></h3>
    <h3>Disciplina: <span>{{$record->discipline_name}}</span></h3>
    <h3>Data da avaliação:
        <span>{{ \Carbon\Carbon::parse($record->exam_date)->translatedFormat('d \d\e F \d\e Y')}}</span></h3>
    <h3>Data em que ficou apto para realizar a avaliação:
        <span>{{ \Carbon\Carbon::parse($record->date_back)->translatedFormat('d \d\e F \d\e Y')}}</span></h3>

    <h2>Fundamentação dos motivos</h2>
    <p>{!! $record->motive !!}</p>

    <p class="text-right">
        Goiânia, {{ \Carbon\Carbon::parse($record->created_at)->translatedFormat('d \d\e F \d\e Y')}}</p>

    <p style="text-transform:uppercase" class="text-center name">{{$military->name}}
        - {{$military->rank}} {{$military->division}}</p>

    @if (str_contains($record->user->platoon->value, 'CFO'))
        <p class="text-center">Aluno do Curso de Formação de Oficiais</p>
    @elseif(str_contains($record->user->platoon->value, 'CHOA'))
        <p class="text-center">Aluno do Curso de Habilitação de Oficiais de Administração</p>
    @else
        <p class="text-center">Aluno do Curso de Formação de Praças</p>
    @endif

</main>

</body>
</html>
