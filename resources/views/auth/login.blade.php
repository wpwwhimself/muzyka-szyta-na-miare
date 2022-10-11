@extends('layouts.app', array_merge(compact("title", "forWhom"), ["extraCss" => "auth"]))

@section('content')
<form method="post" action="{{ route("authenticate") }}">
    <h1>Zaloguj się</h1>
    <input type="text" name="auth_login" autofocus required />
    <input type="password" name="auth_pass" required />
    <input type="submit" name="auth_sub" value="Zaloguj" />
</form>
@endsection
