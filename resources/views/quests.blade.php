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
            @if (count($quests))
                @foreach ($quests as $quest)
                    quest
                @endforeach
            @else
                <p class="grayed-out">brak zleceń</p>
            @endif
        </section>
        <section id="requests-list">
            <div class="section-header">
                <h1><i class="fa-solid fa-envelope-open-text"></i> Lista zapytań</h1>
                <div>
                    <a href="{{ route("add-request") }}">Dodaj nowe <i class="fa-solid fa-plus"></i></a>
                </div>
            </div>
            @if (count($requests))
                <table class="quests-table">
                    <thead>
                        <tr>
                            <th>Tytuł<br>Wykonawca</th>
                            <th>Klient</th>
                            <th><i class="fa-solid fa-traffic-light"></i> Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach ($requests as $request)
                        <tr class="p-{{ $request->status_id }}">
                            <td>
                                <h3 class="song-title">{{ $request->title }}</h3>
                                <span class="song-artist">{{ $request->cover_artist ?? $request->artist }}</span>
                            </td>
                            <td>
                            @if ($request->cl_client_name != null)
                                <i class="fa-solid fa-user"></i> {{ $request->cl_client_name }}
                            @else
                                <i class="fa-regular fa-user"></i> {{ $request->rq_client_name }}
                            @endif
                            </td>
                            <td class="quest-status">{{ $request->status_name }}</td>
                            <td>
                                <a href="{{ route("request", $request->id) }}" title="Szczegóły zapytania"><i class="fa-solid fa-angles-right"></i></a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            @else
                <p class="grayed-out">brak zapytań</p>
            @endif
        </section>
    </div>
@endsection
