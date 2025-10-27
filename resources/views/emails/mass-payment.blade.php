@extends('layouts.mail')
@section("title", "Zarejestrowano wpłatę")

@section('content')

<h2>{{ $pl["kobieta"] ? "Szanowna Pani" : "Szanowny Panie" }} {{ $pl["imiewolacz"] }},</h2>
<p>
    otrzymałem od {{ $pl["kobieta"] ? "Pani" : "Pana" }} wpłatę dotyczącą zleceń:
</p>

@foreach ($quests as $quest)
<x-quests.tile :quest="$quest" />
@endforeach

@if (!($quest->user->notes->is_veteran || $quest->user->notes->trust == 1))
<p>
    Teraz może {{ $pl["kobieta"] ? "Pani" : "Pan" }} pobierać pliki związane ze zleceniami za pomocą odpowiednich przycisków w widoku zlecenia.
</p>
@endif

<p>
    Uprzejmie dziękuję za zaufanie i skorzystanie z moich usług.
</p>
<h3>
    Kliknij przycisk powyżej, aby zalogować się na swoje konto i zobaczyć szczegóły.
</h3>

<p>
    <i>
        Dla przypomnienia: hasło dostępu do {{ $pl["kobieta"] ? "Pani" : "Pana" }} konta to <b>{{ $quests[0]->user->notes->password }}</b>
    </i>
</p>

@endsection
