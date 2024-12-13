@extends("layouts.app-front")
@section("subtitle", "Podkłady i aranże dopasowane do Twoich potrzeb")

@section("bullets")

<li>Nie możesz znaleźć dobrego podkładu muzycznego do swojej ulubionej piosenki?</li>
<li>Szukasz nut lub partytury dla siebie i swojego zespołu?</li>
<li>Masz pomysł na własny utwór i chcesz go wcielić w życie?</li>
<strong>Jesteś we właściwym miejscu!</strong>

@endsection

@section("content")

<section id="offer">
    <h1>Co mogę dla Ciebie zrobić?</h1>
    <div class="main black-back hidden">
        <i class="fa-solid fa-volume-up"></i>
        <div>
            <h2>Podkłady muzyczne</h2>
            <p>Odsłuchuję oryginał i nagrywam podkład według Twoich wymagań</p>
        </div>
        <ul>
            <li><b>Żywe instrumenty</b> i wysokiej jakości syntezatory</li>
            <li>Formaty <b>MP3, WAV, FLAC, MID</b></li>
            <li>Brzmienie niemal <b>identyczne</b> z oryginałem</li>
        </ul>
    </div>
    <div class="main black-back hidden">
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
    <div class="main black-back hidden">
        <i class="fa-solid fa-user"></i>
        <div>
            <h2>Z myślą o Tobie</h2>
            <p>To, co dla Ciebie tworzę, może być dokładnie takie, jakie chcesz</p>
        </div>
        <ul>
            <li>Każdy aspekt projektu <b>dostosowany do Twoich wymagań</b> – od wykorzystanych instrumentów do budowy utworu</li>
            <li><b>Szybki</b> czas realizacji – od 2-{{ $average_quest_done }} dni <x-tutorial>...o ile nie mam za dużo zamówień</x-tutorial></li>
            <li>Darmowe <b>poprawki na bieżąco</b>, żeby było idealnie</li>
        </ul>
    </div>

    <h2>A także:</h2>
    <div id="offer-other" class="flex-right center">
        <span class="section-like hidden">
            Schematy akordowe
            <i class="fa-solid fa-circle-info" @popper(Uproszczona partia instrumentalna, zawierająca wszystkie znajdujące się w utworze akordy)></i>
        </span>
        <span class="section-like hidden">
            Osadzenie partii wokalnej
            <i class="fa-solid fa-circle-info" @popper(Przygotowanie utworu poprzez dodanie dostarczonego nagrania wokalu, bądź też osobiste jego nagranie)></i>
        </span>
        <span class="section-like hidden">
            Korekcja dźwiękowa
            <i class="fa-solid fa-circle-info" @popper(Naniesienie poprawek na dostarczony podkład muzyczny, np. zmiana tonacji czy głośności)></i>
        </span>
    </div>
</section>

<x-sc-hr />

<section id="recomms">
    <h1>Kto już skorzystał?</h1>
    <div class="grid-3">
        <div class="section-like hidden">
            <img src="{{ asset("assets/front/img/recomms/1.jpg") }}" alt="main3">
            <h2>Ewelina Spławska</h2><h3>wokalistka</h3>
            <p>Mega polecam tego pana. Wszystko brzmi <b>genialnie i profesjonalnie</b>. Polecam z całego serduszka.</p>
        </div>
        <div class="section-like hidden">
            <img src="{{ asset("assets/front/img/recomms/2.jpg") }}" alt="main3">
            <h2>Krzysztof Bajeński</h2><h3>muzyk, producent</h3>
            <p>Pełen profesjonalizm – tak określiłbym współpracę z Wojtkiem. Człowiek gotowy zawsze do pracy i w pełni zaangażowany. Rzeczy, które wychodzą spod jego ręki są <b>na bardzo wysokim poziomie</b>. Za nami bardzo dużo ciekawych projektów, myślę jednak, że przed nami jeszcze więcej.</p>
        </div>
        <div class="section-like hidden">
            <img src="{{ asset("assets/front/img/recomms/0.png") }}" alt="main3">
            <h2>Grzegorz Bednarczyk</h2><h3>klient, zamówił podkład do studia</h3>
            <p>Córka wyszła właśnie ze studia nagrań; pan nagrywający jest pełen podziwu podkładu i <b>jakości wykonania</b>. Sami pewnie jeszcze nie raz skorzystamy z usług.</p>
        </div>
    </div>
    <div class="hidden flex-right center">
        <iframe src="https://www.facebook.com/plugins/post.php?href=https%3A%2F%2Fwww.facebook.com%2Fpiotr.sting%2Fposts%2Fpfbid032Xy9dFowc5VZAr3F88vrYF7KfvnGLrGd2XRyTcG7fHd5yVECh9VqjrTJ26PgdUhpl&show_text=true&width=500" width="500" height="208" style="border:none;overflow:hidden" scrolling="no" frameborder="0" allowfullscreen="true" allow="autoplay; clipboard-write; encrypted-media; picture-in-picture; web-share"></iframe>
        <iframe src="https://www.facebook.com/plugins/post.php?href=https%3A%2F%2Fwww.facebook.com%2Falicja.stefanowska.12%2Fposts%2Fpfbid0xko4oooyuc2DAWa7k9jKBMSFcgcDgCpZKmzupUP4Qr2yVdffR8ZEvo7zvUTQD6RTl&show_text=true&width=500" width="500" height="208" style="border:none;overflow:hidden" scrolling="no" frameborder="0" allowfullscreen="true" allow="autoplay; clipboard-write; encrypted-media; picture-in-picture; web-share"></iframe>
        <iframe src="https://www.facebook.com/plugins/post.php?href=https%3A%2F%2Fwww.facebook.com%2Faldona.starzyk%2Fposts%2Fpfbid02Cr5pG2cgwvK6shabfBLEUNm2av9wdc3ZRdzntoF1FQL3pub1Lhd7G5mFFwDUUue1l&show_text=true&width=500" width="500" height="189" style="border:none;overflow:hidden" scrolling="no" frameborder="0" allowfullscreen="true" allow="autoplay; clipboard-write; encrypted-media; picture-in-picture; web-share"></iframe>
    </div>
    <x-a href="https://www.facebook.com/muzykaszytanamiarepl/reviews" target="_blank">Więcej recenzji</x-a>
    <h2>Współpracuję również z:</h2>
    <div id="recomms-other" class="flex-right center">
        <img class="hidden" src="{{ asset("assets/front/img/recomms/pwod.png") }}" alt="recomms" @popper(Powiatowa Wolsztyńska Orkiestra Dęta)>
        <img class="hidden" src="{{ asset("assets/front/img/recomms/gckib.png") }}" alt="recomms" @popper(Gminne Centrum Kultury i Biblioteka w Przemęcie)>
    </div>
</section>

<section id="showcases">
    <h1>Co już udało mi się wykonać?</h1>

    <div id="showcase-yts" class="flex-right center">
        @foreach ($client_showcases as $showcase)
        {!! $showcase->embed !!}
        @endforeach
    </div>
    <div id="showcase-spotify">
        <iframe style="border-radius:12px" src="https://open.spotify.com/embed/album/2jjvEwHOBmdAYZT5rb33Ta?utm_source=generator&theme=0" width="100%" height="152" frameBorder="0" allowfullscreen="" allow="autoplay; clipboard-write; encrypted-media; fullscreen; picture-in-picture" loading="lazy"></iframe>
    </div>

    <div id="showcase-fbs" class="flex-right center">
    @foreach ($showcases as $showcase)
    {!! $showcase->link_ig ?? $showcase->link_fb !!}
    @endforeach
    </div>
    <x-a href="https://www.instagram.com/muzykaszytanamiarepl/" target="_blank">Inne prezentacje</x-a>

    <div id="showcase-scores">
        @for ($i = 1; $i <= 3; $i++)
        <img src="{{ asset("assets/front/nutki$i.jpg") }}" alt="sheet music example {{ $i }}">
        @endfor
    </div>

    <div id="songs">
        <h2>
            Utwory, których się podjąłem
            <x-tutorial>
                Kliknij ikonę płyty, aby odtworzyć próbkę
            </x-tutorial>
        </h2>
        <div class="flex-right center">
            <x-button action="#/" label="Wszystkie" icon="tag" onclick="filterSongs()" small />
            @foreach ($song_tags as $tag)
            <x-button action="#/" :label="$tag->name" icon="tag" onclick="filterSongs({{ $tag->id }})" small />
            @endforeach
        </div>
        <ul><p class="grayed-out">Lista zostanie uzupełniona wkrótce</p></ul>
        <div class="popup">
            <div class="popup-contents flex-down center">
                <h3 class="song-full-title"></h3>
                <p class="song-desc"></p>
                <span id="song-loader" class="hidden"><i class="fa-solid fa-spin fa-circle-notch"></i></span>
                <x-file-player type="ogg" file="" is-showcase />
                <x-button label="" icon="times" :small="true" action="#/" id="popup-close" />
            </div>
        </div>
    </div>
</section>

<section id="prices" class="grid-2">
    <div class="black-back">
        <h1>Cennik</h1>
        <span class="yellowed-out">
            <i class="fas fa-triangle-exclamation"></i>
            Poniższe ceny mają charakter poglądowy.
            Wycena każdego zlecenia jest wykonywana indywidualnie.
        </span>
        <div class="front-table">
            @foreach ([
                "1" => "Podkłady muzyczne",
                "2" => "Nuty",
                "3" => "Nagrania"
            ] as $i => $header)
                <h2 class="header hidden">{{ $header }}</h2>
                @foreach ($prices->where("quest_type_id", $i) as $price)
                <span class="hidden">{{ $price->service }}</span>
                <span class="hidden">{{ as_pln($price->price) }}</span>
                @endforeach
            @endforeach
        </div>
    </div>

    <div class="sc-line">
        <x-sc-scissors />
        <h1>FAQ</h1>
        <ul id="faq">
            <li class="hidden">Jak tworzone są utwory?</li>
            <li class="hidden">Każdy utwór i podkład przygotowany jest od zera. Nagrania poszczególnych partii są wykonywane w całości przeze mnie. Dotyczy to również dogrywania ewentualnych drugich głosów i chórków. <i>Nie potrafię po prostu usunąć wokalu z nagrania</i>.</li>

            <li class="hidden">Jakie materiały muszę przygotować?</li>
            <li class="hidden">Jestem w stanie przygotować podkład na podstawie istniejącego już utworu (nagranie czy nawet zapis nutowy), przekazanej melodii lub nawet samych wskazówek stylistycznych. W wyjątkowych przypadkach możliwe jest też całkowite powierzenie mi aranżacji.</li>

            <li class="hidden">Czy mój podkład będzie miał linię melodyczną?</li>
            <li class="hidden"><strong>Z reguły nie</strong>, ale jeśli interesuje Cię taka, proszę o dodatkową informację.</li>

            <li class="hidden">Jak szybko można się spodziewać gotowego podkładu?</li>
            <li class="hidden">
                <!-- W związku z moimi studiami projekty wykonuję w weekendy, zwykle jeden wystarcza. -->
                Zwykle projekty jestem w stanie wykonać <b>w 2-{{ $average_quest_done }} dni</b>, choć wszystko zależy od wielu czynników.
                <strong>Nie rozpoczynam jednak pracy przed zgromadzeniem kompletu informacji</strong> – dlatego właśnie oczekuję odpowiedzi na każdą wiadomość.
            </li>

            <li class="hidden">Czy możliwe są poprawki w przygotowywanych aranżach?</li>
            <li class="hidden">Oczywiście. Efekty mojej pracy zawsze przedstawiam do recenzji, gdzie można wskazać elementy utworu, które nie przypadną Ci do gustu. Poprawki najczęściej nie wpływają na wycenę zlecenia.</li>

            <li class="hidden">Co z zapłatą za utwór?</li>
            <li class="hidden">Wycena zlecenia zostanie Ci przedstawiona przed jego podjęciem. Otrzymasz także informację o możliwych metodach płatności. <i>Nie musisz płacić od razu!</i> Wpłata jest niezbędna do pobrania plików – bez niej możesz je jedynie przeglądać.</li>
        </ul>
    </div>
</section>

@endsection

@section("contact-form")

<section id="contact">
    <h1>Napisz już teraz</h1>
    <form method="post" action="{{ route("add-request-back") }}" id='contactform' class="black-back flex-down hidden">
        @csrf
        <h2>Szczegóły zlecenia</h2>
        <div class="bulk-box sc-line flex-right center">
            <div class="flex-down but-mobile-right">
                @foreach ($quest_types as $id => $type)
                <x-input type="radio" name="quest_type[]" :value="$id" :label="$type" :checked="$id == 1" />
                @endforeach
            </div>
            <x-input type="text" name="title[]" label="Tytuł utworu" placeholder="{{ $random_song->title }}" />
            <x-input type="text" name="artist[]" label="Wykonawca" placeholder="{{ $random_song->artist }}" />
            <x-input type="text" name="link[]" label="Linki do oryginalnych nagrań (oddzielone przecinkami)" :small="true" placeholder="{{ $random_song->link }}" />
            <x-input type="TEXT" name="wishes[]" label="Jakie są Twoje życzenia? (np. styl, czy z linią melodyczną itp.)" />
            <x-input type="date" name="hard_deadline[]" label="Kiedy najpóźniej chcesz otrzymać materiały? (opcjonalnie)" />
        </div>
        <x-button action="#/" id="request_bulk_add" icon="plus" label="Dodaj kolejny utwór" />
        <script>
        $(document).ready(function(){
            $("#request_bulk_add").click(function(){
                $(".bulk-box:first-of-type").clone().insertBefore($(this));
                $(".bulk-box:last-of-type input, .bulk-box:last-of-type textarea").val("");
            });
        });
        </script>

        <h2>Twoje dane</h2>
        <div class="flex-right center">
            <x-input type="text" name="client_name" label="Imię i nazwisko" placeholder="Jan Kowalski" />
            <div class="section-like sc-line">
                <label>Jak mogę do Ciebie dotrzeć? <i>(wypełnij co najmniej jedno)</i></label>
                <div class="flex-right">
                    <x-input type="email" name="email" label="Email" />
                    <x-input type="tel" name="phone" label="Numer telefonu" />
                    <x-input type="text" name="other_medium" label="Inna forma kontaktu (np. Whatsapp)" />
                </div>
            </div>
            <x-select name="contact_preference" label="Preferowana forma kontaktu" :options="$contact_preferences" />
            <x-input type="number" name="m_test" label="Test antyspamowy – cztery razy pięć?" :required="true" />
        </div>

        <input type="hidden" name="intent" value="new" />
        <x-button
            label="Wyślij zapytanie" icon="1" name="new_status" value="1"
            action="submit"
            />
    </form>
</section>

@endsection
