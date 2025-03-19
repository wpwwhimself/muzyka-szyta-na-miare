@extends("layouts.app-front")
@section("subtitle", "Kompleksowe usługi muzyczne dopasowane do Ciebie")

@section("content")

<section id="links">
    <h1>Wybierz, czego potrzebujesz:</h1>
    <div class="grid-3">
        @foreach ([
            ["Podkłady i nuty", "Nagrania i partytury", "podklady"],
            ["Organista", "Oprawa ślubów i mszy", "organista"],
            ["Imprezy i koncerty", "Muzyka na żywo", "dj"],
        ] as [$label, $desc, $slug_part])

        <a href="{{ route("home-".$slug_part) }}"
            class="section-like flex-right keep-for-mobile center"
        >
            <img src="{{ asset("assets/divisions/$slug_part.svg") }}" alt="logo"
                class="icon white-on-black"
            >
            <div>
                <h2>{{ $label }}</h2>
                <p>{{ $desc }}</p>
            </div>
        </a>
        @endforeach
    </div>
</section>

@endsection
