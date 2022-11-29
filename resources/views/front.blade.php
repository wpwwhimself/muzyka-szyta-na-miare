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

        <p></p> <!-- TODO COPYWRITING -->
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
                <p>Mega polecam tego pana. Wszystko brzmi <b>genialnie i profesjonalnie</b>. Polecam z całego serduszka.</p>
            </div>
            <div class="section-like">
                <img src="{{ asset("assets/front/img/recomms/2.jpg") }}" alt="main3">
                <h2>Krzysztof Bajeński</h2><h3>muzyk, producent</h3>
                <p>Pełen profesjonalizm – tak określiłbym współpracę z Wojtkiem. Człowiek gotowy zawsze do pracy i w pełni zaangażowany. Rzeczy, które wychodzą spod jego ręki są <b>na bardzo wysokim poziomie</b>. Za nami bardzo dużo ciekawych projektów, myślę jednak, że przed nami jeszcze więcej.</p>
            </div>
            <div class="section-like">
                <img src="{{ asset("assets/front/img/recomms/0.png") }}" alt="main3">
                <h2>Grzegorz Bednarczyk</h2><h3>klient, zamówił podkład do studia</h3>
                <p>Córka wyszła właśnie ze studia nagrań; pan nagrywający jest pełen podziwu podkładu i <b>jakości wykonania</b>. Sami pewnie jeszcze nie raz skorzystamy z usług.</p>
            </div>
        </div>
        <div>
            <iframe src="https://www.facebook.com/plugins/post.php?href=https%3A%2F%2Fwww.facebook.com%2Fjedrek.kocjan%2Fposts%2F1882616848543669&show_text=true&width=500" width="500" height="188" style="border:none;overflow:hidden" scrolling="no" frameborder="0" allowfullscreen="true" allow="autoplay; clipboard-write; encrypted-media; picture-in-picture; web-share"></iframe>
            <iframe src="https://www.facebook.com/plugins/post.php?href=https%3A%2F%2Fwww.facebook.com%2FwpwwMuzykaSzytaNaMiare%2Fposts%2F862916034490127&show_text=true&width=500" width="500" height="169" style="border:none;overflow:hidden" scrolling="no" frameborder="0" allowfullscreen="true" allow="autoplay; clipboard-write; encrypted-media; picture-in-picture; web-share"></iframe>
        </div>
        <h2>Współpracuję również z:</h2>
        <div id="recomms-other" class="flex-right">
            <img src="{{ asset("assets/front/img/recomms/pwod.png") }}" alt="recomms" @popper(Powiatowa Wolsztyńska Orkiestra Dęta)>
            <img src="{{ asset("assets/front/img/recomms/gckib.png") }}" alt="recomms" @popper(Gminne Centrum Kultury i Biblioteka w Przemęcie)>
        </div>
    </section>

    <section id="showcase">
        <h1>Co już udało mi się wykonać?</h1>

        <div id="showcase-mp3s" class="flex-right">
            <div class="sampleproj section-like">
                <p>Solo fortepian</p>
                <audio controls><source type="audio/mp3" src="{{ asset("assets/front/showcase/Z4O.mp3") }}"></audio>
                <audio controls><source type="audio/mp3" src="{{ asset("assets/front/showcase/Z94.mp3") }}"></audio>
                <audio controls><source type="audio/ogg" src="{{ asset("assets/front/showcase/piano_ZCJ.ogg") }}"></audio>
            </div>
            <div class="sampleproj section-like">
                <p>Piosenka aktorska</p>
                <audio controls><source type="audio/mp3" src="{{ asset("assets/front/showcase/Z4E.mp3") }}"></audio>
                <audio controls><source type="audio/mp3" src="{{ asset("assets/front/showcase/Z97.mp3") }}"></audio>
                <audio controls><source type="audio/ogg" src="{{ asset("assets/front/showcase/aktorska_ZC1.ogg") }}"></audio>
            </div>
            <div class="sampleproj section-like">
                <p>Dla dzieci</p>
                <audio controls><source type="audio/mp3" src="{{ asset("assets/front/showcase/Z3M.mp3") }}"></audio>
                <audio controls><source type="audio/ogg" src="{{ asset("assets/front/showcase/kids_Z9K.ogg") }}"></audio>
            </div>
            <div class="sampleproj section-like">
                <p>Ballada</p>
                <audio controls><source type="audio/mp3" src="{{ asset("assets/front/showcase/Z4T.mp3") }}"></audio>
                <audio controls><source type="audio/mp3" src="{{ asset("assets/front/showcase/Z98.mp3") }}"></audio>
                <audio controls><source type="audio/ogg" src="{{ asset("assets/front/showcase/ballad_ZCT.ogg") }}"></audio>
            </div>
            <div class="sampleproj section-like">
                <p>Rock</p>
                <audio controls><source type="audio/mp3" src="{{ asset("assets/front/showcase/Z34.mp3") }}"></audio>
                <audio controls><source type="audio/mp3" src="{{ asset("assets/front/showcase/Z91.mp3") }}"></audio>
                <audio controls><source type="audio/ogg" src="{{ asset("assets/front/showcase/rock_ZCK.ogg") }}"></audio>
            </div>
            <div class="sampleproj section-like">
                <p>Metal</p>
                <audio controls><source type="audio/mp3" src="{{ asset("assets/front/showcase/Z4F.mp3") }}"></audio>
            </div>
            <div class="sampleproj section-like">
                <p>Reggae</p>
                <audio controls><source type="audio/mp3" src="{{ asset("assets/front/showcase/Z4M.mp3") }}"></audio>
            </div>
            <div class="sampleproj section-like">
                <p>Biesiadne</p>
                <audio controls><source type="audio/mp3" src="{{ asset("assets/front/showcase/Z4P.mp3") }}"></audio>
                <audio controls><source type="audio/mp3" src="{{ asset("assets/front/showcase/Z9N.mp3") }}"></audio>
                <audio controls><source type="audio/ogg" src="{{ asset("assets/front/showcase/biesiada_ZCQ.ogg") }}"></audio>
            </div>
            <div class="sampleproj section-like">
                <p>Disco polo</p>
                <audio controls><source type="audio/mp3" src="{{ asset("assets/front/showcase/Z4Q.mp3") }}"></audio>
                <audio controls><source type="audio/mp3" src="{{ asset("assets/front/showcase/Z9G.mp3") }}"></audio>
                <audio controls><source type="audio/ogg" src="{{ asset("assets/front/showcase/discopolo_ZCN.ogg") }}"></audio>
            </div>
            <div class="sampleproj section-like">
                <p>Country</p>
                <audio controls><source type="audio/mp3" src="{{ asset("assets/front/showcase/Z45.mp3") }}"></audio>
                <audio controls><source type="audio/mp3" src="{{ asset("assets/front/showcase/Z92.mp3") }}"></audio>
                <audio controls><source type="audio/ogg" src="{{ asset("assets/front/showcase/country_ZCS.ogg") }}"></audio>
            </div>
            <div class="sampleproj section-like">
                <p>Jazz</p>
                <audio controls><source type="audio/mp3" src="{{ asset("assets/front/showcase/Z33.mp3") }}"></audio>
                <audio controls><source type="audio/ogg" src="{{ asset("assets/front/showcase/jazz_ZCW.ogg") }}"></audio>
            </div>
            <div class="sampleproj section-like">
                <p>Blues</p>
                <audio controls><source type="audio/mp3" src="{{ asset("assets/front/showcase/Z3N.mp3") }}"></audio>
                <audio controls><source type="audio/mp3" src="{{ asset("assets/front/showcase/Z9E.mp3") }}"></audio>
                <audio controls><source type="audio/ogg" src="{{ asset("assets/front/showcase/blues_ZCL.ogg") }}"></audio>
            </div>
        </div>

        <div id="showcase-fbs">
        @foreach ($showcases as $showcase)
        {!! $showcase->link_fb !!}
        @endforeach
        </div>

        <div id="showcase-scores">
            @for ($i = 1; $i <= 3; $i++)
            <img src="{{ asset("assets/front/nutki$i.jpg") }}" alt="sheet music example {{ $i }}">
            @endfor
        </div>
    </section>

    <section id="prices" class="grid-2">
        <div class="black-back">
            <h1>Cennik</h1>
            <div class="front-table">
                @foreach ([
                    "1" => "Podkłady muzyczne",
                    "2" => "Nuty",
                    "3" => "Nagrania"
                ] as $i => $header)
                    <h2 class="header">{{ $header }}</h2>
                    @foreach ($prices->where("quest_type_id", $i) as $price)
                    <span>{{ $price->service }}</span>
                    <span>{{ $price->price_b }} zł</span>
                    @endforeach
                @endforeach
            </div>
        </div>

        <div class="black-back">
            <h1>FAQ</h1>
            <ul id="faq">
                <li>Jak tworzone są utwory?</li>
                <li>Każdy utwór i podkład przygotowany jest od zera. Nagrania poszczególnych partii są wykonywane w całości przeze mnie. Dotyczy to również dogrywania ewentualnych drugich głosów i chórków. <i>Nie potrafię po prostu usunąć wokalu z nagrania</i>.</li>
    
                <li>Jakie materiały muszę przygotować?</li>
                <li>Jestem w stanie przygotować podkład na podstawie istniejącego już utworu (nagranie czy nawet zapis nutowy), przekazanej melodii, samych wskazówek stylistycznych. W skrajnych przypadkach możliwe jest też całkowite powierzenie mi aranżacji.</li>
    
                <li>Czy mój podkład będzie miał linię melodyczną?</li>
                <li><strong>Z reguły nie</strong>, ale jeśli jesteś zainteresowany takową, proszę o dodatkową informację.</li>
    
                <li>Jak szybko można się spodziewać gotowego podkładu?</li>
                <li><!-- W związku z moimi studiami projekty wykonuję w weekendy, zwykle jeden wystarcza. -->Zwykle projekty jestem w stanie wykonać w 1-3 dni, choć wszystko zależy od tego, jak mi studia pozwolą pracować. <strong>Nie rozpoczynam jednak pracy przed zgromadzeniem kompletu informacji</strong> – dlatego właśnie oczekuję na odpowiedź na każdego wysłanego przeze mnie maila.</li>
    
                <li>Czy możliwe są poprawki w przygotowywanych aranżach?</li>
                <li>Oczywiście. Efekty mojej pracy zawsze przedstawiam do recenzji, gdzie można wskazać elementy utworu, które nie przypadną Ci do gustu. Poprawki najczęściej nie wpływają na wycenę zlecenia.</li>
    
                <li>Co z zapłatą za utwór?</li>
                <li>Wycena zlecenia zostanie przesłana do Ciebie mailem przed jego podjęciem. Otrzymasz także numer konta do przelewu. <i>Nie musisz płacić od razu!</i> Wpłata jest potrzebna jedynie do możliwości pobrania plików.</li>
            </ul>
        </div>
    </section>

    <section id="about">
        <h1>O mnie</h1>
        <div class="flex-right">
            <img src="{{ asset("assets/front/img/dixie_kontent.jpg") }}" alt="me!">
            <p>
                Mam na imię Wojtek i&nbsp;jestem muzykiem z&nbsp;ponaddziesięcioletnim doświadczeniem instrumentalnym, aranżerskim i&nbsp;kompozytorskim.
                Moim głównym instrumentem jest gitara, ale często pracuję też z&nbsp;klawiszami i&nbsp;perkusją, czasami udzielając się również na instrumentach dętych.
                Kiedy nie tworzę podkładów, pracuję nad własną twórczością i&nbsp;zajmuję się programowaniem.
            </p>
        </div>
    </section>

    <section id="contact">
        <h1>Napisz już teraz</h1>
        <form method="post" action="{{ route("mod-request-back") }}" id='contactform' class="black-back grid-2">
            @csrf
            <div>
                <h2>Szczegóły zlecenia</h2>
                <x-select name="quest_type" label="Rodzaj zlecenia" :options="$quest_types" />
                <x-input type="text" name="m_title" label="Tytuł utworu" />
                <x-input type="text" name="m_artist" label="Wykonawca" />
                <x-input type="text" name="link" label="Linki do nagrań (oddzielone przecinkiem)" :small="true" />
                <x-input type="TEXT" name="wishes" label="Jakie są Twoje życzenia? (np. styl, czy z linią melodyczną itp.)" />
                <x-input type="date" name="hard_deadline" label="Na kiedy jest potrzebne? (opcjonalnie)" />
            </div>
            <div>
                <h2>Twoje dane</h2>
                <x-input type="text" name="m_name" label="Imię i nazwisko" placeholder="Jan Kowalski" />
                <label>Jak mogę do Ciebie dotrzeć?<br><i>(wypełnij co najmniej jedno, choć zachęcam do podania maila)</i></label>
                <x-input type="email" name="email" label="Email" />
                <x-input type="tel" name="phone" label="Numer telefonu" />
                <x-input type="text" name="other_medium" label="Inna forma kontaktu" />
                <x-select name="contact_preference" label="Preferowana forma kontaktu" :options="$contact_preferences" />
                <x-input type="number" name="m_test" label="Cztery razy pięć?" :required="true" />
            </div>
            <button type="submit" name="m_sub">
                Wyślij
            </button>
        </form>
    </section>
</div>
<x-footer />

@include("popper::assets")
@include('cookie-consent::index')
@endsection
