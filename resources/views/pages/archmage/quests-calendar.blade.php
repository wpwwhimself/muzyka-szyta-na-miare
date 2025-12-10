@extends("layouts.app")
@section("title", "Kalendarz zleceń i dni wolne")

@section('content')

<div class="grid but-mobile-down" style="--col-count: 2;">
    <x-section title="Dni wolne" :icon="model_icon('calendar-free-days')">
        <form action="{{ route('qc-mod-free-day') }}" class="flex right center">
            @csrf
            <x-input type="date" name="date" label="Dodaj dzień" :small="true" min="{{ \Carbon\Carbon::today()->format('Y-m-d') }}" />
            <input type="hidden" name="mode" value="add" />
            <x-shipyard.ui.button class="primary" action="submit" label="Dodaj" icon="check" />
        </form>

        <div class="flex right but-mobile-down center day-tiles">
        @forelse ($free_days as $day)
            <a href="{{ route('qc-mod-free-day', ['date' => $day->date->format("Y-m-d"), 'mode' => 'remove']) }}" class="flex down center middle no-gap rounded padded backdropped">
                <span>{{ $day->date->format("d.m") }}</span>
                <small class="ghost">{{ $day->date->addDay()->diffForHumans() }}</small>
            </a>
        @empty
            <span class="grayed-out">Brak wpisów</span>
        @endforelse
        </div>

        <p>Najbliższy dzień pracujący: <b>{{ get_next_working_day()->format("d.m.Y") }}</b></p>
    </x-section>

    <x-section title="Grafik nadchodzących zleceń" icon="calendar">
        <x-calendar :click-days="true" :suggest="false" :with-today="true" />
        <script>
        function handleCalendarClick(date) {
            document.querySelector("#date").value = date;
        }
        </script>
    </x-section>
</div>
@endsection
