@extends("layouts.app-front")
@section("title", setting("app_name"))

@section("content")

<script>
function openSection(slug) {
    document.querySelectorAll(`[role="service"]`).forEach(el => {
        el.classList.toggle("hidden", el.getAttribute("data-slug") !== slug);
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
        ] as [$label, $desc, $slug_part])
        <div class="section-like flex right keep-for-mobile center middle interactive"
            onclick="openSection('{{ $slug_part }}')"
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

<div role="service" data-slug="podklady" class="hidden">
    <x-front.podklady />
</div>

<div role="service" data-slug="organista" class="hidden">
    <x-front.organista />
</div>

<div role="service" data-slug="dj" class="hidden">
    <x-front.dj />
</div>

@endsection
