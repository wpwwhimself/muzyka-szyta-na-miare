<nav>
    @guest
        <a href="{{ route("login") }}" class="auth-link"><li><i class="fa-solid fa-circle-user"></i> Zaloguj się</li></a>
    @endguest
    @auth
        <a href="{{ route("dashboard") }}"><li><i class="fa-solid fa-house-chimney-user"></i> Pulpit</li></a>
        <a href="{{ route("quests") }}"><li><i class="fa-solid fa-boxes-stacked"></i> Zlecenia</li></a>
        <a href="{{ route("requests") }}"><li><i class="fa-solid fa-envelope-open-text"></i> Zapytania</li></a>
        @if (Auth::id() == 1)
            <a href="#"><li><i class="fa-solid fa-compact-disc"></i> Utwory</li></a>
            <a href="{{ route("clients") }}"><li><i class="fa-solid fa-users"></i> Klienci</li></a>
            <a href="{{ route("ads") }}"><li><i class="fa-solid fa-bullhorn"></i> Reklama</li></a>
            <a href="{{ route("messages") }}"><li><i class="fa-solid fa-comment"></i> Komunikacja</li></a>
        @endif
        <a href="{{ route("logout") }}" class="auth-link"><li><i class="fa-solid fa-power-off"></i> Wyloguj się</li></a>
    @endauth
    <script>
        $('a[href="{{ URL::current() }}"]').addClass("active");
    </script>
</nav>
