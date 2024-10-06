@extends("layouts.app-front")
@section("subtitle", "Oprawa mszy okolicznościowych w Twoim stylu")

@section("bullets")

<li>Szukasz zespołu, który uświetni Wasz ślub?</li>
<li>Potrzebujesz organisty na mszę jubileuszową lub urodzinową?</li>
<li>Chcesz wynająć trębacza na ceremonię pogrzebu?</li>
<strong>Jesteś we właściwym miejscu!</strong>

@endsection

@section("content")

<section id="offer">
    <h1>Jak mogę wzbogacić Twoją uroczystość?</h1>

    <div class="main black-back hidden">
        <x-mdi-book-cross height="6em" />
        <div>
            <h2>Organy</h2>
            <p>Od wielu lat gram na organach podczas mszy niedzielnych i okolicznościowych</p>
        </div>
        <ul>
            <li>Na <strong>lokalnym instrumencie</strong> <i class="fas fa-circle-question" @popper(...o ile proboszcz pozwoli grać)></i> lub moim własnym</li>
            <li>Akompaniament do wielu <strong>różnych pieśni</strong> i piosenek</li>
            <li>Nastrojowe <strong>improwizacje</strong> i bogaty repertuar <strong>psalmów</strong></li>
        </ul>
    </div>
    <div class="main black-back hidden">
        <x-mdi-piano height="6em" />
        <div>
            <h2>Pianino</h2>
            <p>Dodatkowy akcent muzyczny dla Twojej ceremonii</p>
        </div>
        <ul>
            <li><strong>Realistyczne brzmienie</strong> fortepianu</li>
            <li>Efekty dźwiękowe budujące <strong>nastrój</strong></li>
            <li>W utworach spoza repertuaru kościelnego</li>
        </ul>
    </div>
    <div class="main black-back hidden">
        <x-mdi-trumpet height="6em" />
        <div>
            <h2>Trąbka</h2>
            <p>Pozwól wybrzmieć pięknym melodiom</p>
        </div>
        <ul>
            <li>Trębacz podczas <strong>pogrzebu</strong></li>
            <li><strong>Solista</strong> z akompaniatorem</li>
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

    <ul class="flex-right center no-points">
        @foreach ([
            ["Shorty Organisty", "Nagrania mojej gry podczas mszy", "https://www.youtube.com/embed/videoseries?si=PPSi-adlexB7K5if&amp;list=PLpS8kGlqkYvS3nI6F_S2ZMchY3oS8VgjU"],
            ["Gig Vlog Organisty", "Dłuższe składanki zagranych przeze mnie mszy", "https://www.youtube.com/embed/videoseries?si=aWiggJ82p_zxHdzx&amp;list=PLpS8kGlqkYvQAj957C4JmjMwXmFSa9Z4_"],
        ] as [$label, $desc, $embed_link])
        <li class="hidden">
            <h2>{{ $label }}</h2>
            <p>{{ $desc }}</p>
            <iframe width="560" height="315" src="{{ $embed_link }}" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
        </li>
        @endforeach
    </ul>
</section>

<section id="prices" class="grid-2">
    <div class="black-back">
        <h1>Cennik</h1>
        <span class="yellowed-out">
            <i class="fas fa-triangle-exclamation"></i>
            Poniższe ceny mogą się różnić w zależności od kosztów dojazdu
        </span>
        <div class="front-table">
            <span class="hidden">Msza okolicznościowa (ślub, jubileusz, ...)</span>
            <span class="hidden">{{ as_pln(250) }}</span>
        </div>
    </div>

    <div class="sc-line">
        <x-sc-scissors />
        <h1>FAQ</h1>

        <p class="ghost">🚧 Zostanie dodane wkrótce</p>
        <ul id="faq">

        </ul>
    </div>
</section>


@endsection
