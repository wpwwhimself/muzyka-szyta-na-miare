<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <meta name=author content="Wojciech Przybyła, Wesoły Wojownik">
	<meta name=description content="Poszukujesz kogoś, kto pomoże Ci w sprawach muzycznych? Potrzebujesz podkładu lub nut? Napisz do mnie.">
	<meta name=keywords content="Wojciech Przybyła, Wesoły Wojownik, fajna strona, WPWW, podkłady, nuty, transkrypcja, patrytury, studio, muzyka">
	<meta name='viewport' content='width=device-width, initial-scale=1.0'>
	<meta property='og:image' content='https://muzykaszytanamiare.pl/media/thumbnail.jpg' />
	<meta property='og:type' content='website' />
	<meta property='og:url' content='https://muzykaszytanamiare.pl/' />
	<meta property='og:title' content='{{ config("app.name") }} – podkłady, transkrypcje, aranże, kompozycje' />
	<meta property='og:description' content='Poszukujesz kogoś, kto pomoże Ci w sprawach muzycznych? Potrzebujesz podkładu lub nut? Napisz do mnie.' />

    <link rel="icon" href="{{ URL::asset("msznm.svg") }}">
    <link rel="stylesheet" href="{{ URL::asset("css/app.css") }}">
    <link rel="stylesheet" href="{{ URL::asset("css/front.css") }}">

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="{{ asset("js/app.js") }}" defer></script>
    <script src="{{ asset("js/front.js") }}" defer></script>
    <script src="https://kit.fontawesome.com/97bfe258ce.js" crossorigin="anonymous"></script>
    <script src="https://unpkg.com/@sidsbrmnn/scrollspy@1.x/dist/scrollspy.min.js"></script>

    <!-- Global site tag (gtag.js) - Google Analytics -->
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-PDLBED2GBQ">
    </script>
    <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());

    gtag('config', 'G-PDLBED2GBQ');
    </script>

    <script>
    const USER_ID = {{ Auth::id() ?? 'null' }};
    const IS_VETERAN = {{ Auth::id() <= 1 ? 0 : intval(is_veteran(Auth::id() ?? "")) }};
    </script>

    <title>{{ config("app.name") }} ✂🎵 Podkłady i aranże dopasowane do Twoich potrzeb</title>
</head>
<body>
    @foreach (["success", "error"] as $status)
    @if (session($status))
        <x-alert :status="$status" />
    @endif
    @endforeach
    @yield('everything')
</body>
</html>
