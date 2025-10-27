@extends("layouts.mail")
@section("title", "Nowe zapytanie o podkład")

@section("content")

<x-client.contact-info :data="$data" />

<div class="flex down">
    <span><strong>Rodzaj zlecenia</strong>: {{ $data["quest_type_id"] }}</span>
    <span><strong>Tytuł</strong>: {{ $data["title"] }}</span>
    <span><strong>Wykonawca</strong>: {{ $data["artist"] }}</span>
</div>

<x-shipyard.ui.button
    label="Przejdź do zapytania"
    :icon="model_icon('requests')"
    :action="route('request', ['id' => $data['id']])"
    class="primary"
/>

@endsection
