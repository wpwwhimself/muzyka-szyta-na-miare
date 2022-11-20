@extends('layouts.app', compact("title"))

@section('content')
    @foreach (["success", "error"] as $status)
        @if (session($status))
            <x-alert :status="$status" />
        @endif
    @endforeach

    <div class="grid-2">
        <section id="who-am-i">
            <div class="section-header">
                <h1><i class="fa-solid fa-user-check"></i> Zalogowany jako</h1>
            </div>
            <h2>{{ Auth::user()->client->client_name }}</h2>
            <div class="hint-table">
                <style>.hint-table div{ grid-template-columns: 1fr 1fr; }</style>
                <div class="positions">
                    <span>Ukończonych zleceń</span>
                    <span>{{ DB::table("quests")->where("client_id", Auth::id())->whereNotIn("status_id", [19, 18])->count() }}</span>
                    <span>Status klienta</span>
                    <span>
                    @if (is_veteran(Auth::id()))
                    stały klient
                    @else
                    klient zwykły<br>
                    <i>pozostało zleceń: {{ DB::table("settings")->where("setting_name", "veteran_from")->value("value_str") - DB::table("quests")->where("client_id", Auth::id())->whereNotIn("status_id", [19, 18])->count() }}</i>
                    @endif
                    </span>
                </div>
            </div>
        </section>

        <section id="dashboard-finances">
            <div class="section-header">
                <h1><i class="fa-solid fa-sack-dollar"></i> Finanse</h1>
            </div>
            <div class="dashboard-mini-wrapper">
                🚧 TBD 🚧
            </div>
        </section>
    </div>

    <section id="dashboard-requests">
        <div class="section-header">
            <h1><i class="fa-solid fa-envelope"></i> Zapytania</h1>
            <div>
                <a href="{{ route("quests") }}">Wszystkie <i class="fa-solid fa-angles-right"></i></a>
                <a href="{{ route("add-request") }}">Dodaj nowe <i class="fa-solid fa-plus"></i></a>
            </div>
        </div>
        <div class="dashboard-mini-wrapper">
        @forelse ($requests as $request)
            <x-quest-mini :quest="$request" />
        @empty
            <p class="grayed-out">brak aktywnych zapytań</p>
        @endforelse
        </div>
    </section>

    <section id="dashboard-quests">
        <div class="section-header">
            <h1><i class="fa-solid fa-gears"></i> Aktualne zlecenia</h1>
            <div>
                <a href="{{ route("quests") }}">Wszystkie <i class="fa-solid fa-angles-right"></i></a>
            </div>
        </div>
        <div class="dashboard-mini-wrapper">
        @forelse ($quests as $quest)
            <x-quest-mini :quest="$quest" />
        @empty
            <p class="grayed-out">brak aktywnych zleceń</p>
        @endforelse
        </div>
    </section>
@endsection
