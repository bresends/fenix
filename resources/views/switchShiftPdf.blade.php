<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <title>{{ config('app.name', 'Certidão de Troca de Serviço') }}</title>
    <meta charset="utf-8">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet"/>

    <!-- Styles -->
    <link href="{{ public_path('css/pdf.css') }}" rel="stylesheet">

</head>
<body class="font-sans antialiased">
@if($record->status->value === 'Deferido')
    <nav class="nav-container">
        <img src="{{ url('/images/logo.png') }}" alt="Logo"/>
        <p>Estado de Goiás</p>
        <p>Corpo de Bombeiros Militar</p>
    </nav>

    <main>
        <h1 class="text-center">Certidão de Troca de Serviço</h1>

        <p class="text-right">Goiânia, {{ \Carbon\Carbon::now()->translatedFormat('d \d\e F \d\e Y') }}</p>

        <p>
            Certifico que o(a) aluno(a) {{$military->sei}},
            lotado(a) no Comando da Academia e Ensino Bombeiro Militar, teve sua solicitação de troca de serviço
            DEFERIDA conforme as
            informações abaixo:
        </p>

        <h2>Serviço em que será substituído</h2>
        <ul>
            <li><strong>Data e
                    hora:</strong> {{ \Carbon\Carbon::parse($record->first_shift_date)->translatedFormat('d \d\e F \d\e Y') }}
            </li>
            <li><strong>Local:</strong> {{ $record->first_shift_place }}</li>
            <li><strong>Substituído:</strong> {{ $receiving_military->sei }}</li>
            <li><strong>Substituto:</strong> {{ $asking_military->sei }}</li>
        </ul>

        <h2>Serviço em que será substituto</h2>
        <ul>
            <li><strong>Data e
                    hora:</strong> {{ \Carbon\Carbon::parse($record->second_shift_date)->translatedFormat('d \d\e F \d\e Y') }}
            </li>
            <li><strong>Local:</strong> {{ $record->second_shift_place }}</li>
            <li><strong>Substituído:</strong> {{ $asking_military->sei }}</li>
            <li><strong>Substituto:</strong> {{ $receiving_military->sei }}</li>
        </ul>

        <h3>Tipo da escala: <span>{{ $record->type }}</span></h3>

        <h2>Parecer da Coordenação</h2>
        <p><strong>Status:</strong> {{ $record->status }}</p>
        @if($record->final_judgment_reason)
            <p><strong>Observações:</strong> {!! $record->final_judgment_reason !!}</p>
        @endif
        <p><strong>Autorizado por:</strong> {{ $evaluated_by->sei }}</p>
        <p>
            <strong>Data da
                autorização:</strong> {{ \Carbon\Carbon::parse($record->evaluated_at)->translatedFormat('d \d\e F \d\e Y') }}
        </p>

        <br/>

        <p class="text-center" style="text-transform: uppercase;">{{ $record->evaluator->name }}
            - {{ $evaluated_by->rank }} {{ $evaluated_by->division }}</p>
        <p class="text-center">Coordenação CAEBM</p>

    </main>
@endif

</body>
</html>
