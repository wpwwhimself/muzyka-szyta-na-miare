@extends('layouts.app', ["title" => "Błąd serwera"])

@section('content')

<div id="error-page">
    <h1 class="error">500</h1>
    <p>Coś złego stało się na serwerze. To nie Twoja wina.</p>
    <p class="yellowed-out"><a href="mailto:{{ env("MAIL_MAIN_ADDRESS") }}">Wyślij do mnie maila</a> z informacją, że błąd występuje i jaka operacja była wykonywana tuż przed pojawieniem się tego błędu.</p>
</div>
@endsection
