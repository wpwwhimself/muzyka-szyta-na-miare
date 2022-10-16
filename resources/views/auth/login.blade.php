@extends('layouts.app', array_merge(compact("title", "forWhom"), ["extraCss" => "auth"]))

@section('content')
    @foreach (["success", "error"] as $status)
        @if (session($status))
            <div class="alert {{ $status }}">
                {{ session($status) }}
            </div>
        @endif
    @endforeach
    <form class="login-form" method="post" action="{{ route("authenticate") }}">
        @csrf
        <h1>Zaloguj się</h1>
        <div class="login-grid">
            <x-input class="login-grid-container"
                type="text" name="login" label="Login"
                :autofocus="true" :required="true" />
            <x-input class="login-grid-container"
                type="password" name="password" label="Hasło"
                :required="true" />
            <x-input class="login-grid-container"
                type="checkbox" name="remember" label="Zapamiętaj mnie"
                />
        </div>
        <input type="submit" name="submit" value="Zaloguj" />
    </form>
@endsection
