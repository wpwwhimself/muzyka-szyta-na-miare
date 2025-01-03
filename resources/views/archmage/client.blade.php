@extends('layouts.app')

@section('content')
<form action="{{ route("client-edit", ['id' => $client->id]) }}" method="POST">
    @csrf

    <div class="grid-2">
        <section>
            <div class="section-header">
                <h1><i class="fa-solid fa-clipboard-user"></i> Dane osobowe</h1>
            </div>
                <x-input type="text" name="client_name" label="Nazwisko" :value="_ct_($client->client_name)" />
                <div class="grid-2">
                    <x-input type="email" name="email" label="Email" :value="_ct_($client->email)" :small="true" />
                    <x-input type="phone" name="phone" label="Telefon" :value="_ct_($client->phone)" :small="true" />
                    <x-input type="text" name="other_medium" label="Inna droga kontaktu" :value="_ct_($client->other_medium)" :small="true" />
                    <x-select name="contact_preference" label="Preferencja kontaktowa" :value="_ct_($client->contact_preference)" :small="true" :options="$contact_preferences" />
                </div>
        </section>
        <section>
            <div class="section-header">
                <h1><i class="fa-solid fa-suitcase"></i> Dane klienta</h1>
            </div>
            <div class="grid-2">
                <x-select name="trust" label="Zaufanie" :value="$client->trust" :options="$trust_levels" :small="true" />
                <x-select name="helped_showcasing" label="Status patrona" :value="$client->helped_showcasing" :options="$patron_levels" :small="true" />
                <x-input type="number" name="budget" label="Budżet" :value="_c_($client->budget)" :small="true" step="0.01" />
                <x-input type="number" name="extra_exp" label="Dodatkowe doświadczenie" :value="$client->extra_exp" :small="true" />
                <x-input type="TEXT" name="default_wishes" label="Domyślne życzenia" :value="_ct_($client->default_wishes)" />
                <x-input type="TEXT" name="special_prices" label="Specjalne warunki cenowe" :value="_ct_($client->special_prices)" />
                <x-input type="url" name="external_drive" label="Link do chmury" :value="_ct_($client->external_drive)" />
            </div>
        </section>
        <section>
            <div class="section-header">
                <h1><i class="fa-solid fa-address-card"></i> Dane użytkownika</h1>
            </div>
                <x-input type="text" name="password" label="Hasło" :value="_ct_($client->password)" :small="true" />
        </section>
    </div>

    <x-button action="submit" label="Popraw dane" icon="pencil" />
</form>
@endsection
