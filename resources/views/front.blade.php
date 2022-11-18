@extends('layouts.app-front')

@section('everything')
    <img id=scrolldown class="animatable" src="{{ asset("assets/front/img/scroll.png") }}" alt="scroll down to see more">
    <nav>
        <a href="#services"><li>Usługi</li></a>
        <a href="#recomms"><li>Klienci</li></a>
        <a href="#mymusic"><li>Próbki</li></a>
        <a href="#pricing"><li>Cennik</li></a>
        <a href="#contact"><li>Kontakt</li></a>
        @guest
        <a href="{{ route("login") }}" class="auth-link"><li>Zaloguj się</li></a>
        @endguest
        @auth
        <a href="{{ route("dashboard") }}" class="auth-link"><li>Moje projekty</li></a>
        @endauth
    </nav>
    <section id=home>
        @foreach (["success", "error"] as $status)
        @if (session($status))
            <x-alert :status="$status" />
        @endif
        @endforeach
        <ul class="shoutoutlist">
            <li class="disguised1 disguised2 animatable">Potrzebujesz aranżu lub podkładu do swojej ulubionej piosenki?</li>
            <li class="disguised1 disguised2 animatable">Planujesz nagrywać swoją muzykę lub szukasz muzyka na swój występ?</li>
            <li class="disguised1 disguised2 animatable">Masz pomysł na własny utwór i chcesz go wcielić w życie?</li>
        </ul>

        <img src="{{ asset("logo.png") }}" alt="logo" class="disguised3 animatable">
        <div class="title disguised3 animatable">
            <h1>WPWW</h1>
            <h2>Muzyka Szyta Na Miarę</h2>
        </div>
    </section>
    <section id="intro">
        <p>Od kilku lat pomagam osobom z różnych kręgów spełniać ich muzyczne marzenia. Z moją wiedzą i pasją stworzę (lub pomogę Ci stworzyć) specjalnie na Twoje życzenie utwory idealne na każdą okazję!</p>
        <ul class="dividebythree">
            <li class="spreadable animatable"><img class="shadow" src="{{ asset("assets/front/img/dostudia.jpg") }}" alt="do studia"><p class="shadow">Do studia</p></li>
            <li class="spreadable animatable"><img class="shadow" src="{{ asset("assets/front/img/nakoncert.jpg") }}" alt="na koncert"><p class="shadow">Na koncert</p></li>
            <li class="spreadable animatable"><img class="shadow" src="{{ asset("assets/front/img/nascenę.jpg") }}" alt="na scenę"><p class="shadow">Dla siebie</p></li>
            <li class="spreadable animatable"><div class="shadow" title="na podstawie wykonanych zleceń">średnio od <h3>1</h3>do<h3>4</h3>dni</div><p class="shadow">Szybka realizacja</p></li>
        </ul>
    </section>
    <section id="services">
        <h2>Jak mogę Ci pomóc?</h2>
        <ul class="dividebyfour">
            <li>
                <div class="imageinhere"><img src="{{ asset("assets/front/img/s_nagr.jpg") }}" alt="nagrania" class="animatable"></div>
                <h3>Podkłady i nagrania</h3>
                <ul>
                    <li>Profesjonalne podkłady muzyczne o szerokiej gamie instrumentalnej</li>
                    <li>Formaty <strong>WAVE, FLAC, MP3</strong>, a także wersje <strong>MIDI</strong></li>
                    <li><strong>Żywe instrumenty</strong> i wysokiej jakości sample</li>
                    <li>W <strong>dowolnym gatunku</strong> – od elektroniki po jazz i metal</li>
                </ul>
            </li>
            <li>
                <div class="imageinhere"><img src="{{ asset("assets/front/img/s_nuty.jpg") }}" alt="nuty" class="animatable"></div>
                <h3>Nuty i partytury</h3>
                <ul>
                    <li>Schematy <strong>akordów</strong> do szybkiego zagrania np. na gitarze</li>
                    <li><strong>Transkrypcje</strong> utworów muzycznych ze słuchu</li>
                    <li>Aranżacje wszelakich utworów <strong>na dowolny skład</strong> instrumentalny</li>
                    <li><strong>Kompozycje</strong> na specjalne życzenie klienta</li>
                </ul>
            </li>
            <li>
                <div class="imageinhere"><img src="{{ asset("assets/front/img/s_studio.jpg") }}" alt="studio" class="animatable"></div>
                <h3>Pomoc w nagraniach</h3>
                <ul>
                    <li><strong>Obróbka i mastering</strong> utworu</li>
                    <li><strong>Korekcje nagrań</strong> – m.in. podgłośnienie, przycinanie, odszumianie</li>
                    <li><strong>Osadzenie wokalu</strong> na nagraniu</li>
                </ul>
            </li>
            <li>
                <div class="imageinhere"><img src="{{ asset("assets/front/img/s_live.jpg") }}" alt="live" class="animatable"></div>
                <h3>Muzyka na żywo</h3>
                <ul>
                    <li>Występy na <strong>koncertach</strong>, przyjęciach, imprezach</li>
                    <li>Na niemal <strong>dowolnym</strong> instrumencie</li>
                    <li>Także w charakterze <strong>akompaniatora</strong>/tła muzycznego</li>
                </ul>
            </li>
        </ul>
        <div class=flexright><div class="animatable appearable"><h4 class="dummyanchor">Pełna lista usług</h4></div></div>
        <!-- CZARNA PŁACHTAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA -->
        <div id=alltheservices>
            <div class=singleservice>
                <h3>Podkład muzyczny (ścieżka dźwiękowa)</h3>
                <p>Podkład w wersji MP3, WAV lub FLAC. Na życzenie możliwym jest wykluczenie konkretnych partii instrumentalnych z finalnej wersji podkładu.</p>
            </div>
            <div class=singleservice>
                <h3>Podkład muzyczny MIDI</h3>
                <p>Podkład w wersji General MIDI. Na życzenie możliwym jest wykluczenie konkretnych partii instrumentalnych z finalnej wersji podkładu.</p>
            </div>
            <div class=singleservice>
                <h3>Partytura/Lead sheet</h3>
                <p>Rozpis dowolnego utworu na poszczególne partie instrumentalne.</p>
            </div>
            <div class=singleservice>
                <h3>Schematy akordowe</h3>
                <p>Uproszczona partia instrumentalna, zawierająca wszystkie znajdujące się w utworze akordy.</p>
            </div>
            <div class=singleservice>
                <h3>Aranżacja</h3>
                <p>Podkład lub rozpis nutowy wzorowany na bazowym utworze, gdzie moim zadaniem jest przygotować jego aranżację w konkretnym stylu muzycznym z zachowaniem najważniejszych elementów obu środowisk.</p>
            </div>
            <div class=singleservice>
                <h3>Klip filmowy</h3>
                <p>Usługa dodatkowa, jaką jest prosta wizualizacja filmowa na podstawie podkładu muzycznego. Możliwym jest też osadzenie tekstu piosenki.</p>
            </div>
            <div class=singleservice>
                <h3>Kompozycja od podstaw</h3>
                <p>Przygotowanie utworu muzycznego od zera, na zadany temat lub w konkretnym stylu. Możliwym jest spreparowanie partytury oraz podkładu muzycznego.</p>
            </div>
            <div class=singleservice>
                <h3>Osadzenie partii wokalnej</h3>
                <p>Przygotowanie utworu poprzez dodanie dostarczonego nagrania wokalu, bądź też osobiste jego nagranie.</p>
            </div>
            <div class=singleservice>
                <h3>Korekcja dźwiękowa</h3>
                <p>Naniesienie poprawek na dostarczony podkład muzyczny, np. zmiana tonacji czy głośności.</p>
            </div>
            <div class=singleservice>
                <h3>Tło muzyczne</h3>
                <p>Występ na żywo w formie akompaniatora lub tła muzycznego na gitarze lub pianinie.</p>
            </div>
            <div class=singleservice>
                <h3>Sub dla zespołu</h3>
                <p>Występ na żywo jako członek zespołu na dowolnym instrumencie.</p>
            </div>
        </div>
    </section>
    <section id="recomms">
        <h2>Kto już skorzystał?</h2>
        <div class="flexright">
            <div class="shadow animatable appearable">
                <img src="{{ asset("assets/front/img/recomms/1.jpg") }}" alt="main3" class="shadow">
                <h3>Ewelina Spławska</h3><h4>wokalistka</h4>
                <hr>
                <p>Mega polecam tego pana. Wszystko brzmi genialnie i profesjonalnie. Polecam z całego serduszka.</p>
            </div>
            <div class="shadow animatable appearable">
                <img src="{{ asset("assets/front/img/recomms/2.jpg") }}" alt="main3" class="shadow">
                <h3>Krzysztof „Bajek” Bajeński</h3><h4>muzyk, producent</h4>
                <hr>
                <p>Pełen profesjonalizm – tak określiłbym współpracę z Wojtkiem. Człowiek gotowy zawsze do pracy i w pełni zaangażowany. Rzeczy, które wychodzą spod jego ręki są na bardzo wysokim poziomie. Za nami bardzo dużo ciekawych projektów, myślę jednak, że przed nami jeszcze więcej.</p>
            </div>
            <div class="shadow animatable appearable">
                <img src="{{ asset("assets/front/img/recomms/0.png") }}" alt="main3" class="shadow">
                <h3>Grzegorz Bednarczyk</h3><h4>klient, zamówił podkład do studia</h4>
                <hr>
                <p>Córka wyszła właśnie ze studia nagrań; pan nagrywający jest pełen podziwu podkładu i jakości wykonania. Sami pewnie jeszcze nie raz skorzystamy z usług.</p>
            </div>
        </div>
        <div>
            <iframe src="https://www.facebook.com/plugins/post.php?href=https%3A%2F%2Fwww.facebook.com%2Fjedrek.kocjan%2Fposts%2F1882616848543669&show_text=true&width=500" width="500" height="188" style="border:none;overflow:hidden" scrolling="no" frameborder="0" allowfullscreen="true" allow="autoplay; clipboard-write; encrypted-media; picture-in-picture; web-share"></iframe>
            <iframe src="https://www.facebook.com/plugins/post.php?href=https%3A%2F%2Fwww.facebook.com%2Fwiktoria.matysik.7%2Fposts%2F1281876378821393&show_text=true&width=500" width="500" height="158" style="border:none;overflow:hidden" scrolling="no" frameborder="0" allowfullscreen="true" allow="autoplay; clipboard-write; encrypted-media; picture-in-picture; web-share"></iframe>
            <iframe src="https://www.facebook.com/plugins/post.php?href=https%3A%2F%2Fwww.facebook.com%2FwpwwMuzykaSzytaNaMiare%2Fposts%2F862916034490127&show_text=true&width=500" width="500" height="169" style="border:none;overflow:hidden" scrolling="no" frameborder="0" allowfullscreen="true" allow="autoplay; clipboard-write; encrypted-media; picture-in-picture; web-share"></iframe>
        </div>
        <div class=flexright><div class="shadow animatable appearable"><h4 class="dummyanchor">Zobacz więcej opinii</h4></div></div>
        <!-- CZARNA PŁACHTAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA -->
        <div id=alltherecomms>
            <quote>Zadowoleni to mało powiedziane! Jestem wręcz zachwycona zarówno jakością, brzmieniem oddającym charakter oryginału, a jednocześnie bardziej nowocześnie brzmiącym niż oryginał, no i szybkością wykonania zlecenia. Gratuluję solidności i ogromnego talentu.</quote>
            <p>Agnieszka</p>

            <quote>Pan to potrafi zadziwić! A chórek to „klękajcie, narody”! Nie mam nic do dodania; po prostu rewelacyjny podkład.</quote>
            <p>Piotr</p>

            <hr>
            <quote class=disclaimer>Powyższe wypowiedzi zostały zacytowane bezpośrednio z rozmów mailowych lub telefonicznych z klientami</quote>
        </div>
        <h2>Współpracuję również z:</h2>
        <div class="forceflexright">
            <span class="shadow animatable appearable">
                <img src="{{ asset("assets/front/img/recomms/pwod.png") }}" alt="recomms">
                <p class="animatable">Powiatowa Wolsztyńska Orkiestra Dęta</p>
            </span>
            <span class="shadow animatable appearable">
                <img src="{{ asset("assets/front/img/recomms/gckib.png") }}" alt="recomms">
                <p class="animatable">Gminne Centrum Kultury i Biblioteka w Przemęcie</p>
            </span>
        </div>
        <h2>Mam za sobą już <span id="ileprojektow">kilkaset</span> zamówień (w tym <span id="ileautorskich">wiele</span> piosenek autorskich)</h2>
        <a href="http://projects.wpww.pl/"><h4 class="dummyanchor">Lista moich projektów</h4></a>
    </section>
    <section id="mymusic">
        <h2>Efekty moich prac</h2>
        <div class=flexright>
            <iframe width="560" height="315" src="https://www.youtube.com/embed/RRGUBczaxQc" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
            <iframe width="560" height="315" src="https://www.youtube.com/embed/4l0n_VqYBUk" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
        </div>
        <div>
            <!-- <div class="sampleproj">
                <p>--</p>
                <audio controls><source type="audio/mp3" src="/samples/ZXX.mp3"></audio>
            </div> -->
            <div class="sampleproj">
                <p>Solo fortepian</p>
                <audio controls><source type="audio/mp3" src="{{ asset("assets/front/showcase/Z4O.mp3") }}"></audio>
                <audio controls><source type="audio/mp3" src="{{ asset("assets/front/showcase/Z94.mp3") }}"></audio>
                <audio controls><source type="audio/ogg" src="{{ asset("assets/front/showcase/piano_ZCJ.ogg") }}"></audio>
            </div>
            <div class="sampleproj">
                <p>Piosenka aktorska</p>
                <audio controls><source type="audio/mp3" src="{{ asset("assets/front/showcase/Z4E.mp3") }}"></audio>
                <audio controls><source type="audio/mp3" src="{{ asset("assets/front/showcase/Z97.mp3") }}"></audio>
                <audio controls><source type="audio/ogg" src="{{ asset("assets/front/showcase/aktorska_ZC1.ogg") }}"></audio>
            </div>
            <div class="sampleproj">
                <p>Dla dzieci</p>
                <audio controls><source type="audio/mp3" src="{{ asset("assets/front/showcase/Z3M.mp3") }}"></audio>
                <audio controls><source type="audio/ogg" src="{{ asset("assets/front/showcase/kids_Z9K.ogg") }}"></audio>
            </div>
            <div class="sampleproj">
                <p>Ballada</p>
                <audio controls><source type="audio/mp3" src="{{ asset("assets/front/showcase/Z4T.mp3") }}"></audio>
                <audio controls><source type="audio/mp3" src="{{ asset("assets/front/showcase/Z98.mp3") }}"></audio>
                <audio controls><source type="audio/ogg" src="{{ asset("assets/front/showcase/ballad_ZCT.ogg") }}"></audio>
            </div>
            <div class="sampleproj">
                <p>Rock</p>
                <audio controls><source type="audio/mp3" src="{{ asset("assets/front/showcase/Z34.mp3") }}"></audio>
                <audio controls><source type="audio/mp3" src="{{ asset("assets/front/showcase/Z91.mp3") }}"></audio>
                <audio controls><source type="audio/ogg" src="{{ asset("assets/front/showcase/rock_ZCK.ogg") }}"></audio>
            </div>
            <div class="sampleproj">
                <p>Metal</p>
                <audio controls><source type="audio/mp3" src="{{ asset("assets/front/showcase/Z4F.mp3") }}"></audio>
            </div>
            <div class="sampleproj">
                <p>Reggae</p>
                <audio controls><source type="audio/mp3" src="{{ asset("assets/front/showcase/Z4M.mp3") }}"></audio>
            </div>
            <div class="sampleproj">
                <p>Biesiadne</p>
                <audio controls><source type="audio/mp3" src="{{ asset("assets/front/showcase/Z4P.mp3") }}"></audio>
                <audio controls><source type="audio/mp3" src="{{ asset("assets/front/showcase/Z9N.mp3") }}"></audio>
                <audio controls><source type="audio/ogg" src="{{ asset("assets/front/showcase/biesiada_ZCQ.ogg") }}"></audio>
            </div>
            <div class="sampleproj">
                <p>Disco polo</p>
                <audio controls><source type="audio/mp3" src="{{ asset("assets/front/showcase/Z4Q.mp3") }}"></audio>
                <audio controls><source type="audio/mp3" src="{{ asset("assets/front/showcase/Z9G.mp3") }}"></audio>
                <audio controls><source type="audio/ogg" src="{{ asset("assets/front/showcase/discopolo_ZCN.ogg") }}"></audio>
            </div>
            <div class="sampleproj">
                <p>Country</p>
                <audio controls><source type="audio/mp3" src="{{ asset("assets/front/showcase/Z45.mp3") }}"></audio>
                <audio controls><source type="audio/mp3" src="{{ asset("assets/front/showcase/Z92.mp3") }}"></audio>
                <audio controls><source type="audio/ogg" src="{{ asset("assets/front/showcase/country_ZCS.ogg") }}"></audio>
            </div>
            <div class="sampleproj">
                <p>Jazz</p>
                <audio controls><source type="audio/mp3" src="{{ asset("assets/front/showcase/Z33.mp3") }}"></audio>
                <audio controls><source type="audio/ogg" src="{{ asset("assets/front/showcase/jazz_ZCW.ogg") }}"></audio>
            </div>
            <div class="sampleproj">
                <p>Blues</p>
                <audio controls><source type="audio/mp3" src="{{ asset("assets/front/showcase/Z3N.mp3") }}"></audio>
                <audio controls><source type="audio/mp3" src="{{ asset("assets/front/showcase/Z9E.mp3") }}"></audio>
                <audio controls><source type="audio/ogg" src="{{ asset("assets/front/showcase/blues_ZCL.ogg") }}"></audio>
            </div>
        </div>
        <h2>Przed i po</h2>
        <div class=flexright>
            <p>zostaną dodane wkrótce...</p>
        </div>
        <a href='https://www.facebook.com/wpwwMuzykaSzytaNaMiare/videos' target='_blank'><h4 class="dummyanchor">Więcej próbek</h4></a>
        <h2>Przykłady mojej własnej twórczości</h2>
        <div class=flexright>
            <iframe width="560" height="315" src="https://www.youtube.com/embed/1ojXryJAf44" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
            <iframe width="560" height="315" src="https://www.youtube.com/embed/zKBSMTMQJ3I" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
            <iframe width="560" height="315" src="https://www.youtube.com/embed/iaAI4pbfDIE" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
        </div>
    </section>
    <section id="pricing">
        <div class="flexright"><div>
        <h2>Ceny</h2>
        <table>
            <tr class="animatable appearable"><th colspan=2 class="shadow">Podkłady muzyczne</th></tr>
            <tr class="animatable appearable">
                <td>Korekcja/przeróbka gotowego podkładu</td>
                <td>20 zł</td>
            </tr>
            <tr class="animatable appearable">
                <td>Kameralny (np. solo pianino lub duet)</td>
                <td>50 zł</td>
            </tr>
            <tr class="animatable appearable">
                <td>Typowy (np. zespół rockowy)</td>
                <td>70 zł</td>
            </tr>
            <tr class="animatable appearable">
                <td>Pełny (np. orkiestra)</td>
                <td>90 zł</td>
            </tr>
            <tr class="animatable appearable">
                <td>Aranżacja istniejącego utworu</td>
                <td>+20 zł</td>
            </tr>
            <tr class="animatable appearable">
                <td>Podkład pod własny utwór</td>
                <td>150 zł</td>
            </tr>
            <tr class="animatable appearable">
                <td>Kompozycja od podstaw</td>
                <td>250 zł</td>
            </tr>
            <tr class="animatable appearable"><th colspan=2 class="shadow">Zapis nutowy</th></tr>
            <tr class="animatable appearable">
                <td>Przygotowanie melodii/akordów piosenki</td>
                <td>30 zł</td>
            </tr>
            <tr class="animatable appearable">
                <td>Transkrypcja</td>
                <td>od 140 zł</td>
            </tr>
            <tr class="animatable appearable">
                <td>Aranż</td>
                <td>od 200 zł</td>
            </tr>
            <tr class="animatable appearable">
                <td>Kompozycja od podstaw</td>
                <td>od 340 zł</td>
            </tr>
            <tr class="animatable appearable"><th colspan=2 class="shadow">Nagrania utworów</th></tr>
            <tr class="animatable appearable">
                <td>Osadzenie wokalu do dostarczonego przez siebie podkładu</td>
                <td>100 zł</td>
            </tr>
            <tr class="animatable appearable">
                <td>Osadzenie wokalu do podkładu zrobionego przeze mnie</td>
                <td>w cenie</td>
            </tr>
            <tr class="animatable appearable">
                <td>Przygotowanie prostej wizualizacji filmowej</td>
                <td>od 40 zł</td>
            </tr>
            <tr class="animatable appearable"><th colspan=2 class="shadow">Granie na żywo</th></tr>
            <tr class="animatable appearable">
                <td>Akompaniament na gitarze/pianinie</td>
                <td>od 200 zł/h</td>
            </tr>
            <tr class="animatable appearable">
                <td>Jako członek zespołu (instrument do wyboru)</td>
                <td>od 250 zł/h</td>
            </tr>
        </table>
        </div><div>
        <h2>FAQ</h2>
        <ul>
            <li class="animatable appearable">Jak tworzone są utwory?</li>
            <li class="animatable appearable">Każdy utwór i podkład przygotowany jest od zera. Nagrania poszczególnych partii są wykonywane w całości przeze mnie. Dotyczy to również dogrywania ewentualnych drugich głosów i chórków. <i>Nie potrafię po prostu usunąć wokalu z nagrania</i>.</li>

            <li class="animatable appearable">Jakie materiały muszę przygotować?</li>
            <li class="animatable appearable">Jestem w stanie przygotować podkład na podstawie istniejącego już utworu (nagranie czy nawet zapis nutowy), przekazanej melodii, samych wskazówek stylistycznych. W skrajnych przypadkach możliwe jest też całkowite powierzenie mi aranżacji.</li>

            <li class="animatable appearable">Czy mój podkład będzie miał linię melodyczną?</li>
            <li class="animatable appearable"><strong>Z reguły nie</strong>, ale jeśli jesteś zainteresowany takową, proszę o dodatkową informację.</li>

            <li class="animatable appearable">Jak szybko można się spodziewać gotowego podkładu?</li>
            <li class="animatable appearable"><!-- W związku z moimi studiami projekty wykonuję w weekendy, zwykle jeden wystarcza. -->Zwykle projekty jestem w stanie wykonać w 1-3 dni, choć wszystko zależy od tego, jak mi studia pozwolą pracować. <strong>Nie rozpoczynam jednak pracy przed zgromadzeniem kompletu informacji</strong> – dlatego właśnie oczekuję na odpowiedź na każdego wysłanego przeze mnie maila.</li>

            <li class="animatable appearable">Czy możliwe są poprawki w przygotowywanych aranżach?</li>
            <li class="animatable appearable">Oczywiście. Efekty mojej pracy zawsze przedstawiam do recenzji, gdzie można wskazać elementy utworu, które nie przypadną Ci do gustu. Poprawki najczęściej nie wpływają na wycenę zlecenia.</li>

            <li class="animatable appearable">Co z zapłatą za utwór?</li>
            <li class="animatable appearable">Wycena zlecenia zostanie przesłana do Ciebie mailem przed jego podjęciem. Otrzymasz także numer konta do przelewu. <i>Nie musisz płacić od razu!</i> Wpłata jest potrzebna jedynie do możliwości pobrania plików.</li>
        </ul>
        </div></div>
    </section>
    <section id="contact">
        <h2>Napisz już teraz!</h2>
        <form method="post" action="{{ route("mod-request-back") }}" id='contactform'>
            @csrf
            <div>
                <h3>Szczegóły zlecenia</h3>
                <label for="quest_type">Rodzaj zlecenia</label>
                <select name="quest_type">
                @foreach ($quest_types as $key => $val)
                    <option value="{{ $key }}">{{ $val }}</option>
                @endforeach
                </select>
                <label for="m_title">Tytuł utworu</label>
                <input type="text" id="m_title" name="title" class="animatable"></input>
                <label for="m_artist">Wykonawca</label>
                <input type="text" id="m_artist" name="artist" class="animatable"></input>
                <label for="m_link">Linki do nagrań<br><i>(oddzielone przecinkiem)</i></label>
                <input type="url" id="m_link" name="link" class="animatable"></input>
                <label for="m_req">Jakie są Twoje życzenia?<br><i>(np. styl, czy z linią melodyczną itp.)</i></label>
                <textarea id="m_req" name="wishes" class="animatable"></textarea>
                <label for="m_date">Na kiedy jest potrzebne?<br><i>(opcjonalnie)</i></label>
                <input type="date" id="m_date" name="hard_deadline" class="animatable"></input>
            </div>
            <div>
                <h3>Twoje dane</h3>
                <label for="m_name">Imię i nazwisko</label>
                <input type="text" id="m_name" name="client_name" placeholder="Jan Kowalski" class="animatable"></input>
                <label>Jak mogę do Ciebie dotrzeć?<br><i>(wypełnij co najmniej jedno, choć zachęcam do podania maila)</i></label>
                <input type="email" id="m_mail" name="email" placeholder="jankowalski@poczta.pl" class="animatable"></input>
                <input type="tel" id="m_tel" name="phone" placeholder="123456789" class="animatable"></input>
                <input type="text" id="m_other" name="other_medium" placeholder="inna forma kontaktu" class="animatable"></input>
                <label for="contact_preference">Preferowana forma kontaktu</label>
                <select name="contact_preference" id="contact_preference">
                    <option value="email">email</option>
                    <option value="telefon">telefon</option>
                    <option value="sms">SMS</option>
                    <option value="inne">inne</option>
                </select>
                <label for="m_test">Cztery razy pięć?</label>
                <input type="number" id="m_test" name="m_test" required></input>
                <input type="submit" name="m_sub" value="Wyślij" class="animatable"></input>
            </div>
        </form>
        <div class="flexright">
        <a href="mailto:contact@wpww.pl">
            <img alt='email' src="{{ asset("assets/front/img/contact/email.png") }}">
            contact@wpww.pl
        </a>
        <a href="https://www.facebook.com/wpwwMuzykaSzytaNaMiare">
            <img alt='facebook' src="{{ asset("assets/front/img/contact/faceb.png") }}">
            /wpwwMuzykaSzytaNaMiare
        </a>
        <a href="callto:+48530268000">
            <img alt='phone' src="{{ asset("assets/front/img/contact/phone.png") }}">
            +48 530 268 000
        </a>
        <a href="https://www.google.com/maps/place/62-068+%C5%81%C4%85kie/@52.102515,16.228492,87889m/data=!3m1!1e3!4m5!3m4!1s0x4705cff1dac9cfc3:0x6cb74d79e1e1973a!8m2!3d52.1052491!4d16.2258752?hl=pl">
            <img alt='address' src="{{ asset("assets/front/img/contact/adres.png") }}">
            Łąkie 62<br>62-068 Rostarzewo
        </a>
        </div>
        <h2>Poczytaj i posłuchaj:</h2>
        <div class="flexright">
        <a href="http://wpww.pl">
            <img alt='WPWW' src="http://wpww.pl/media/logo.png" class="noinv">
            O mnie
        </a>
        <a href="http://projects.wpww.pl">
            <img alt='Brzoskwinia' src="http://projects.wpww.pl/interface/logo.png" class="noinv">
            Moje projekty
        </a>
        </div>
        <x-footer />
    </section>
    @include('cookie-consent::index')
@endsection
