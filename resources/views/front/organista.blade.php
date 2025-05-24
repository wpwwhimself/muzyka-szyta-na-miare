@extends("layouts.app-front")
@section("subtitle", "Oprawa mszy okolicznościowych w Twoim stylu")

@section("bullets")

<li>Szukasz muzyka lub zespołu, który uświetni Wasz ślub?</li>
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
            <li>Nastrojowe <strong>improwizacje</strong> i bogaty repertuar melodii <strong>psalmów</strong></li>
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
            <li>Melancholijne utwory odpowiednie do okazji</li>
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

    <x-front.showcase-reels :showcases="$showcases" />
</section>

<section id="prices" class="grid-2">
    <div class="black-back">
        <h1>Cennik</h1>
        <span class="yellowed-out">
            <i class="fas fa-triangle-exclamation"></i>
            Poniższe ceny mogą się różnić w zależności od kosztów dojazdu
        </span>
        <div class="front-table">
            <span class="hidden">Organy (ślub, jubileusz, ...)</span>
            <span class="hidden">{{ as_pln(350) }}</span>

            <span class="hidden">Trąbka (pogrzeb, ślub, ...)</span>
            <span class="hidden">{{ as_pln(100) }}</span>
        </div>
    </div>

    <div class="sc-line">
        <x-sc-scissors />
        <h1>FAQ</h1>

        <ul id="faq">
            <li class="hidden">Jaki repertuar gram?</li>
            <li class="hidden">Gram pieśni eucharystyczne, ale nie tylko. Na msze okolicznościowe gram pieśni dopasowane do okazji. Mogę również zagrać utwory <strong>na życzenie</strong>.</li>

            <li class="hidden">Czy mam własny instrument?</li>
            <li class="hidden">Preferuję grę na lokalnym instrumencie, ale jeśli go nie ma lub nie ma pozwolenia na grę na nim, jestem w stanie grać na <b>własnych organach (elektrycznych) z własnym nagłośnieniem</b>.</li>

            <li class="hidden">Czy współpracuję z innymi muzykami?</li>
            <li class="hidden">Jeśli podczas uroczystości ma zaśpiewać/zagrać również ktoś inny, to jestem w stanie tej osobie akompaniować. Proszę tylko o stosowną informację wcześniej, żeby móc się dogadać z innymi muzykami.</li>

            <li class="hidden">Co z zapłatą?</li>
            <li class="hidden">W zupełności wystarcza mi przekazanie pieniędzy przed uroczystością.</li>

            <li class="hidden">Podpisujemy umowę?</li>
            <li class="hidden">Ja nie widzę takiej potrzeby – jeśli termin zostanie przez nas uzgodniony i mnie on pasuje, to zobowiązuję się przyjechać na uroczystość.</li>
        </ul>
    </div>
</section>

@endsection
