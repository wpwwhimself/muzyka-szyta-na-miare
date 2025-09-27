@extends("layouts.app")

@section("content")

<x-section title="Ustawienia domyślne" icon="cog">
    <form action="{{ route('gig-price-process-defaults') }}" method="POST">
        @csrf

        <div class="flex-right center">
            @foreach ($defaults as $setting)
            <x-input type="number" step="0.01"
                :name="$setting->name"
                :label="$setting->label"
                :value="$setting->value"
                small
            />
            @endforeach
        </div>

        <div class="flex-right">
            <x-button action="submit" icon="check" label="Zapisz" />
        </div>
    </form>
</x-section>

<x-a :href="route('gig-price-suggest')">Wróć</x-a>

@endsection