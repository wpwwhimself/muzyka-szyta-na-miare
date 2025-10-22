@extends('layouts.app')
@section("title", $client->notes->client_name)
@section("subtitle", "Dane klienta")

@section('content')
<x-shipyard.app.form
    :action="route('client-edit', ['id' => $client->id])"
    method="POST"
>
    <x-slot:actions>
        <x-button action="submit" label="Popraw dane" icon="pencil" />
    </x-slot:actions>

    <div class="grid but-mobile-down" style="--col-count: 2;">
        <x-section title="Dane osobowe" :icon="model_icon('user_notes')">
            <div class="grid but-mobile down" style="--col-count: 2;">
                @foreach ([
                    "client_name",
                    "email",
                    "phone",
                    "other_medium",
                ] as $field_name)
                <x-shipyard.ui.field-input :model="$client->notes" :field-name="$field_name" />
                @endforeach
            </div>
            
            @foreach ([
                "trust",
                "budget",
                "extra_exp",
                "default_wishes",
                "special_prices",
                "external_drive",
                "is_forgotten",
            ] as $field_name)
            <x-shipyard.ui.field-input :model="$client->notes" :field-name="$field_name" />
            @endforeach
        </x-section>

        <x-section title="Dane użytkownika" :icon="model_icon('users')">
            <x-shipyard.ui.field-input :model="$client->notes" field-name="password" />
        </x-section>

        <x-section title="Reklama" icon="bullhorn">
            <x-shipyard.ui.field-input :model="$client->notes" field-name="helped_showcasing" />

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
</x-shipyard.app.form>
@endsection
