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
            <div class="quests-table">
                <div class="table-header table-row">
                    <span>Tytuł<br>Wykonawca</span>
                    <span>Klient</span>
                    <span><i class="fa-solid fa-traffic-light"></i> Status</span>
                </div>
                <hr />
                @forelse ($requests as $request)
                <a href="{{ route("request", $request->id) }}" class="table-row p-{{ $request->status_id }}">
                    <span>
                        <h3 class="song-title">{{ $request->title }}</h3>
                        <span class="song-artist">{{ $request->cover_artist ?? $request->artist }}</span>
                    </span>
                    <span>
                    @if ($request->client?->client_name)
                        <i class="fa-solid fa-user"></i> {{ $request->client->client_name }}
                    @else
                        <i class="fa-regular fa-user"></i> {{ $request->client_name }}
                    @endif
                    </span>
                    <span class="quest-status">{{ $request->status->status_name }}</span>
                </a>
                @empty
                <p class="grayed-out">brak zapytań</p>
                @endforelse
                </tbody>
            </div>
        </section>
    </div>
@endsection
