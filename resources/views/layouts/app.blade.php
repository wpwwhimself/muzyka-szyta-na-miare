<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <link rel="icon" href="{{ asset("logo.png") }}">
    <link rel="stylesheet" href="{{ asset("css/app.css") }}">
    <link rel="stylesheet" href="{{ asset("css/back.css") }}">
    @if (isset($extraCss))
    <link rel="stylesheet" href="{{ asset("css/$extraCss.css") }}">
    @endif

    <script src="https://kit.fontawesome.com/97bfe258ce.js" crossorigin="anonymous"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="{{ asset("js/app.js") }}" defer></script>

    <title>{{ $title == null ? "WPWW" : "$title |" }} Muzyka Szyta Na Miarę</title>
</head>
<body>
    <x-header :title="$title" />
    <div class="main-wrapper">
        @yield("content")
    </div>
    <x-footer />
    @include("popper::assets")
</body>
</html>
