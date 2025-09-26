<section id="offer">
    <h1>Jak mogę wzbogacić Twoją uroczystość?</h1>

    <div class="main rounded black-back scroll-hidden stagger" style="--stagger-index: 1;">
        <x-shipyard.app.icon name="book-cross" />
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
    <div class="main rounded black-back scroll-hidden stagger" style="--stagger-index: 2;">
        <x-shipyard.app.icon name="piano" />
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
    <div class="main rounded black-back scroll-hidden stagger" style="--stagger-index: 3;">
        <x-shipyard.app.icon name="trumpet" />
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

<section id="showcases">
    <h1>Posłuchaj, jak brzmię</h1>

    <x-front.showcase-reels :showcases="$showcases" />
</section>

<section id="prices" class="grid" style="--col-count: 2;">
    <div class="black-back rounded stagger" style="--stagger-index: 1;">
        <h1>Cennik</h1>
        <span class="yellowed-out">
            <i class="fas fa-triangle-exclamation"></i>
            Poniższe ceny mogą się różnić w zależności od kosztów dojazdu
        </span>
        <div class="front-table">
            <span class="scroll-hidden">Organy (ślub, jubileusz, ...)</span>
            <span class="scroll-hidden">{{ as_pln(350) }}</span>

            <span class="scroll-hidden">Trąbka (pogrzeb, ślub, ...)</span>
            <span class="scroll-hidden">{{ as_pln(100) }}</span>
        </div>
    </div>

    <div class="sc-line rounded stagger" style="--stagger-index: 2;">
        <x-sc-scissors />
        <h1>FAQ</h1>

        <ul id="faq">
            <li class="scroll-hidden">Jaki repertuar gram?</li>
            <li class="scroll-hidden">Gram pieśni eucharystyczne, ale nie tylko. Na msze okolicznościowe gram pieśni dopasowane do okazji. Mogę również zagrać utwory <strong>na życzenie</strong>.</li>

            <li class="scroll-hidden">Czy mam własny instrument?</li>
            <li class="scroll-hidden">Preferuję grę na lokalnym instrumencie, ale jeśli go nie ma lub nie ma pozwolenia na grę na nim, jestem w stanie grać na <b>własnych organach (elektrycznych) z własnym nagłośnieniem</b>.</li>

            <li class="scroll-hidden">Czy współpracuję z innymi muzykami?</li>
            <li class="scroll-hidden">Jeśli podczas uroczystości ma zaśpiewać/zagrać również ktoś inny, to jestem w stanie tej osobie akompaniować. Proszę tylko o stosowną informację wcześniej, żeby móc się dogadać z innymi muzykami.</li>

            <li class="scroll-hidden">Co z zapłatą?</li>
            <li class="scroll-hidden">W zupełności wystarcza mi przekazanie pieniędzy przed uroczystością.</li>

            <li class="scroll-hidden">Podpisujemy umowę?</li>
            <li class="scroll-hidden">Ja nie widzę takiej potrzeby – jeśli termin zostanie przez nas uzgodniony i mnie on pasuje, to zobowiązuję się przyjechać na uroczystość.</li>
        </ul>
    </div>
</section>
