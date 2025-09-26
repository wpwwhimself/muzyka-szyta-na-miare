<ul class="section-like">
    <li>Szukasz zespołu na koncert?</li>
    <li>Potrzebujesz DJa na wesele lub imprezę?</li>
    <li>Chcesz, żeby muzyka, do jakiej się bawisz, była grana na żywo?</li>
    <strong>Jesteś we właściwym miejscu!</strong>
</ul>

<section id="offer">
    <h1>Jak mogę uświetnić Twoją imprezę?</h1>

    <div class="main black-back hidden">
        <x-mdi-guitar-electric height="6em" />
        <div>
            <h2>Impreza z pompą</h2>
            <p>Jednoosobowy koncert, po którym trudno będzie ustać w miejscu</p>
        </div>
        <ul>
            <li><strong>Energiczne utwory</strong> z rockowym pazurem</li>
            <li>Różne piosenki <strong>przearanżowane tak, by bawić</strong></li>
            <li>Na wesele, na imprezę lub na koncert</li>
        </ul>
    </div>
    <div class="main black-back hidden">
        <x-mdi-piano height="6em" />
        <div>
            <h2>Występ kameralny</h2>
            <p>Nastrojowy koncert dla mniejszej publiczności</p>
        </div>
        <ul>
            <li>Solowy występ <strong>na pianinie lub gitarze</strong></li>
            <li><strong>Nastrojowy repertuar</strong> dostosowany pod okazję</li>
            <li>Na koncert lub recital</li>
        </ul>
    </div>
    <div class="main black-back hidden">
        <x-mdi-saxophone height="6em" />
        <div>
            <h2>Żywe instrumenty</h2>
            <p>Miks DJa i instrumentalisty</p>
        </div>
        <ul>
            <li>Wszystkie piosenki <strong>śpiewam na żywo</strong> i dogrywam <strong>na instrumentach live</strong></li>
            <li>Dowolność repertuaru - <strong>zagram, co zechcesz</strong></li>
        </ul>
    </div>

    <h1>Gdzie gram?</h1>

    <div class="grid-3">
        @foreach ([
            "Wolsztyn",
            "Poznań",
            "Jarocin",
        ] as $loc)
        <span class="hidden">
            <i class="fas fa-location-dot large-icon"></i>
            <h2>{{ $loc }}</h2>
        </span>
        @endforeach
    </div>
    <p>Przyjmuję też zlecenia na granie w okolicznych miejscowościach</p>
</section>

<x-sc-hr />

<section id="recomms">
    <h1>Opinie</h1>

    <p>🚧 Na razie nie zbieram opinii... Wkrótce się tu pojawią</p>
</section>

<x-front.tabbed-section id="showcases" title="Jak to brzmi?" icon="compact-disc">
    <x-slot name="buttons">
        @foreach ([
            "rolki" => "reels",
            "pełny katalog" => "list",
        ] as $label => $mode)
        <x-button action="#/" :label="$label" icon="bullhorn" onclick="filterShowcases('{{ $mode }}')" small />
        @endforeach
    </x-slot>

    <div class="showcase-section flex-down spaced" data-mode="reels">
        <h2>Najnowsze realizacje</h2>
        <x-front.showcase-reels :showcases="$showcases" />
    </div>

    <div class="showcase-section flex-down spaced gone" data-mode="list">
        <div id="songs">
            <h2>
                Wszystkie utwory, jakie mam w repertuarze
                <x-tutorial>
                    Kliknij ikonę płyty, aby odtworzyć próbkę
                </x-tutorial>
            </h2>

            <h3>Filtruj:</h3>
            <div class="flex-right keep-for-mobile center">
                <x-button action="#/" label="wszystkie" icon="circle-xmark" onclick="filterSongs()" small />

                @foreach ($genres as $genre)
                <x-button action="#/" :label="$genre->name" icon="radio" onclick="filterSongs('genre', {{ $genre->id }})" small />
                @endforeach

                {{-- @foreach ($song_tags as $tag) --}}
                {{-- <x-button action="#/" :label="$tag->name" icon="tag" onclick="filterSongs('tag', {{ $tag->id }})" small /> --}}
                {{-- @endforeach --}}
            </div>

            <ul><p class="grayed-out">Lista zostanie uzupełniona wkrótce</p></ul>
            <script defer>getSongList("dj");</script>
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
    </div>
</x-front.tabbed-section>

<section id="prices" class="grid-2">
    <div class="black-back">
        <h1>Cennik</h1>
        <span class="yellowed-out">
            <i class="fas fa-triangle-exclamation"></i>
            Poniższe ceny mogą się różnić w zależności od kosztów dojazdu
        </span>
        <div class="front-table">
            {{-- <span class="hidden">Organy (ślub, jubileusz, ...)</span>
            <span class="hidden">{{ as_pln(300) }}</span>

            <span class="hidden">Trąbka (pogrzeb, ślub, ...)</span>
            <span class="hidden">{{ as_pln(100) }}</span> --}}
            <span>🚧 zostanie uzupełnione wkrótce</span>
        </div>
    </div>

    <div class="sc-line">
        <x-sc-scissors />
        <h1>FAQ</h1>

        <ul id="faq">
            <li class="hidden">Jaki repertuar gram?</li>
            <li class="hidden">Specjalizuję się w graniu polskiego i angielskiego rocka. Gram również piosenki z innych gatunków, często rearanżując je, żeby były bardziej akustyczno-taneczne.</li>

            <li class="hidden">Czy mam własne nagłośnienie?</li>
            <li class="hidden ghost">🚧 zostanie uzupełnione wkrótce</li>

            <li class="hidden">Co z zapłatą?</li>
            <li class="hidden">W zupełności wystarcza mi przekazanie pieniędzy przed lub po uroczystości.</li>
        </ul>
    </div>
</section>
