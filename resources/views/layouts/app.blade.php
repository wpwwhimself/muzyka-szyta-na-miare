@extends("layouts.shipyard.base")

@section("body")

<script>
// #region redirect from subdomains
if (window.location.hostname != "{{ env("APP_DOMAIN") }}") {
    window.location.href = `{{ env("APP_URL") }}${window.location.pathname}`
}
// #endregion
</script>

<x-shipyard.app.big.header>
    <x-slot:top>
        <x-shipyard.app.logo />
        <x-shipyard.app.page-title>
            <x-slot:title>@yield("title", "Strona główna")</x-slot:title>
            <x-slot:subtitle>@yield("subtitle", setting("app_name"))</x-slot:subtitle>
        </x-shipyard.app.page-title>
    </x-slot:top>

    <x-slot:bottom>
        @env("local") <span @popper(Środowisko lokalne) class="accent danger"><x-shipyard.app.icon name="shovel" /></span> @endenv
        @env("stage") <span @popper(Środowisko testowe (stage)) class="accent success"><x-shipyard.app.icon name="test-tube" /></span> @endenv
        <x-shipyard.app.big.nav />
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

    <x-slot:middle>
        <x-shipyard.auth.user-badge />
    </x-slot:middle>

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
