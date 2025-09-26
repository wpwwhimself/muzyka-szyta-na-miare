@extends("layouts.shipyard.base")

@section("prepends")
<link rel="stylesheet" href="{{ asset("css/front.css") }}">
@endsection

@section("body")

<x-shipyard.app.big.header>
    <x-slot:top>
        <x-shipyard.app.logo />
        <x-shipyard.app.page-title>
            <x-slot:title>@yield("title", "Strona główna")</x-slot:title>
        </x-shipyard.app.page-title>
    </x-slot:top>

    <x-slot:bottom>
        <x-shipyard.auth.user-badge />
    </x-slot:bottom>
</x-shipyard.app.big.header>

<div id="background-division">
    @foreach (["podklady", "organista", "dj", "msznm"] as $name)
    <img
        src="{{ asset("assets/divisions/$name.svg") }}"
        alt="division logo"
        class="white-on-black"
    >
    @endforeach
</div>

<div id="middle-wrapper">
    @hasSection("sidebar")
    <aside>
        @yield("sidebar")
    </aside>
    @endif

    @hasSection("content")
    <main>
        @yield("content")
    </main>
    @endif
</div>

<x-shipyard.app.big.footer>
    <x-slot:top>
        <x-contact-info />
    </x-slot:top>

    <x-slot:bottom>
        @unless (setting("app_adaptive_dark_mode"))
        <x-shipyard.ui.button
            icon="theme-light-dark"
            pop="Tryb ciemny"
            action="none"
            onclick="toggleTheme()"
            class="tertiary"
        />
        @endunless

        <x-shipyard.app.app-badge />
    </x-slot:bottom>
</x-shipyard.app.big.footer>

@endsection()
