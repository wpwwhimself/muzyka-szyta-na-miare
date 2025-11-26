@extends("layouts.app-front")
@section("title", setting("app_name"))

@section("content")

<script>
function jumpTo(selector) {
    document.querySelector(selector).scrollIntoView({
        behavior: "smooth",
        block: "start",
    });
}

function openSection(slug) {
    document.querySelectorAll(`[role="service"]`).forEach(el => {
        el.classList.toggle("active", el.getAttribute("data-slug") === slug);
    });
    document.querySelectorAll(`[role="service-button"]`).forEach(el => {
        el.classList.toggle("active", el.getAttribute("data-slug") === slug);
    });

    jumpTo(`#jump-target`);
}

function filterSongs(domain, criterion = undefined, id = undefined) {
    const filterFunction = {
        genre: (song, needle, haystack) => haystack === needle,
        tag: (song, needle, haystack) => haystack.includes(needle)
    }

    let visible_songs = 0;
    document.querySelectorAll(`#songs ul#${domain}-song-list li`).forEach(song => {
        const haystacks = {
            genre: parseInt(song.getAttribute("data-song-genre")),
            tag: song.getAttribute("data-song-tags").split(",").map(parseInt)
        }

        if (criterion === undefined) {
            song.classList.remove("hidden")
            visible_songs++
            return
        }

        if (!filterFunction[criterion](song, parseInt(id), haystacks[criterion])) {
            song.classList.add("hidden")
        } else {
            song.classList.remove("hidden")
            visible_songs++
        }
    })
    document.querySelector(`#${domain}-songs-count`).innerHTML = visible_songs
}

function filterShowcases(mode) {
    document.querySelectorAll("#showcases .showcase-section").forEach(section => {
        if (section.getAttribute("data-mode") === mode) {
            section.classList.remove("hidden")
        } else {
            section.classList.add("hidden")
        }
    })
}

function getSongList(domain = undefined) {
    /**
     * load and display songs
     * it's set here to accelerate loading speed
     */
    fetch("/api/songs/info" + (domain ? "?for=" + domain : ""))
        .then(res => res.json())
        .then(({data, table}) => {
            const list = document.querySelector(`#songs ul#${domain}-song-list`);
            list.replaceWith(fromHTML(table));
            list.insertAdjacentHTML('afterbegin', `<p>Razem: <b id="${domain}-songs-count">${data.length}</b></p>`);
        });
}

function startDemo(song_id) {
    const popup = document.querySelector("#song-demo-popup");
    const player = popup.querySelector("audio");

    player.src = `showcase/show/${song_id}`;
    player.parentElement.classList.remove("hidden");
    player.load();
    player.addEventListener("canplay", () => {
        startFilePlayer("");
    });
}

function openSongDemo(song_id = undefined, song_title = undefined, song_desc = undefined) {
    const popup = document.querySelector("#song-demo-popup");
    const player = popup.querySelector("audio");

    popup.classList.toggle("open", song_id !== undefined);

    if (song_id == undefined) {
        pauseFilePlayer("");
    }

    popup.querySelector(".song-full-title").innerHTML = song_title;

    if (song_id !== undefined) {
        startDemo(song_id);
    }
}

function openCompositionDemos(composition_id = undefined) {
    const popup = document.querySelector("#song-demo-popup");
    const player = popup.querySelector("audio");
    const loader = popup.querySelector(".loader");

    popup.classList.toggle("open", composition_id !== undefined);

    popup.querySelector(".song-full-title").innerHTML = "";
    popup.querySelector(".song-list").innerHTML = "";

    if (composition_id == undefined) {
        pauseFilePlayer("");
    } else {
        loader.classList.remove("hidden");
        player.parentElement.classList.add("hidden");

        fetch(`/api/songs/compositions/${composition_id}`)
            .then(res => res.json())
            .then(({composition, songs}) => {
                popup.querySelector(".song-full-title").innerHTML = composition.full_title;
                songs.forEach(song => {
                    popup.querySelector(".song-list").innerHTML += `<li>
                        ${song.full_title}
                        ${song.has_showcase_file
                            ? `<span class="interactive accent primary" onclick="startDemo('${song.id}')">
                                <x-shipyard.app.icon :name="model_icon('songs')" />
                            </span>`
                            : ""
                        }
                    </li>`;
                });
            })
            .finally(() => {
                loader.classList.add("hidden");
            });
    }
}
</script>

<x-section id="home" scissors>
    <div class="company-name flex right but-mobile-down center">
        <img src="{{ asset("msznm.svg") }}" alt="logo" class="logo">
        <div>
            <h1>{{ setting("app_name") }}</h1>
            <p>Wojciech Przybyła</p>
            <h2>
                <span class="appear-cycle">
                    <span>Podkłady i aranże</span>
                    <span>Msze i uroczystości</span>
                    <span>Imprezy i koncerty</span>
                </span><br>
                dostosowane do<br>
                Twoich potrzeb
            </h2>
        </div>
    </div>
</x-section>

<div role="services">
    <p id="jump-target">Wybierz kategorię, aby dowiedzieć się więcej:</p>

    <div class="flex right center">
        @foreach ([
            ["Podkłady i nuty", "Nagrania i partytury", "podklady"],
            ["Organista", "Oprawa ślubów i mszy", "organista"],
            ["Imprezy i koncerty", "Muzyka na żywo", "dj"],
        ] as $i => [$label, $desc, $slug_part])
        <div class="section flex right center middle interactive backdropped stagger"
            onclick="openSection('{{ $slug_part }}')"
            role="service-button"
            data-slug="{{ $slug_part }}"
            style="--stagger-index: {{ $i + 1 }};"
        >
            <img src="{{ asset("assets/divisions/$slug_part.svg") }}" alt="logo"
                class="icon white-on-black"
            >
            <div class="service-label">
                <h2>{{ $label }}</h2>
                <p>{{ $desc }}</p>
            </div>
        </div>
        @endforeach
    </div>
</div>

<div role="service" data-slug="podklady" class="animatable stagger">
    <x-front.podklady />
</div>

<div role="service" data-slug="organista" class="animatable stagger">
    <x-front.organista />
</div>

<div role="service" data-slug="dj" class="animatable stagger">
    <x-front.dj />
</div>

<section id="about">
    <h1>O mnie</h1>
    <div class="flex right center">
        <img class="photo rounded" src="{{ asset("assets/front/img/home_me.jpg") }}" alt="me!">
        <ul>
            <li>Mam na imię Wojtek i muzyką profesjonalnie zajmuję się od <b>ponad {{ date("Y") - 2012 }} lat</b></li>
            <li>Ukończyłem <b>szkołę muzyczną</b> I stopnia na gitarze</li>
            <li>Gram na wielu instrumentach, w tym <b>klawiszowych, perkusyjnych oraz dętych</b></li>
            <li>Jestem stałym członkiem <b>2 zespołów muzycznych</b>:
                <a href="https://www.facebook.com/profile.php?id=100060053047728">Dixie Kings</a>
                oraz
                <a href="https://www.facebook.com/orkiestrawihajster">Orkiestry Tanecznej Wihajster</a>
            </li>
            <li>Z wykształcenia <b>jestem informatykiem</b>, obecnie pracuję jako software developer</li>
            <li>Mam za sobą <b>studia magisterskie</b> z matematyki i informatyki</li>
        </ul>
    </div>
</section>

<div id="song-demo-popup" class="popup">
    <x-shipyard.app.loader />

    <div class="popup-contents flex down center middle">
        <h3 class="song-full-title"></h3>
        <h4 class="ghost">Aranże, jakie wykonałem:</h4>
        <ul class="song-list"></ul>
        <p class="ghost">
            Kliknij ikonę <span class="accent primary">
                <x-shipyard.app.icon :name="model_icon('songs')" />
            </span>, aby odtworzyć próbkę
        </p>

        <x-file-player type="ogg" file="" is-showcase />

        <x-shipyard.ui.button
            label="Zamknij"
            icon="close"
            action="none"
            onclick="openSongDemo();"
            class="tertiary"
        />
    </div>
</div>

@endsection

@section("appends")

<script>
/**
 * animate on scroll
 */
const observer = new IntersectionObserver((entries) => {
    entries.forEach((entry) => {
        if (entry.isIntersecting) {
            entry.target.classList.remove("scroll-hidden");
        }
    });
});

const hiddenElements = document.querySelectorAll(".scroll-hidden");
hiddenElements.forEach((el) => observer.observe(el));
</script>

@endsection
