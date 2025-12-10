<section id="offer">
    <h1>Jak mogę uświetnić Twoją imprezę?</h1>

    <div class="main rounded backdropped scroll-hidden stagger" style="--stagger-index: 1;">
        <x-shipyard.app.icon name="guitar-electric" />
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
    <div class="main rounded backdropped scroll-hidden stagger" style="--stagger-index: 2;">
        <x-shipyard.app.icon name="piano" />
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
    <div class="main rounded backdropped scroll-hidden stagger" style="--stagger-index: 3;">
        <x-shipyard.app.icon name="saxophone" />
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

    <div class="grid" style="--col-count: 3;">
        @foreach ([
            "Wolsztyn",
            "Poznań",
            "Jarocin",
        ] as $i => $loc)
        <span class="location scroll-hidden stagger" style="--stagger-index: {{ $i + 4 }}">
            <x-shipyard.app.icon name="map-marker" />
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

<x-front.tabbed-section id="showcases" title="Jak to brzmi?" icon="disc">
    <x-slot:buttons>
        @foreach ([
            "rolki" => "reels",
            "pełny katalog" => "list",
        ] as $label => $mode)
        <x-shipyard.ui.button
            :label="$label"
            icon="bullhorn"
            action="none"
            onclick="filterShowcases('{{ $mode }}')"
            @class([
                'toggle',
                'active' => $mode === 'reels',
            ])
        />
        @endforeach
    </x-slot:buttons>

    <div class="showcase-section flex down spaced" data-mode="reels">
        <h2>Najnowsze realizacje</h2>
        <x-front.showcase-reels :showcases="$showcases" />
    </div>

    <div class="showcase-section flex down spaced hidden" data-mode="list">
        <div id="songs">
            <h2>Wszystkie utwory, jakie mam w repertuarze</h2>
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
                    onclick="filterSongs(`dj`)"
                />

                @foreach ($genres as $genre)
                <x-shipyard.ui.button
                    action="none"
                    class="toggle"
                    :label="$genre->name"
                    icon="radio"
                    onclick="filterSongs(`dj`, 'genre', {{ $genre->id }})"
                />
                @endforeach

                {{--
                @foreach ($song_tags as $tag)
                <x-shipyard.ui.button
                    action="none"
                    class="toggle"
                    :label="$tag->name"
                    icon="tag"
                    onclick="filterSongs(`podklady`, 'tag', {{ $tag->id }})"
                />
                @endforeach
                --}}
            </div>

            <ul id="dj-song-list"><p class="grayed-out">Lista zostanie uzupełniona wkrótce</p></ul>
            <script defer>getSongList("dj");</script>
        </div>
    </div>
</x-front.tabbed-section>

<section id="prices" class="grid but-mobile-down" style="--col-count: 2;">
    <div class="backdropped rounded stagger" style="--stagger-index: 1;">
        <h1>Cennik</h1>
        <span class="yellowed-out">
            <i class="fas fa-triangle-exclamation"></i>
            Poniższe ceny mogą się różnić w zależności od kosztów dojazdu
        </span>
        <div class="front-table">
            {{-- <span class="scroll-hidden">Organy (ślub, jubileusz, ...)</span>
            <span class="scroll-hidden">{{ as_pln(300) }}</span>

            <span class="scroll-hidden">Trąbka (pogrzeb, ślub, ...)</span>
            <span class="scroll-hidden">{{ as_pln(100) }}</span> --}}
            <span>🚧 zostanie uzupełnione wkrótce</span>
        </div>
    </div>

    <div class="sc-line rounded stagger" style="--stagger-index: 2;">
        <x-sc-scissors />
        <h1>FAQ</h1>

        <ul id="faq">
            <li class="scroll-hidden">Jaki repertuar gram?</li>
            <li class="scroll-hidden">Specjalizuję się w graniu polskiego i angielskiego rocka. Gram również piosenki z innych gatunków, często rearanżując je, żeby były bardziej akustyczno-taneczne.</li>

            <li class="scroll-hidden">Czy mam własne nagłośnienie?</li>
            <li class="scroll-hidden ghost">🚧 zostanie uzupełnione wkrótce</li>

            <li class="scroll-hidden">Co z zapłatą?</li>
            <li class="scroll-hidden">W zupełności wystarcza mi przekazanie pieniędzy przed lub po uroczystości.</li>
        </ul>
    </div>
</section>

<x-shipyard.ui.button
    label="Złóż zapytanie o występ"
    icon="send"
    action="none"
    onclick="openModal('send-dj-request')"
    class="major primary"
/>
