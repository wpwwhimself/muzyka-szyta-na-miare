@extends('layouts.app', ["title" => ($platform) ? "{$platform->name} | Edycja platformy" : "Nowa platforma"])

@section('content')

<form action="{{ route('showcase-platform-process') }}" method="POST">
    @csrf

    <x-section title="Dane platformy" icon="hashtag">
        <x-input type="text"
            name="code"
            label="Kod"
            :value="$platform?->code"
            small
        />

        <x-input type="text"
            name="name"
            label="Nazwa"
            :value="$platform?->name"
        />

        <x-input type="text"
            name="icon_class"
            label="Ikona"
            :value="$platform?->icon_class"
            small
        />
        {!! $platform?->icon !!}

        <x-input type="number"
            name="orderign"
            label="Kolejność"
            :value="$platform?->ordering"
            small
        />

        <x-button action="submit" name="action" value="save" label="Zatwierdź" icon="check" />
        @if ($platform)
        <x-button action="submit" danger name="action" value="delete" label="Usuń" icon="trash" />
        @endif
        <x-a :href="route('showcase-platforms')" icon="angles-left">Wróć</x-a>
    </x-section>
</form>

@endsection
