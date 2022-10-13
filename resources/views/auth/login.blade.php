@extends('layouts.app', array_merge(compact("title", "forWhom"), ["extraCss" => "auth"]))

@section('content')
    <form method="post" action="{{ route("authenticate") }}">
        @csrf
        <h1>Zaloguj się</h1>
        <input type="text" name="login" autofocus required />
        <input type="password" name="password" required />
        <input type="submit" name="submit" value="Zaloguj" />
    </form>
@endsection
