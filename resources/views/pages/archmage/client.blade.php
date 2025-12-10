@extends('layouts.app')
@section("title", $client->notes->client_name)
@section("subtitle", "Dane klienta")

@section('content')
<x-shipyard.app.form
    :action="route('client-edit', ['id' => $client->id])"
    method="POST"
>
    <x-slot:actions>
        <x-shipyard.ui.button action="submit" class="primary" label="Popraw dane" icon="pencil" />
    </x-slot:actions>

    <div class="grid but-mobile-down" style="--col-count: 2;">
        <x-section title="Dane osobowe" :icon="model_icon('user_notes')">
            <div class="grid but-mobile down" style="--col-count: 2;">
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

        <x-shipyard.app.section title="Statystyki" icon="finance">
            <div class="flex right center middle">
                @foreach ([
                    ["Pierwsze zlecenie", "star", $client->created_at->format("Y-m-d")],
                    ["Doświadczenie", "abacus", $client->notes->exp],
                    ["Zlecenia w toku", model_icon('quests'), $client->notes->upcoming_quests_count ?? 0],
                ] as [$label, $icon, $value])
                <x-shipyard.app.icon-label-value
                    :icon="$icon"
                    :label="$label"
                >
                    {{ $value }}
                </x-shipyard.app.icon-label-value>
                @endforeach

                <x-shipyard.app.model.badges :badges="$client->notes->badges" />
            </div>
        </x-shipyard.app.section>
    </div>
</x-shipyard.app.form>
@endsection
