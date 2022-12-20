@extends('layouts.app', compact("title"))

@section('content')
<section id="ppp">
@forelse ($questions as $question => $answer)
    <div class="section-header">
        <h1><i class="fa-solid fa-circle-question"></i> {{ $question }}</h1>
    </div>
    <div>
        {{ Illuminate\Mail\Markdown::parse($answer) }}
    </div>
@empty
    <p class="grayed-out">brak kwestii dyskusyjnych</p>
@endforelse
</section>

@endsection
