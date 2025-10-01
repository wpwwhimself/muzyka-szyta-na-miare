@extends("layouts.app-front")
@section("title", setting("app_name"))

@section("content")

<script>
function openSection(slug) {
    document.querySelectorAll(`[role="service"]`).forEach(el => {
        el.classList.toggle("active", el.getAttribute("data-slug") === slug);
    });
    document.querySelectorAll(`[role="service-button"]`).forEach(el => {
        el.classList.toggle("active", el.getAttribute("data-slug") === slug);
    });

    jumpTo(`[role="service"][data-slug="${slug}"] #offer`);
}

function filterSongs(criterion = undefined, id = undefined) {
    const filterFunction = {
        genre: (song, needle, haystack) => haystack === needle,
        tag: (song, needle, haystack) => haystack.includes(needle)
    }

    let visible_songs = 0;
    document.querySelectorAll("#songs li").forEach(song => {
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
    document.querySelector("#songs-count").innerHTML = visible_songs
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
        .then(res => {
            if(res.length == 0) return;

            const list = document.querySelector("#songs ul");
            document.querySelector("#songs .grayed-out").remove();
            for(song of res) {
                const tags = song.tags?.map(tag => tag.id).join(",");

                list.append(`<li data-song-genre="${song.genre_id}" data-song-tags="${tags}">
                    <span>${song.title ?? 'utwór bez tytułu'}</span>
                    <span class='ghost'>${song.artist ?? ''}</span>
                    ${song.has_showcase_file ? `<span title="Posłuchaj próbki mojego wykonania"
                        class="clickable"
                        data-song-id="${song.id}"
                        data-song-title="${song.full_title}"
                        data-song-desc="${song.notes?.replace(/\n/g, '<br>') || ''}"
                    >💽</span>` : ``}
                </li>`);
            }
            list.insertAdjacentHTML('afterbegin', `<p>Razem: <b id="songs-count">${res.length}</b></p>`);

            document.querySelector("#song-loader").classList.add("hidden");

            const player = document.querySelector("#songs audio");
            $("#songs .clickable").click(function(){
                $("#songs .popup").addClass("open");

                const song_id = $(this).attr("data-song-id");
                const song_title = $(this).attr("data-song-title");
                const song_desc = $(this).attr("data-song-desc");

                $("#songs .popup .song-full-title").text(song_title)
                $("#songs .popup .song-desc").html(song_desc)

                player.src = `showcase/show/${song_id}`;
                player.load();
                player.addEventListener("canplay", () => {
                    startFilePlayer("")
                })
            });
            $("#songs #popup-close").click(function() {
                $("#songs .popup").removeClass("open");
                pauseFilePlayer("")
            })
        });
}
</script>

<x-section id="home" scissors>
    <div class="company-name flex right center">
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
    <p>Wybierz kategorię, aby dowiedzieć się więcej:</p>

    <div class="flex right center">
        @foreach ([
            ["Podkłady i nuty", "Nagrania i partytury", "podklady"],
            ["Organista", "Oprawa ślubów i mszy", "organista"],
            ["Imprezy i koncerty", "Muzyka na żywo", "dj"],
        ] as $i => [$label, $desc, $slug_part])
        <div class="section-like flex right keep-for-mobile center middle interactive backdropped stagger"
            onclick="openSection('{{ $slug_part }}')"
            role="service-button"
            data-slug="{{ $slug_part }}"
            style="--stagger-index: {{ $i + 1 }};"
        >
            <img src="{{ asset("assets/divisions/$slug_part.svg") }}" alt="logo"
                class="icon white-on-black"
            >
            <div>
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
