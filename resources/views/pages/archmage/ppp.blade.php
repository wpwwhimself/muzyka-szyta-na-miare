@extends('layouts.app', compact("title"))

@section('content')

<div id="ppp" class="flex right">
  <div id="ppp_nav" class="section">
    <h2>Rozdziały</h2>
    <ol>
    @foreach ($titles as $ttl)
      <li value="{{ preg_replace('/^(\d+).*/', '$1', $ttl) }}">
        <a href="{{ route('ppp', ['page' => $ttl]) }}">
          {{ preg_replace('/\d+-(.*)/', '$1', $ttl) }}
        </a>
      </li>
    @endforeach
    </ol>
  </div>
  <div id="ppp_content">
    @includeFirst(["doc.$page", "doc-missing"])
  </div>
</div>

@endsection
