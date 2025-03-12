@extends("layouts.app", ["stripped" => true])

@section("content")

<x-extendo-block key="song"
    header-icon="boxes"
    title="Zlecenia na tapecie"
    extended="perma"
>
    <script src="{{ mix('js/react/studio.js') }}" defer></script>
    <div id="studio" class="no-shrinking" style="width: 100%"></div>
</x-extendo-block>

<x-a :href="route('dashboard')">Wróć</x-a>

@endsection
