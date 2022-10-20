@extends('layouts.app', ["title" => "Tworzenie nowego loginu"])

@section('content')
    <form method="post" action="{{ route("register") }}">
        @csrf
        <h1>Nowy login</h1>
        <input type="text" placeholder="login" name="login" autofocus required />
        <input type="text" placeholder="password" name="password" required />
        <input type="submit" name="submit" value="Utwórz" />
    </form>
@endsection
