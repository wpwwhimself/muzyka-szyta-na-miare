@extends('layouts.app', compact("title"))

@section('content')
    <section id="quests-list">
        <div class="section-header">
            <h1>🎷 Lista zleceń</h1>
        </div>
        @if (count($quests))
            @foreach ($quests as $quest)
                <x-quest-mini :quest="$quest" />
            @endforeach
        @else
            <p class="grayed-out">brak zleceń</p>
        @endif
    </section>
@endsection
