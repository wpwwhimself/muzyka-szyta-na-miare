<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <link rel="icon" href="{{ asset("msznm.svg") }}">
    <link rel="stylesheet" href="{{ asset("css/app.css") }}?{{ time() }}">
    @if (isset($extraCss))
    <link rel="stylesheet" href="{{ asset("css/$extraCss.css") }}">
    @endif

    <script src="https://kit.fontawesome.com/97bfe258ce.js" crossorigin="anonymous"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="{{ asset("js/app.js") }}?{{ time() }}"></script>

    <script>
    const USER_ID = {{ Auth::id() ?? 'null' }};
    const IS_VETERAN = {{ is_archmage() ? 0 : intval(Auth::user()?->is_veteran ?? "") }};
    </script>

    @env("local")
    <style>
    :root{
        --acc: rgb(235, 34, 235) !important;
    }
    </style>
    @endenv

    <title>{{ $title != null ? "$title | " : "" }}{{ config("app.name") }}</title>
</head>
<body>
    <x-header :title="$title" />

    <div id="background-division">
        @for ($i = 0; $i < 2; $i++)
        <img
            src="{{ asset("assets/divisions/"
                .($i == 0 ? (Str::between(Request::root(), "://", ".".env("APP_DOMAIN")) ?? "msznm") : "msznm")
                .".svg") }}"
            alt="division logo"
            class="white-on-black"
        >
        @endfor
    </div>

    @foreach (["success", "error"] as $status)
        @if (session($status))
            <x-alert :status="$status" />
        @endif
    @endforeach

    <div class="main-wrapper">
        @yield("content")
    </div>

    <x-footer />

    @include("popper::assets")
</body>
</html>
