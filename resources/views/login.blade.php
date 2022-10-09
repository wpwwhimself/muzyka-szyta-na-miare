@extends('layouts.auth')

@section('content')
<form method="post" action="/auth/login">
<p>Podaj hasło, aby przeglądać swoje projekty:</p>
<input type="password" name="auth_pass" autofocus />
<input type="submit" name="auth_sub" value="Zaloguj" />
</form>
@endsection
