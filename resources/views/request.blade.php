@extends('layouts.app', compact("title"))

@section('content')
@foreach (["success", "error"] as $status)
@if (session($status))
    <x-alert :status="$status" />
@endif
@endforeach

<form method="POST" action="{{ route("mod-request-back") }}">
    @csrf
    <h1>Szczegóły zapytania</h1>
    <x-phase-indicator :status-id="$request->status_id" />
    <div class="grid-3">
        <section class="">
            <h2><i class="fa-solid fa-user"></i> Dane klienta</h2>
            @if (Auth::id() != 1)
            <x-input type="text" name="client_name" label="Nazwisko/Nazwa" :autofocus="true" :required="true" :disabled="true" value="{{ Auth::user()->client->client_name }}" />
            <x-input type="email" name="email" label="Adres e-mail" :disabled="true" value="{{ Auth::user()->client->email }}" />
            <x-input type="tel" name="phone" label="Numer telefonu" :disabled="true" value="{{ Auth::user()->client->phone }}" />
            <x-input type="text" name="other_medium" label="Inna forma kontaktu" :disabled="true" value="{{ Auth::user()->client->other_medium }}" />
            <x-input type="text" name="contact_preference" label="Preferencja kontaktowa" placeholder="email" :disabled="true" value="{{ Auth::user()->client->contact_preference }}" />
            @else
            <x-input type="text" name="client_name" label="Nazwisko/Nazwa" :autofocus="true" :required="true" value="{{ $request->client->client_name ?? $request->client_name }}" />
            <x-input type="email" name="email" label="Adres e-mail" value="{{ $request->client->email ?? $request->email }}" />
            <x-input type="tel" name="phone" label="Numer telefonu" value="{{ $request->client->phone ?? $request->phone }}" />
            <x-input type="text" name="other_medium" label="Inna forma kontaktu" value="{{ $request->client->other_medium ?? $request->other_medium }}" />
            <x-input type="text" name="contact_preference" label="Preferencja kontaktowa" placeholder="email" value="{{ $request->client->contact_preference ?? $request->contact_preference }}" />
            @endif
        </section>
        <section class="">
            <h2><i class="fa-solid fa-cart-flatbed"></i> Dane zlecenia</h2>
            <x-input type="text" name="title" label="Tytuł utworu" value="{{ $request->title }}" />
            <x-input type="text" name="artist" label="Oryginalny wykonawca" value="{{ $request->artist }}" />
            <x-input type="text" name="cover_artist" label="Coverujący" value="{{ $request->cover_artist }}" />
            <x-input type="url" name="link" label="Link do nagrania" value="{{ $request->link }}" />
            <x-input type="TEXT" name="wishes" label="Życzenia" value="{{ $request->wishes }}" />
        </section>
        <section class="">
            <h2><i class="fa-solid fa-sack-dollar"></i> Wycena</h2>
            @if (Auth::id() == 1)
            <x-input type="text" name="price" label="Wycena (kod lub kwota)" :hint="$prices" />
            @endif
            <p>Tu będzie przekalkulowana wycena</p>
            <x-input type="date" name="deadline" label="Termin wykonania" />
            @if (Auth::id() == 1)
            <x-input type="checkbox" name="hard_deadline" label="Termin narzucony przez klienta" />
            @endif
        </section>
    </div>
    <button type="submit" class="hover-lift">
        <i class="fa-solid fa-paper-plane"></i> Popraw i oddaj do wyceny
    </button>
</form>
@endsection
