<section id="offer">
    <h1>Jak mogę wzbogacić Twoją uroczystość?</h1>

    <div class="main rounded backdropped scroll-hidden stagger" style="--stagger-index: 1;">
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
    <div class="main rounded backdropped scroll-hidden stagger" style="--stagger-index: 2;">
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
    <div class="main rounded backdropped scroll-hidden stagger" style="--stagger-index: 3;">
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

<x-front.tabbed-section id="showcases" title="Z kim współpracuję" icon="disc">
    <x-slot:buttons>
        @foreach ([
            "samodzielnie" => "piano",
            "ViolArte" => "violin",
            "Ewelina Spławska" => "microphone",
        ] as $label => $icon)
        <x-shipyard.ui.button
            :label="$label"
            :icon="$icon"
            action="none"
            onclick="filterShowcases('{{ $label }}')"
            @class([
                "toggle",
                "active" => $label === "samodzielnie",
            ])
        />
        @endforeach
    </x-slot:buttons>

    <div class="showcase-section flex down spaced" data-mode="samodzielnie">
        <p>
            Mogę zagrać <strong>samodzielnie</strong>.
            Gram wówczas na organach lub na pianinie i śpiewam.
            Poniżej kilka moich losowych nagrań.
        </p>

        <x-front.showcase-reels :showcases="$showcases" />
    </div>

    <div class="showcase-section flex down spaced hidden" data-mode="ViolArte">
        <p>
            Często gram z zespołem <strong>ViolArte</strong> z Wolsztyna jako organista.
            Jest to zespół 4 muzyków, który uświetnia msze i imprezy okoliczonościowe śpiewem i grą na skrzypcach, flecie lub gitarze.
            Nawet śpiewamy na 4 głosy!
        </p>

        <x-shipyard.ui.button
            label="Więcej informacji"
            icon="open-in-new"
            action="https://www.facebook.com/profile.php?id=100024867817512"
            target="_blank"
            class="primary"
        />

        <div id="showcase-fbs">
            <iframe src="https://www.facebook.com/plugins/video.php?height=314&href=https%3A%2F%2Fwww.facebook.com%2F100024867817512%2Fvideos%2F692110923901667%2F&show_text=false&width=560&t=0" width="560" height="314" style="border:none;overflow:hidden" scrolling="no" frameborder="0" allowfullscreen="true" allow="autoplay; clipboard-write; encrypted-media; picture-in-picture; web-share" allowFullScreen="true"></iframe>
        </div>
    </div>

    <div class="showcase-section flex down spaced hidden" data-mode="Ewelina Spławska">
        <p>
            Współpracuję z <strong>Eweliną Spławską</strong>, grając w okolicach Wolsztyna.
            Razem śpiewamy i gramy na pianinie i organach.
        </p>

        <x-shipyard.ui.button
            label="Więcej informacji"
            icon="open-in-new"
            action="https://www.facebook.com/ewelinasplawska"
            target="_blank"
            class="primary"
        />

        <div id="showcase-fbs">
            <iframe src="https://www.tiktok.com/player/v1/7648021841923067158" width="300" height="575" allow="autoplay; clipboard-write; encrypted-media; picture-in-picture; web-share" allowfullscreen></iframe>
            <iframe src="https://www.facebook.com/plugins/video.php?height=476&href=https%3A%2F%2Fwww.facebook.com%2Fewelinasplawska%2Fvideos%2F1322933316175087%2F&show_text=false&width=267&t=0" width="300" height="575" style="border:none;overflow:hidden" scrolling="no" frameborder="0" allowfullscreen="true" allow="autoplay; clipboard-write; encrypted-media; picture-in-picture; web-share" allowFullScreen="true"></iframe>
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
            <h2 class="header scroll-hidden">Organy</h2>

            <span class="scroll-hidden">Msza ślubna</span>
            <span class="scroll-hidden">od {{ as_pln(270) }}</span>

            <span class="scroll-hidden">Msza jubileuszowa/okolicznościowa/pogrzebowa</span>
            <span class="scroll-hidden">od {{ as_pln(170) }}</span>

            <span class="scroll-hidden">Msza niedzielna</span>
            <span class="scroll-hidden">{{ as_pln(80) }}</span>

            <h2 class="header scroll-hidden">Trąbka</h2>

            <span class="scroll-hidden">Pogrzeb</span>
            <span class="scroll-hidden">od {{ as_pln(100) }}</span>
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

<x-shipyard.ui.button
    label="Złóż zapytanie o oprawę"
    icon="send"
    action="none"
    onclick="openModal('send-organista-request')"
    class="major primary"
/>
