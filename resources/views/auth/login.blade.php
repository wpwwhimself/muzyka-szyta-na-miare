@extends('layouts.app', compact("title"))

@section('content')
    <form class="login-form" method="post" action="{{ route("authenticate") }}">
        @csrf
        <h1>Zaloguj się</h1>
        <div class="login-box">
            {{-- <x-input class="login-grid-container"
                type="text" name="login" label="Login"
                :autofocus="true" :required="true" /> --}}
            <x-input class="login-grid-container"
                type="password" name="password" label="Hasło"
                :required="true" />
            <x-input class="login-grid-container"
                type="checkbox" name="remember" label="Zapamiętaj mnie"
                />
        </div>
        <button type="submit" class="hover-lift auth-link">
            <i class="fa-solid fa-check"></i> Zaloguj się
        </button>
    </form>
@endsection
