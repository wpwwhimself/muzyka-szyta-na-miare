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
    <link rel="stylesheet" href="{{ URL::asset("css/app.css") }}?{{ time() }}">
    <link rel="stylesheet" href="{{ URL::asset("css/front.css") }}?{{ time() }}">

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="{{ asset("js/app.js") }}" defer></script>
    <script src="{{ asset("js/front.js") }}" defer></script>
    <script src="https://kit.fontawesome.com/97bfe258ce.js" crossorigin="anonymous"></script>
    <script src="https://unpkg.com/@sidsbrmnn/scrollspy@1.x/dist/scrollspy.min.js"></script>
    @bukStyles

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
    const IS_VETERAN = {{ is_archmage() ? 0 : intval(Auth::user()->client->is_veteran ?? "") }};
    </script>

    <title>{{ config("app.name") }} ✂🎵 @yield("subtitle")</title>
</head>
<body>
    <header>
        <x-logo />
        @unless (Route::currentRouteName() == "home")
        <x-nav />
        @endunless
    </header>

    @if (Str::startsWith(Route::currentRouteName(), "home-"))
    <div id="background-division">
        @for ($i = 0; $i < 2; $i++)
        <img
            src="{{ asset("assets/divisions/".Str::afterLast(Route::currentRouteName(), "-").".svg") }}"
            alt="division logo"
            class="white-on-black"
        >
        @endfor
    </div>
    @endif

    <section id="home" class="sc-line">
        <x-sc-scissors />
        <div class="company-name flex-right">
            <img src="{{ asset(
                Str::startsWith(Route::currentRouteName(), "home-")
                    ? "assets/msznm-".Str::afterLast(Route::currentRouteName(), "-").".svg"
                    : "msznm.svg"
            ) }}" alt="logo" class="logo">
            <div>
                <h1>{{ config("app.name") }}</h1>
                <p>Wojciech Przybyła</p>
                <h2>@yield("subtitle")</h2>
            </div>
        </div>

        @hasSection("bullets")
        <ul class="section-like">
            @yield("bullets")
        </ul>
        @endif
    </section>

    @yield("content")

    @unless (Route::currentRouteName() == "home")
    <section id="about">
        <h1>O mnie</h1>
        <div class="flex-right">
            <img class="hidden" src="{{ asset("assets/front/img/home_me.jpg") }}" alt="me!">
            <ul class="hidden">
                <li>Mam na imię Wojtek i muzyką profesjonalnie zajmuję się od <b>ponad {{ date("Y") - 2012 }} lat</b></li>
                <li>Ukończyłem <b>szkołę muzyczną</b> I stopnia na gitarze</li>
                <li>Gram na wielu instrumentach, w tym <b>klawiszowych, perkusyjnych oraz dętych</b></li>
                <li>Jestem stałym członkiem <b>3 zespołów muzycznych</b>:
                    <a href="https://www.facebook.com/profile.php?id=100060053047728">Dixie Kings</a>,
                    <a href="https://www.facebook.com/orkiestrawihajster">Orkiestry Tanecznej Wihajster</a>
                    oraz
                    <a href="https://www.facebook.com/orkiestrawolsztyn">Powiatowej Wolsztyńskiej Orkiestry Dętej</a>
                </li>
                <li>Z wykształcenia <b>jestem informatykiem</b>, obecnie pracuję jako software developer</li>
                <li>Mam za sobą <b>studia magisterskie</b> z matematyki i informatyki</li>
            </ul>
        </div>
    </section>

    <x-sc-hr />
    @endunless

    @yield("contact-form")

    <x-footer />

    @foreach (["success", "error"] as $status)
    @if (session($status))
        <x-alert :status="$status" />
    @endif
    @endforeach

    @include("popper::assets")
    @include('cookie-consent::index')
    @bukScripts
</body>
</html>
