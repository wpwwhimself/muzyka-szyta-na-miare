<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <link rel="icon" href="{{ asset("mintgreen_tiny.png") }}">
    <link href="https://fonts.googleapis.com/css?family=Krona+One" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Raleway" rel="stylesheet">
    <style>
    body{
        font-family: "Raleway", "Calibri", "Arial";
        font-size: 16px;
    }
    h1, h2, h3{
        font-family: Montserrat, "Arial Black";
    }
    h2, strong, b, a{
        color: #60cc89
    }
    td.framed-cell{
        border: 3px solid gray;
        padding: 1em;
        border-radius: 1em;
    }
    td.framed-cell h2, td.framed-cell p{
        margin: var(--size-xxs);
    }
    i{
        color: gray;
    }
    footer h2, footer h3{
        margin: 0;
    }
    footer .contact-info a{
        display: block;
    }
    </style>
    <title>{{ $title != null ? "$title | " : "" }}{{ config("app.name") }}</title>
</head>
<body>
    <x-mail-header :title="$title" />
    <hr />
    @yield("content")
    <hr />
    <x-mail-footer />
</body>
</html>
