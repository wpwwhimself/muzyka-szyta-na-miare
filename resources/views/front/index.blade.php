@extends("layouts.app-front")

@section("content")

<script>
const observer = new IntersectionObserver((entries) => {
    entries.forEach((entry) => {
        if (entry.isIntersecting) {
            entry.target.classList.remove("scroll-hidden");
        }
    });
});

function openSection(slug) {
    document.querySelectorAll(`[role="service"]`).forEach(el => {
        el.classList.toggle("active", el.getAttribute("data-slug") === slug);
    });
    document.querySelectorAll(`[role="service-button"]`).forEach(el => {
        el.classList.toggle("active", el.getAttribute("data-slug") === slug);
    });

    jumpTo(`[role="service"][data-slug="${slug}"] #offer`);
}

function loadFrontServices() {
    ["podklady", "organista", "dj"].forEach(service_name => {
        const section = document.querySelector(`[role="service"][data-slug="${service_name}"]`);

        section.querySelector(".loader").classList.remove("hidden");
        fetchPublic(`/api/front/service/${service_name}`)
            .then(res => res.json())
            .then(({html}) => {
                section.innerHTML = html;
                reapplyPopper();

                // animate on scroll
                const hiddenElements = document.querySelectorAll(".scroll-hidden");
                hiddenElements.forEach((el) => observer.observe(el));

                // load song list for catalog
                if (["podklady", "dj"].includes(service_name)) {
                    getSongList(service_name);
                }
            });
    });
}
</script>

<section id="home">
    <div class="company-name flex right but-mobile-down center">
        <x-shipyard::app.logo />
        <div class="vertical-divider but-mobile-hidden"></div>
        <div>
            <h1>Wojciech <big class="accent primary">Przybyła</big></h1>
            <p>{{ setting("app_name") }}</p>
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
</section>

<div role="services">
    <p>Wybierz kategorię, aby dowiedzieć się więcej:</p>

    <div class="flex right center">
        @foreach ([
            ["Podkłady i nuty", "Nagrania i partytury", "podklady"],
            ["Organista", "Oprawa ślubów i mszy", "organista"],
            ["Imprezy i koncerty", "Muzyka na żywo", "dj"],
        ] as $i => [$label, $desc, $slug_part])
        <div class="section flex right but-mobile-down center middle interactive backdropped stagger"
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
    <x-shipyard::app.loader />
</div>

<div role="service" data-slug="organista" class="animatable stagger">
    <x-shipyard::app.loader />
</div>

<div role="service" data-slug="dj" class="animatable stagger">
    <x-shipyard::app.loader />
</div>

<section id="about">
    <div class="flex right center">
        <img class="photo rounded" src="{{ asset("assets/front/img/home_me.jpg") }}" alt="me!">
        <div class="flex down center">
            <h1>O mnie</h1>
            <ul>
                <li>Mam na imię Wojtek i muzyką profesjonalnie zajmuję się od <b>ponad {{ date("Y") - 2012 }} lat</b></li>
                <li>Ukończyłem <b>szkołę muzyczną</b> I stopnia na gitarze</li>
                <li>Gram na wielu instrumentach, w tym <b>klawiszowych, perkusyjnych oraz dętych</b></li>
                <li>Jestem stałym członkiem <b>2 zespołów muzycznych</b>:
                    <a href="https://www.facebook.com/profile.php?id=100060053047728">Dixie Kings</a>
                    oraz
                    <a href="https://www.facebook.com/orkiestrawihajster">Orkiestry Tanecznej Wihajster</a>
                </li>
            </ul>
        </div>
    </div>
    <iframe src="https://www.youtube.com/embed/WYTOqc6ADwA?si=sYZUcVrL0Znh7czc"
        title="YouTube video player"
        frameborder="0"
        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen
        width="500"
        style="width: 100%; max-width: 500px; aspect-ratio: 16/9;"
    ></iframe>
</section>

<x-front.song-list.popup />

@endsection

@section("appends")

<script>
loadFrontServices();
</script>

@endsection
