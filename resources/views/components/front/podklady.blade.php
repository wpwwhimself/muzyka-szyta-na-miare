<section id="offer">
    <h1>Co mogę dla Ciebie zrobić?</h1>

    <div class="main rounded backdropped scroll-hidden stagger" style="--stagger-index: 1;">
        <x-shipyard.app.icon name="volume-high" />
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
    <div class="main rounded backdropped scroll-hidden stagger" style="--stagger-index: 2;">
        <x-shipyard.app.icon name="music" />
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
    <div class="main rounded backdropped scroll-hidden stagger" style="--stagger-index: 3;">
        <x-shipyard.app.icon name="account" />
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
    <div id="offer-other" class="flex right center">
        <span class="section scroll-hidden stagger" style="--stagger-index: 4;">
            Schematy akordowe
            <span @popper(Uproszczona partia instrumentalna, zawierająca wszystkie znajdujące się w utworze akordy)>
                <x-shipyard.app.icon name="information" />
            </span>
        </span>
        <span class="section scroll-hidden stagger" style="--stagger-index: 5;">
            Osadzenie partii wokalnej
            <span @popper(Przygotowanie utworu poprzez dodanie dostarczonego nagrania wokalu, bądź też osobiste jego nagranie)>
                <x-shipyard.app.icon name="information" />
            </span>
        </span>
        <span class="section scroll-hidden stagger" style="--stagger-index: 6;">
            Korekcja dźwiękowa
            <span @popper(Naniesienie poprawek na dostarczony podkład muzyczny, np. zmiana tonacji czy głośności)>
                <x-shipyard.app.icon name="information" />
            </span>
        </span>
    </div>
</section>

<x-sc-hr />

<section id="recomms">
    <h1>Kto już skorzystał?</h1>
    <div class="scroll-hidden flex right center">
        <script src="https://static.elfsight.com/platform/platform.js" async></script>
        <div class="elfsight-app-6b00a039-c4ab-497e-91da-5cecfcf8511b" data-elfsight-app-lazy></div>
    </div>
    <div class="scroll-hidden">
        <h2>Komentarze klientów</h2>
        <div class="pinned-comments">
            @foreach ($pinned_comments as $comment)
            <div class="section">
                @php
                $client = $comment->changer;
                @endphp

                <h2>
                    {!! $client !!}
                </h2>
                <small>
                    <x-fa-icon pop="Przygotowany utwór" class="fas fa-box" />
                    {{ $comment->re_quest->song->full_title }}
                </small>
                {!! \Illuminate\Mail\Markdown::parse($comment->comment) !!}
                <div class="grayed-out">{{ $comment->date->diffForHumans() }}</div>
            </div>
            @endforeach
        </div>
    </div>
    <h2>Współpracuję również z:</h2>
    <div id="recomms-other" class="flex right center">
        <img class="scroll-hidden stagger" style="--stagger-index: 1;" src="{{ asset("assets/front/img/recomms/pwod.png") }}" alt="recomms" @popper(Powiatowa Wolsztyńska Orkiestra Dęta)>
        <img class="scroll-hidden stagger" style="--stagger-index: 2;" src="{{ asset("assets/front/img/recomms/gckib.png") }}" alt="recomms" @popper(Gminne Centrum Kultury i Biblioteka w Przemęcie)>
    </div>
</section>

<x-front.tabbed-section id="showcases" title="Jak to brzmi?" icon="disc">
    <x-slot:buttons>
        @foreach ([
            "wszystkie utwory" => "list",
            "nagrania klientów" => "clients",
            "za kulisami" => "reels",
            "nuty" => "scores",
        ] as $label => $mode)
        <x-shipyard.ui.button
            :label="$label"
            icon="bullhorn"
            action="none"
            onclick="filterShowcases('{{ $mode }}')"
            class="tertiary"
        />
        @endforeach
    </x-slot:buttons>

    <div class="showcase-section flex down spaced hidden" data-mode="clients">
        <div id="showcase-yts" class="flex right center">
            @foreach ($client_showcases as $showcase)
            {!! $showcase->embed !!}
            @endforeach
        </div>
        <div id="showcase-spotify">
            <iframe style="border-radius:12px" src="https://open.spotify.com/embed/album/2jjvEwHOBmdAYZT5rb33Ta?utm_source=generator&theme=0" width="100%" height="152" frameBorder="0" allowfullscreen="" allow="autoplay; clipboard-write; encrypted-media; fullscreen; picture-in-picture" loading="lazy"></iframe>
        </div>
    </div>

    <div class="showcase-section flex down spaced hidden" data-mode="reels">
        <h2>Najnowsze realizacje</h2>
        <x-front.showcase-reels :showcases="$showcases" />
    </div>

    <div class="showcase-section flex down spaced hidden" data-mode="scores">
        <div id="showcase-scores">
            @for ($i = 1; $i <= 3; $i++)
            <img src="{{ asset("assets/front/nutki$i.jpg") }}" alt="sheet music example {{ $i }}" class="animatable">
            @endfor
        </div>
    </div>

    <div class="showcase-section flex down spaced" data-mode="list">
        <div id="songs">
            <h2>Wszystkie utwory, których się podjąłem</h2>
            <p>
                Kliknij ikonę <span class="accent primary">
                    <x-shipyard.app.icon :name="model_icon('songs')" />
                </span>, aby odtworzyć próbkę
            </p>

            <h3>Filtruj:</h3>
            <div class="flex right keep-for-mobile center">
                <x-shipyard.ui.button
                    action="none"
                    class="tertiary"
                    label="wszystkie"
                    icon="close-circle"
                    onclick="filterSongs(`podklady`)"
                />

                @foreach ($genres as $genre)
                <x-shipyard.ui.button
                    action="none"
                    class="tertiary"
                    :label="$genre->name"
                    icon="radio"
                    onclick="filterSongs(`podklady`, 'genre', {{ $genre->id }})"
                />
                @endforeach

                @foreach ($song_tags as $tag)
                <x-shipyard.ui.button
                    action="none"
                    class="tertiary"
                    :label="$tag->name"
                    icon="tag"
                    onclick="filterSongs(`podklady`, 'tag', {{ $tag->id }})"
                />
                @endforeach
            </div>

            <ul id="podklady-song-list"><p class="grayed-out">Lista zostanie uzupełniona wkrótce</p></ul>
            <script defer>getSongList("podklady");</script>
        </div>
    </div>
</x-front.tabbed-section>

<section id="prices" class="grid but-mobile-down" style="--col-count: 2;">
    <div class="backdropped rounded stagger" style="--stagger-index: 1;">
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
                <h2 class="header scroll-hidden">{{ $header }}</h2>
                @foreach ($prices->where("quest_type_id", $i) as $price)
                <span class="scroll-hidden">{{ $price->service }}</span>
                <span class="scroll-hidden">{{ as_pln($price->price) }}</span>
                @endforeach
            @endforeach
        </div>
    </div>

    <div class="sc-line rounded stagger" style="--stagger-index: 2;">
        <x-sc-scissors />
        <h1>FAQ</h1>
        <ul id="faq">
            <li class="scroll-hidden">Jak tworzone są utwory?</li>
            <li class="scroll-hidden">Każdy utwór i podkład przygotowany jest od zera. Nagrania poszczególnych partii są wykonywane w całości przeze mnie. Dotyczy to również dogrywania ewentualnych drugich głosów i chórków. <i>Nie potrafię po prostu usunąć wokalu z nagrania</i>.</li>

            <li class="scroll-hidden">Jakie materiały muszę przygotować?</li>
            <li class="scroll-hidden">Jestem w stanie przygotować podkład na podstawie istniejącego już utworu (nagranie czy nawet zapis nutowy), przekazanej melodii lub nawet samych wskazówek stylistycznych. W wyjątkowych przypadkach możliwe jest też całkowite powierzenie mi aranżacji.</li>

            <li class="scroll-hidden">Czy mój podkład będzie miał linię melodyczną?</li>
            <li class="scroll-hidden"><strong>Z reguły nie</strong>, ale jeśli interesuje Cię taka, proszę o dodatkową informację.</li>

            <li class="scroll-hidden">Jak szybko można się spodziewać gotowego podkładu?</li>
            <li class="scroll-hidden">
                <!-- W związku z moimi studiami projekty wykonuję w weekendy, zwykle jeden wystarcza. -->
                Zwykle projekty jestem w stanie wykonać <b>w 2-{{ $average_quest_done }} dni</b>, choć wszystko zależy od wielu czynników.
                <strong>Nie rozpoczynam jednak pracy przed zgromadzeniem kompletu informacji</strong> – dlatego właśnie oczekuję odpowiedzi na każdą wiadomość.
            </li>

            <li class="scroll-hidden">Czy możliwe są poprawki w przygotowywanych aranżach?</li>
            <li class="scroll-hidden">Oczywiście. Efekty mojej pracy zawsze przedstawiam do recenzji, gdzie można wskazać elementy utworu, które nie przypadną Ci do gustu. Poprawki najczęściej nie wpływają na wycenę zlecenia.</li>

            <li class="scroll-hidden">Co z zapłatą za utwór?</li>
            <li class="scroll-hidden">Wycena zlecenia zostanie Ci przedstawiona przed jego podjęciem. Otrzymasz także informację o możliwych metodach płatności. <i>Nie musisz płacić od razu!</i> Wpłata jest niezbędna do pobrania plików – bez niej możesz je jedynie przeglądać.</li>
        </ul>
    </div>
</section>

<x-shipyard.ui.button
    label="Złóż zapytanie o podkład/nuty"
    icon="send"
    action="none"
    onclick="openModal('send-podklady-request', {
        client_id: {{ Auth::user()?->id ?? 'null' }},
        client_name: '{{ Auth::user()?->notes?->client_name }}' || null,
        email: '{{ Auth::user()?->notes?->email }}' || null,
        phone: '{{ Auth::user()?->notes?->phone }}' || null,
        other_medium: '{{ Auth::user()?->notes?->other_medium }}' || null,
        contact_preference: '{{ Auth::user()?->notes?->contact_preference }}' || 'email',
    })"
    class="major primary"
/>
