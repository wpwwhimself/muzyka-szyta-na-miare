@extends('layouts.app', compact("title", "extraCss"))

@section('content')
    @foreach (["success", "error"] as $status)
        @if (session($status))
            <div class="alert {{ $status }}">
                {{ session($status) }}
            </div>
        @endif
    @endforeach
    <section id="dashboard-quests">
        <div class="section-header">
            <h1>Aktualne zlecenia</h1>
            <a href="{{ route("quests") }}">Wszystkie</a>
        </div>
        <div class="dashboard-mini-wrapper">
        @foreach ($quests as $quest)
            <x-quest-mini :quest="$quest" />
        @endforeach
        </div>
    </section>
@endsection
