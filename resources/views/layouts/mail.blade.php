<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <link rel="icon" href="{{ asset("mintgreen.png") }}">
    <link href="https://fonts.googleapis.com/css?family=Krona+One" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Raleway" rel="stylesheet">
    <style>
    body{
        font-family: "Raleway", "Calibri", "Arial";
        font-size: 16px;
        text-align: center;
    }
    h1, h2, h3{
        font-family: "Krona One", "Arial Black";
    }
    h2{
        color: #60cc89
    }
    table{
        margin: 0 auto;
    }
    td.framed-cell{
        border: 3px solid gray;
        padding: 1em;
        border-radius: 1em;
    }
    td.framed-cell h2, td.framed-cell p{
        margin: 0.2em;
    }
    footer h2, footer h3{
        margin: 0;
    }
    footer .contact-info{
        margin: 1em 0;
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
