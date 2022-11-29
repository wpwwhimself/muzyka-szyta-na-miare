@extends('layouts.app-front')

@section('everything')

<header>
    <x-logo />
    <div><x-nav /></div>
</header>

<div class="main-wrapper">
    <section id="home">
        <ul>
            <li>Nie możesz znaleźć dobrego podkładu muzycznego do swojej ulubionej piosenki?</li>
            <li>Szukasz nut lub partytury dla siebie i swojego zespołu?</li>
            <li>Masz pomysł na własny utwór i chcesz go wcielić w życie?</li>
        </ul>

        <img src="{{ asset("mintgreen.png") }}" alt="logo" class="logo">
        <div>
            <h1>{{ config("app.name") }}</h1>
            <h2>Podkłady i aranże dopasowane do Twoich potrzeb</h2>
        </div>

        <p>COPYWRITING Lorem ipsum dolor, sit amet consectetur adipisicing elit. Optio dolores inventore veniam vel obcaecati? Accusantium perferendis cumque reiciendis? Magnam iure dolores facere? Asperiores non quae itaque enim nam quos nostrum!</p>
    </section>

    <section id="offer">
        <h1>Co mogę dla Ciebie zrobić?</h1>
        <div class="main black-back">
            <i class="fa-solid fa-volume-up"></i>
            <div>
                <h2>Podkłady muzyczne</h2>
                <p>Odsłuchuję oryginał i nagrywam podkład według Twoich wymagań</p>
            </div>
            <ul>
                <li><b>Żywe instrumenty</b> i wysokiej jakości syntezatory</li>
                <li>Formaty <b>MP3, WAV, FLAC, MID</b></li>
                <li>Niemal <b>identyczne</b> z oryginałem</li>
            </ul>
        </div>
        <div class="main black-back">
            <i class="fa-solid fa-music"></i>
            <div>
                <h2>Nuty i partytury</h2>
                <p>Odsłuchuję oryginał i przepisuję wszystko, co usłyszę</p>
            </div>
            <ul>
                <li>Dbałość o szczegóły – <b>każdy akcent zaznaczony</b></li>
                <li>Format <b>PDF</b></li>
                <li><b>Dowolny zakres instrumentów</b> – od partii solowej po orkiestrę</li>
            </ul>
        </div>
        <div class="main black-back">
            <i class="fa-solid fa-user"></i>
            <div>
                <h2>Z myślą o Tobie</h2>
                <p>To, co dla Ciebie tworzę, może być dokładnie takie, jakie chcesz</p>
            </div>
            <ul>
                <li>Każdy aspekt projektu <b>dostosowany do Twoich wymagań</b> – od wykorzystanych instrumentów do budowy utworu</li>
                <li><b>Szybki</b> czas realizacji – od 1 dnia</li>
                <li>Darmowe <b>poprawki na gorąco</b>, żeby było idealnie</li>
            </ul>
        </div>

        <div id="offer-other" class="flex-right">
            <span>
                Schematy akordowe
                <i class="fa-solid fa-circle-info" @popper(Uproszczona partia instrumentalna, zawierająca wszystkie znajdujące się w utworze akordy)></i>
            </span>
            <span>
                Osadzenie partii wokalnej
                <i class="fa-solid fa-circle-info" @popper(Przygotowanie utworu poprzez dodanie dostarczonego nagrania wokalu, bądź też osobiste jego nagranie)></i>
            </span>
            <span>
                Korekcja dźwiękowa
                <i class="fa-solid fa-circle-info" @popper(Naniesienie poprawek na dostarczony podkład muzyczny, np. zmiana tonacji czy głośności)></i>
            </span>
        </div>
    </section>

    <section id="recomms">
        <h1>Kto już skorzystał?</h1>
        <div class="grid-3">
            <div class="section-like">
                <img src="{{ asset("assets/front/img/recomms/1.jpg") }}" alt="main3">
                <h2>Ewelina Spławska</h2><h3>wokalistka</h3>
                <p>Mega polecam tego pana. Wszystko brzmi genialnie i profesjonalnie. Polecam z całego serduszka.</p>
            </div>
            <div class="section-like">
                <img src="{{ asset("assets/front/img/recomms/2.jpg") }}" alt="main3">
                <h2>Krzysztof „Bajek” Bajeński</h2><h3>muzyk, producent</h3>
                <p>Pełen profesjonalizm – tak określiłbym współpracę z Wojtkiem. Człowiek gotowy zawsze do pracy i w pełni zaangażowany. Rzeczy, które wychodzą spod jego ręki są na bardzo wysokim poziomie. Za nami bardzo dużo ciekawych projektów, myślę jednak, że przed nami jeszcze więcej.</p>
            </div>
            <div class="section-like">
                <img src="{{ asset("assets/front/img/recomms/0.png") }}" alt="main3">
                <h2>Grzegorz Bednarczyk</h2><h3>klient, zamówił podkład do studia</h3>
                <p>Córka wyszła właśnie ze studia nagrań; pan nagrywający jest pełen podziwu podkładu i jakości wykonania. Sami pewnie jeszcze nie raz skorzystamy z usług.</p>
            </div>
        </div>
        <div>
            <iframe src="https://www.facebook.com/plugins/post.php?href=https%3A%2F%2Fwww.facebook.com%2Fjedrek.kocjan%2Fposts%2F1882616848543669&show_text=true&width=500" width="500" height="188" style="border:none;overflow:hidden" scrolling="no" frameborder="0" allowfullscreen="true" allow="autoplay; clipboard-write; encrypted-media; picture-in-picture; web-share"></iframe>
            <iframe src="https://www.facebook.com/plugins/post.php?href=https%3A%2F%2Fwww.facebook.com%2FwpwwMuzykaSzytaNaMiare%2Fposts%2F862916034490127&show_text=true&width=500" width="500" height="169" style="border:none;overflow:hidden" scrolling="no" frameborder="0" allowfullscreen="true" allow="autoplay; clipboard-write; encrypted-media; picture-in-picture; web-share"></iframe>
        </div>
        <h2>Współpracuję również z:</h2>
        <div class="flex-right">
            <img src="{{ asset("assets/front/img/recomms/pwod.png") }}" alt="recomms" @popper(Powiatowa Wolsztyńska Orkiestra Dęta)>
            <img src="{{ asset("assets/front/img/recomms/gckib.png") }}" alt="recomms" @popper(Gminne Centrum Kultury i Biblioteka w Przemęcie)>
        </div>
    </section>

    <section id="showcase">
        <h1>Co już udało mi się wykonać?</h1>
    </section>

    <section id="prices" class="grid-2">
        <div class="black-back">
            <h1>Cennik</h1>
        </div>

        <div class="black-back">
            <h1>FAQ</h1>
        </div>
    </section>

    <section id="about">
        <h1>O mnie</h1>
    </section>

    <section id="contact">
        <h1>Napisz już teraz</h1>
    </section>
</div>
<x-footer />

@include("popper::assets")
@include('cookie-consent::index')
@endsection
