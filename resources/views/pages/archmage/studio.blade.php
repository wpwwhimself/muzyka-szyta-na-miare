@extends("layouts.minimal")
@section("title", "Lista zleceń")
@section("subtitle", "Studio")

@section("content")

<x-extendo-block key="song"
    :header-icon="model_icon('quests')"
    title="Zlecenia na tapecie"
    extended="perma"
>
    <script src="{{ mix('js/react/studio.js') }}" defer></script>
    <div id="studio" style="width: 100%"></div>
</x-extendo-block>

<x-a :href="route('dashboard')">Wróć</x-a>

@endsection
