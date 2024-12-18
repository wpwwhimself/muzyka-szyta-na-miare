@extends('layouts.mail', compact("title"))

@section('content')
    <h2>{{ $pl["kobieta"] ? "Szanowna Pani" : "Szanowny Panie" }} {{ $pl["imiewolacz"] }},</h2>
    <p>
        nie przyznałem {{ $pl["kobieta"] ? "Pani" : "Panu" }} zniżki za reklamę, ponieważ nie widzę {{ $pl["kobieta"] ? "Pani" : "Pana" }} posta wśród <a href="https://www.facebook.com/muzykaszytanamiarepl/reviews">recenzji na Facebooku</a>.
    </p>
    <p>
        Najczęstszym tego powodem jest nieprawidłowo ustawiona widoczność posta.
        Proszę zwrócić uwagę, żeby widoczność posta była ustawiona na <strong>Wszyscy</strong>.
    </p>
    <p>
        Proszę o zweryfikowanie napisanej recenzji i ponowne kliknięcie przycisku w <a href="{{ route('dashboard') }}">Panelu klienta</a>.
    </p>
@endsection
