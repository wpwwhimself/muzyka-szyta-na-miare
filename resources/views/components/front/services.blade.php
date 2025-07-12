@props([
    "except" => null,
])

<div class="flex-right center">
    @foreach ([
        ["Podkłady i nuty", "Nagrania i partytury", "podklady"],
        ["Organista", "Oprawa ślubów i mszy", "organista"],
        ["Imprezy i koncerty", "Muzyka na żywo", "dj"],
    ] as [$label, $desc, $slug_part])

    @if ($except == $slug_part) @continue @endif

    <a href="{{ route("home-".$slug_part) }}"
        class="section-like flex-right keep-for-mobile center hidden"
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
