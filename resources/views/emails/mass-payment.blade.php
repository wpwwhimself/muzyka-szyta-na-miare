@extends('layouts.mail', compact("title"))

@section('content')
    <h2>{{ $pl["kobieta"] ? "Szanowna Pani" : "Szanowny Panie" }} {{ $pl["imiewolacz"] }},</h2>
    <p>
        otrzymałem od {{ $pl["kobieta"] ? "Pani" : "Pana" }} wpłatę dotyczącą zleceń:
    </p>

    @foreach ($quests as $quest)
    <x-mail-quest-mini :quest="$quest" />
    @endforeach

    @if (!($quest->client->is_veteran || $quest->client->trust == 1))
    <p>
        Teraz może {{ $pl["kobieta"] ? "Pani" : "Pan" }} pobierać pliki związane ze zleceniami za pomocą odpowiednich przycisków w widoku zlecenia.
    </p>
    @endif

    <p>
        Uprzejmie dziękuję za zaufanie i skorzystanie z moich usług.
    </p>
    <h3>
        Kliknij
        <a
            class="button"
            href="{{ route('dashboard') }}"
            >
            tutaj,
        </a>
        aby zalogować się na swoje konto.
    </h3>

    <p>
        <i>
            Dla przypomnienia: hasło dostępu do {{ $pl["kobieta"] ? "Pani" : "Pana" }} konta to <b>{{ $quests[0]->client->user->password }}</b>
        </i>
    </p>
@endsection
