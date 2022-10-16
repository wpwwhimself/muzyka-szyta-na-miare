@extends('layouts.app', compact("title"))

@section('content')
    @foreach (["success", "error"] as $status)
    @if (session($status))
        <div class="alert {{ $status }}">
            {{ session($status) }}
        </div>
    @endif
    @endforeach
<h1>🚧 Tu będzie front strony 🚧</h1>
@endsection
