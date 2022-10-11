<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <link rel="icon" href="{{ URL::asset("logo.png") }}">
    <link rel="stylesheet" href="{{ URL::asset("css/app.css") }}">
    @if (isset($extraCss))
    <link rel="stylesheet" href="{{ URL::asset("css/$extraCss.css") }}">
    @endif

    <title>{{ $title == null ? "WPWW –" : "$title |" }} Muzyka szyta na miarę</title>
</head>
<body>
    <x-header :title="$title" :for-whom="$forWhom" />
    <div class="main-wrapper">
        @yield("content")
    </div>
    <x-footer />
</body>
</html>
