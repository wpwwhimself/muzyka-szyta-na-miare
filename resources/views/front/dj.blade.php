@extends("layouts.app-front")
@section("subtitle", "Muzyka na żywo taka, jaką chcesz")

@section("bullets")

<li>Szukasz zespołu na koncert?</li>
<li>Potrzebujesz DJa na wesele lub imprezę?</li>
<li>Chcesz, żeby muzyka, do jakiej się bawisz, była grana na żywo?</li>
<strong>Jesteś we właściwym miejscu!</strong>

@endsection

@section("content")

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

<section id="showcases">
    <h1>Posłuchaj, jak brzmię</h1>

    <p>🚧 Na razie nie mam co pokazać... Wkrótce coś tu będzie</p>
</section>

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

@endsection
