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
    <iframe width="560" height="315" src="https://www.youtube.com/embed/WYTOqc6ADwA?si=sYZUcVrL0Znh7czc" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
</section>

<x-front.song-list.popup />

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
