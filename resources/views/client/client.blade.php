@extends('layouts.app')

@section('content')
<form action="{{ route("client-edit", ['id' => $client->id]) }}" method="POST">
    @csrf
    <section>
        <div class="section-header">
            <h1><i class="fa-solid fa-clipboard-user"></i> Dane osobowe</h1>
        </div>
            <x-input type="text" name="client_name" label="Imię i nazwisko" :value="$client->client_name" />
            <div class="grid-2">
                <x-input type="email" name="email" label="Email" :value="$client->email" :small="true" />
                <x-input type="phone" name="phone" label="Telefon" :value="$client->phone" :small="true" />
                <x-input type="text" name="other_medium" label="Inna droga kontaktu" :value="$client->other_medium" :small="true" />
                <x-input type="text" name="contact_preference" label="Preferencja kontaktowa" :value="$client->contact_preference" :small="true" />
            </div>
    </section>
    <section>
        <div class="section-header">
            <h1><i class="fa-solid fa-address-card"></i> Dane użytkownika</h1>
        </div>
            <x-input type="text" name="password" label="Hasło" :value="$client->user->password" :small="true" :disabled="true" />
            <p>Hasło jest nadawane przeze mnie odgórnie, nie można go zmienić, chyba że nastąpią jakieś komplikacje z logowaniem.</p>
    </section>
    <x-button action="submit" label="Popraw dane" icon="pencil" />
</form>
@endsection
