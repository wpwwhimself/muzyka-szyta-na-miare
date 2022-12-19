@extends('layouts.app', compact("title"))

@section('content')
<section id="clients-list">
    <div class="section-header">
        <h1><i class="fa-solid fa-broom"></i> Logi</h1>
    </div>
    <style>
    .table-row{ grid-template-columns: repeat(5, 1fr); }
    </style>
    <div class="quests-table">
        <div class="table-header table-row">
            <span>Data</span>
            <span>ReQuest</span>
            <span>Status</span>
            <span>Komentarz</span>
            <span>Maile</span>
        </div>
        <hr />
        @forelse ($logs as $log)
        <div class="table-row">
            <span>{{ $log->date }}</span>
            <span>{{ $log->re_quest_id }}</span>
            <span>{{ DB::table("statuses")->find($log->new_status_id)->status_name }}</span>
            <span>{{ $log->comment }}</span>
            <span>{{ $log->mail_sent }}</span>
        </div>
        @empty
        <p class="grayed-out">brak logów</p>
        @endforelse
    </div>
</section>

@endsection
