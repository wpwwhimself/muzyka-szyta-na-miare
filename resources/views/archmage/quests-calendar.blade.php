@extends("layouts.app")

@section('content')

<div class="grid-2">
    <section>
        <div class="section-header">
            <h1>
                <i class="fa-solid fa-calendar"></i>
                Grafik nadchodzących zleceń
            </h1>
        </div>
        <x-calendar :click-days="true" :suggest="false" :with-today="true" />
        <script>
        $(document).ready(() => {
            $("tr[date]").click((el)=>{
                $("#date").val($(el.currentTarget).attr("date"));
            });
        });
        </script>
    </section>

    <section>
        <div class="section-header">
            <h1>
              <i class="fa-solid fa-calendar-day"></i>
              Dni wolne
            </h1>
        </div>

        <form action="{{ route('qc-mod-free-day') }}" class="flex-right center">
            @csrf
            <x-input type="date" name="date" label="Dodaj dzień" :small="true" min="{{ \Carbon\Carbon::today()->format('Y-m-d') }}" />
            <input type="hidden" name="mode" value="add" />
            <x-button action="submit" label="Dodaj" icon="check" :small="true" />
        </form>

        <style>
        .day-tiles > a{
            background-color: var(--bg2);
            padding: 1em;
            border-radius: 1em;
        }
        @media screen and (max-width: 600px){
            .day-tiles{
                flex-direction: row;
            }
        }
        </style>
        <div class="flex-right center day-tiles">
        @forelse ($free_days as $day)
            <a href="{{ route('qc-mod-free-day', ['date' => $day->date->format("Y-m-d"), 'mode' => 'remove']) }}" class="flex-down center">
                <span>{{ $day->date->format("d.m") }}</span>
                <small class="ghost">{{ $day->date->addDay()->diffForHumans() }}</small>
            </a>
        @empty
            <span class="grayed-out">Brak wpisów</span>
        @endforelse
        </div>
    </section>
</div>
@endsection
