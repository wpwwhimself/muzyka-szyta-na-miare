@extends('layouts.app', compact("title"))

@section('content')

<div id="ppp" class="grid-2">
  <div id="ppp_content">
    {!! Illuminate\Mail\Markdown::parse($content) !!}
  </div>
  <div id="ppp_nav" class="section-like">
    <h2>Rozdziały</h2>
    <ol>
    @foreach ($titles as $ttl)
      <li value="{{ explode("_", $ttl)[0] }}"><a href="{{ route('ppp', ['c' => $ttl]) }}">{{ explode("_", $ttl)[1] }}</a></li>
    @endforeach
    </ol>
  </div>
</div>

@endsection
