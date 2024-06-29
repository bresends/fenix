<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <title>{{ config('app.name', 'Recurso de Prova') }}</title>
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
    <h1>Recurso Contra Gabarito Preliminar</h1>
    <p>Eu, {{$military->sei}}, ciente das regras e condições estabelecidas na Norma de Ensino nº 01 do Comando da
        Academia e Ensino Bombeiro Militar, em especial quanto
        ao que consta no Capítulo VIII, apresento recurso contra o gabarito preliminar da Prova
        de {{$record->discipline}} do

        @if (str_contains($record->user->platoon->value, 'CFO'))
            Curso de Formação de Oficiais - {{$record->user->platoon}}
        @else
            Curso de Formação de Praças - Pelotão {{$record->user->platoon}}
        @endif
        , conforme exposto abaixo:</p>


    <h3>Tipo de avaliação: <span>Avaliação {{$record->type}}</span></h3>
    <h3>Prova: <span>{{$record->exam}}</span></h3>
    <h3>Questão recursada: <span>{{$record->question}}</span></h3>
    <h3>Disciplina: <span>{{$record->discipline}}</span></h3>

    <h2>Fundamentação do Recurso / Argumentação:</h2>
    <p>{!! $record->motive !!}</p>

    <h2>Fontes Bibliográficas que embasam a argumentação:</h2>
    <p class="p-5 indent-14">{!! $record->bibliography !!}</p>

    <p class="text-right">
        Goiânia, {{ \Carbon\Carbon::parse($record->created_at)->translatedFormat('d \d\e F \d\e Y')}}</p>

    <p class="text-center name">{{$military->name}} - {{$military->rank}} {{$military->division}}</p>

    @if (str_contains($record->user->platoon->value, 'CFO'))
        <p class="text-center">Aluno de Curso de Formação de Oficiais</p>
    @else
        <p class="text-center">Aluno de Curso de Formação de Praças</p>
    @endif

</main>

</body>
</html>
