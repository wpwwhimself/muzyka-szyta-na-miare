@extends('layouts.app', compact("title"))

@section('content')
    @foreach (["success", "error"] as $status)
    @if (session($status))
        <x-alert :status="$status" />
    @endif
    @endforeach
    <div class="grid-2">
        <section id="quests-list">
            <div class="section-header">
                <h1><i class="fa-solid fa-boxes-stacked"></i> Lista zleceń</h1>
                <div>
                    <a href="{{ route("add-quest") }}">Dodaj nowe <i class="fa-solid fa-plus"></i></a>
                </div>
            </div>
            @forelse ($quests as $quest)
                quest
            @empty
                <p class="grayed-out">brak zleceń</p>
            @endforelse
        </section>
    </div>
@endsection
