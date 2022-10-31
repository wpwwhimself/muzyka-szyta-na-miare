@extends('layouts.app', compact("title"))

@section('content')
    @foreach (["success", "error"] as $status)
    @if (session($status))
        <x-alert :status="$status" />
    @endif
    @endforeach
        <section id="requests-list">
            <div class="section-header">
                <h1><i class="fa-solid fa-envelope-open-text"></i> Lista zapytań</h1>
                <div>
                    <a href="{{ route("add-request") }}">Dodaj nowe <i class="fa-solid fa-plus"></i></a>
                </div>
            </div>
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
                @forelse ($requests as $request)
                    <tr class="p-{{ $request->status_id }}">
                        <td>
                            <h3 class="song-title">{{ $request->title }}</h3>
                            <span class="song-artist">{{ $request->cover_artist ?? $request->artist }}</span>
                        </td>
                        <td>
                        @if ($request->client?->client_name)
                            <i class="fa-solid fa-user"></i> {{ $request->client->client_name }}
                        @else
                            <i class="fa-regular fa-user"></i> {{ $request->client_name }}
                        @endif
                        </td>
                        <td class="quest-status">{{ $request->status->status_name }}</td>
                        <td>
                            <a href="{{ route("request", $request->id) }}" title="Szczegóły zapytania"><i class="fa-solid fa-angles-right"></i></a>
                        </td>
                    </tr>
                @empty
                    <p class="grayed-out">brak zapytań</p>
                @endforelse
                </tbody>
            </table>
        </section>
    </div>
@endsection
