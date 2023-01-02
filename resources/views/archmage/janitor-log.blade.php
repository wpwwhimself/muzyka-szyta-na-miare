@extends('layouts.app', compact("title"))

@section('content')
<section id="clients-list">
    <div class="section-header">
        <h1><i class="fa-solid fa-broom"></i> Logi</h1>
    </div>
    <style>
    .table-row{ grid-template-columns: repeat(4, 1fr); }
    </style>
    <div class="quests-table">
        <div class="table-header table-row">
            <span>Data</span>
            <span>ReQuest</span>
            <span>Status - komentarz</span>
            <span>Maile</span>
        </div>
        <hr />
        @forelse ($logs as $log)
        <div class="table-row">
            <span>{{ $log->date->format("Y-m-d") }}</span>
            <span><a href="{{ route(strlen($log->re_quest_id) == 36 ? "request" : "quest", ["id" => $log->re_quest_id]) }}">{{ $log->re_quest_id }}</a></span>
            <span>{{ DB::table("statuses")->find($log->new_status_id)->status_name }} - {{ $log->comment }}</span>
            <span>{{ $log->mail_sent }}</span>
        </div>
        @empty
        <p class="grayed-out">brak logów</p>
        @endforelse
    </div>
</section>

@endsection
