@extends('layouts.app')

@section('content')
<form action="{{ route("client-edit", ['id' => $client->id]) }}" method="POST">
    @csrf

    <div class="grid-2">
        <x-section title="Dane osobowe" icon="clipboard-user">
            <x-input type="text" name="client_name" label="Nazwisko" :value="_ct_($client->client_name)" />
            <div class="grid-2">
                <x-input type="email" name="email" label="Email" :value="_ct_($client->email)" :small="true" />
                <x-input type="phone" name="phone" label="Telefon" :value="_ct_($client->phone)" :small="true" />
                <x-input type="text" name="other_medium" label="Inna droga kontaktu" :value="_ct_($client->other_medium)" :small="true" />
                <x-select name="contact_preference" label="Preferencja kontaktowa" :value="_ct_($client->contact_preference)" :small="true" :options="$contact_preferences" />
            </div>
        </x-section>

        <x-section title="Dane klienta" icon="suitcase">
            <div class="grid-2">
                <x-select name="trust" label="Zaufanie" :value="$client->trust" :options="$trust_levels" :small="true" />
                <x-input type="number" name="budget" label="Budżet" :value="_c_($client->budget)" :small="true" step="0.01" />
                <x-input type="number" name="extra_exp" label="Dodatkowe doświadczenie" :value="$client->extra_exp" :small="true" />
                <x-input type="TEXT" name="default_wishes" label="Domyślne życzenia" :value="_ct_($client->default_wishes)" />
                <x-input type="TEXT" name="special_prices" label="Specjalne warunki cenowe" :value="_ct_($client->special_prices)" />
                <x-input type="url" name="external_drive" label="Link do chmury" :value="_ct_($client->external_drive)" />
                <x-input type="checkbox" name="is_forgotten" label="Zapomniany" :value="$client->is_forgotten" />
            </div>
        </x-section>

        <x-section title="Dane użytkownika" icon="address-card">
            <x-input type="text" name="password" label="Hasło" :value="_ct_($client->password)" :small="true" />
        </x-section>

        <x-section title="Reklama" icon="bullhorn">
            <x-select name="helped_showcasing" label="Status patrona" :value="$client->helped_showcasing" :options="$patron_levels" :small="true" />

            <h2>Przypięty komentarz</h2>
            <table>
                <thead>
                    <tr>
                        <th>Zlecenie</th>
                        <th>Komentarz</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($client->comments as $comment)
                    <tr {{ $comment->pinned ? "class='active'" : "" }}>
                        <td><a href="{{ route('quest', ['id' => $comment->re_quest_id]) }}">{{ $comment->re_quest?->song->full_title }}</a></td>
                        <td>{{ $comment->comment }}</td>
                        <td><input type="radio" name="pinned_comment_id" value="{{ $comment->id }}" {{ $comment->pinned ? "checked" : "" }} /></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </x-section>
    </div>

    <x-button action="submit" label="Popraw dane" icon="pencil" />
</form>
@endsection
