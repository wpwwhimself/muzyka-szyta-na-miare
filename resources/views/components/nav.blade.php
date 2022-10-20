<nav>
    @guest
        <a href="{{ route("login") }}" class="auth-link"><li>Zaloguj się</li></a>
    @endguest
    @auth
        <a href="{{ route("dashboard") }}"><li>{{ Auth::id() == 1 ? "Dashboard" : "Moje zlecenia" }}</li></a>
        <a href="{{ route("quests") }}"><li>Zlecenia</li></a>
        @if (Auth::id() == 1)
            <a href="{{ route("clients") }}"><li>Klienci</li></a>
            <a href="{{ route("ads") }}"><li>Reklama</li></a>
            <a href="{{ route("messages") }}"><li>Komunikacja</li></a>
        @endif
        <a href="{{ route("logout") }}" class="auth-link"><li>Wyloguj się</li></a>
    @endauth
    <script>
        $('a[href="{{ URL::current() }}"]').addClass("active");
    </script>
</nav>
