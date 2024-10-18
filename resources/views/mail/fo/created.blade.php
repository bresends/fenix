{{--@formatter:off--}}
<x-mail::layout>

<x-slot:header>
<x-mail::header url="{{config('app.url')}}">
Fenix
</x-mail::header>
</x-slot:header>

# Notificação de FO

Olá!
Você está recebendo este e-mail porque recebeu um FO.

**FO n°:** {{ $fo->id }}

**Tipo:** {{ $fo->type->value }}

**Data:** {{ $fo->created_at->format('d/m/Y H:i') }}

**Motivo:** {{ $fo->reason }}

Use o link abaixo para visualizar o FO:

<x-mail::button :url="$url" color="primary">
Visualizar FO
</x-mail::button>

<x-mail::panel>
**Atenção:** Conforme o Art. 61 da NE-03, você tem até 1 hora antes do término do
expediente do dia útil seguinte ao recebimento deste FO para apresentar justificativa ou dar ciência.
</x-mail::panel>

<x-slot:subcopy>
<x-mail::subcopy>
Se você estiver tendo problemas para clicar no botão "Visualizar FO", copie e cole a URL abaixo no seu
navegador: <a href="{{ $url }}">{{ $url }}</a>
</x-mail::subcopy>
</x-slot:subcopy>

Atenciosamente,<br>
CAEBM

<x-slot:footer>
<x-mail::footer>
<a href="{{ config('app.url') }}/unsubscribe">Clique aqui para se desinscrever desses emails.</a>
</x-mail::footer>
</x-slot:footer>
</x-mail::layout>
