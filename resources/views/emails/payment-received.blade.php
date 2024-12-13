@extends('layouts.mail', compact("title"))

@section('content')
    <h2>{{ $pl["kobieta"] ? "Szanowna Pani" : "Szanowny Panie" }} {{ $pl["imiewolacz"] }},</h2>
    <p>
        otrzymałem od {{ $pl["kobieta"] ? "Pani" : "Pana" }} wpłatę dotyczącą zlecenia:
    </p>

    <x-mail-quest-mini :quest="$quest" />

    @if ($paymentShouldBeDelayed)
    <p>
        Chciałbym jednak zauważyć, że z uwagi na limity wpłat, jakie muszę spełniać, <strong>poprosiłem o wpłatę po {{ $quest->delayed_payment->format("d.m.Y") }}</strong>.
        Bardzo proszę o zwracanie uwagi na informacje podane na zleceniach w przyszłości.
    </p>
    <p>
        Dodatkowo będę wdzięczny za każdą informację, która poprawi czytelność tego komunikatu.
    </p>
    @endif

    @if (!($quest->client->is_veteran || $quest->client->trust == 1))
    <p>
        Teraz może {{ $pl["kobieta"] ? "Pani" : "Pan" }} pobierać pliki związane ze zleceniem za pomocą odpowiednich przycisków w widoku zlecenia.
    </p>
    @endif

    <h3>
        Kliknij
        <a
            class="button"
            href="{{ route('quest', ['id' => $quest->id]) }}"
            >
            tutaj,
        </a>
        aby zobaczyć szczegóły zlecenia
    </h3>

    <p>
        {{ $paymentShouldBeDelayed ? "Niemniej" : "Uprzejmie" }} dziękuję za zaufanie i skorzystanie z moich usług.
    </p>
    <p>
        <i>
            Dla przypomnienia: hasło dostępu do {{ $pl["kobieta"] ? "Pani" : "Pana" }} konta to <b>{{ $quest->client->user->password }}</b>
        </i>
    </p>
@endsection
