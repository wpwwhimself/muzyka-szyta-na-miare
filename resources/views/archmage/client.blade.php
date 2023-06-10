@extends('layouts.app')

@section('content')
<form action="{{ route("client-edit", ['id' => $client->id]) }}" method="POST">
    @csrf
    <section>
        <div class="section-header">
            <h1><i class="fa-solid fa-clipboard-user"></i> Dane osobowe</h1>
        </div>
            <x-input type="text" name="client_name" label="Nazwisko" :value="$client->client_name" />
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
            <x-input type="text" name="password" label="Hasło" :value="$client->user->password" :small="true" />
    </section>
    <section>
        <div class="section-header">
            <h1><i class="fa-solid fa-suitcase"></i> Dane klienta</h1>
        </div>
        <div class="grid-2">
            <x-input type="number" name="trust" label="Zaufanie" :value="$client->trust" :small="true" />
            <x-input type="number" name="helped_showcasing" label="Status patrona" :value="$client->helped_showcasing" :small="true" />
            <x-input type="number" name="budget" label="Budżet" :value="$client->budget" :small="true" />
            <x-input type="number" name="extra_exp" label="Dodatkowe doświadczenie" :value="$client->extra_exp" :small="true" />
            <x-input type="TEXT" name="default_wishes" label="Domyślne życzenia" :value="$client->default_wishes" />
            <x-input type="TEXT" name="special_prices" label="Specjalne warunki cenowe" :value="$client->special_prices" />
        </div>
    </section>
    <x-button action="submit" label="Popraw dane" icon="pencil" />
</form>
@endsection
