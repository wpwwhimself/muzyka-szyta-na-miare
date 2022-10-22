<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <meta name=author content="Wojciech Przybyła, Wesoły Wojownik">
	<meta name=description content="Poszukujesz kogoś, kto pomoże Ci w sprawach muzycznych? Potrzebujesz nut, podkładu lub muzyka? Napisz do WPWW.">
	<meta name=keywords content="Wojciech Przybyła, Wesoły Wojownik, fajna strona, Lightstream, WPWW, podkłady, nuty, transkrypcja, patrytury, nagrania, studio, muzyka">
	<meta name='viewport' content='width=device-width, initial-scale=1.0'>
	<meta property='og:image' content='http://hire.wpww.pl/media/thumbnail.jpg' />
	<meta property='og:type' content='website' />
	<meta property='og:url' content='http://hire.wpww.pl/' />
	<meta property='og:title' content='WPWW – Muzyka szyta na miarę – podkłady, transkrypcje, aranże, kompozycje' />
	<meta property='og:description' content='Poszukujesz kogoś, kto pomoże Ci w sprawach muzycznych? Potrzebujesz nut, podkładu lub muzyka? Napisz do WPWW.' />

    <link rel="icon" href="{{ URL::asset("logo.png") }}">
    <link rel="stylesheet" href="{{ URL::asset("css/app.css") }}">
    <link rel="stylesheet" href="{{ URL::asset("css/front.css") }}">

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="{{ asset("js/app.js") }}" defer></script>
    <script src="{{ asset("js/front.js") }}" defer></script>

    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-73695122-4"></script>
    <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());

    gtag('config', 'UA-73695122-4');
    </script>

    <title>WPWW – Muzyka szyta na miarę ✂🎵 Podkłady i usługi muzyczne</title>
</head>
<body>
    @yield('everything')
</body>
</html>
