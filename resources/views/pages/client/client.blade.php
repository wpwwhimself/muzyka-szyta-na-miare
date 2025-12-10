@extends('layouts.app')
@section("title", $client->notes->client_name)
@section("subtitle", "Dane klienta")

@section('content')
<x-shipyard.app.form
    :action="route('client-edit', ['id' => $client->id])"
    method="POST"
>
    <x-slot:actions>
        <x-shipyard.ui.button class="primary" action="submit" label="Popraw dane" icon="pencil" />
    </x-slot:actions>

    <div class="grid but-mobile-down" style="--col-count: 2;">
        <x-section title="Dane osobowe" :icon="model_icon('user_notes')">
            <div class="grid but-halfsize-down" style="--col-count: 2;">
                @foreach ([
                    "client_name",
                    "email",
                    "phone",
                    "other_medium",
                    "contact_preference",
                ] as $field_name)
                <x-shipyard.ui.field-input :model="$client->notes" :field-name="$field_name" />
                @endforeach
            </div>
        </x-section>

        <x-section title="Dane użytkownika" :icon="model_icon('users')">
            <x-shipyard.ui.field-input :model="$client->notes" field-name="password" dummy />
            <p>Hasło jest nadawane przeze mnie odgórnie, nie można go zmienić, chyba że nastąpią jakieś komplikacje z logowaniem.</p>
        </x-section>
    </div>
</x-shipyard.app.form>
@endsection
