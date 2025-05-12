@extends('layouts.app', compact("title"))

@section('content')
<form action="{{ route("gig-price-rate-process") }}" method="POST">
    @csrf
    <input type="hidden" name="id" value="{{ $rate?->id }}">

    <x-section title="Dane stawki" icon="percent">
        <x-input type="text"
            name="label"
            label="Nazwa"
            :value="$rate?->label"
        />

        <x-input type="number"
            name="value"
            label="Stawka [zł/h]"
            :value="$rate?->value"
        />

        <x-button action="submit" name="action" value="save" label="Zatwierdź" icon="check" />
        @if ($rate)
        <x-button action="submit" name="action" value="delete" label="Usuń" icon="trash" />
        @endif
        <x-a :href="route('gig-price-rates')">Wróć</x-a>
    </x-section>
</form>
@endsection
