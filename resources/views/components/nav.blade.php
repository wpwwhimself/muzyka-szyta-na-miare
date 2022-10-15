<nav>
    <a href="#"><li>lorem</li></a>
    <a href="#"><li>ipsum</li></a>
    <a href="#"><li>dolor</li></a>
    @guest
        <a href="{{ route("login") }}"><li>Zaloguj się</li></a>
    @endguest
    @auth
        <a href="{{ route("dashboard") }}"><li>Moje zlecenia</li></a>
        <a href="{{ route("logout") }}"><li>Wyloguj się</li></a>
    @endauth
    <script>
        $('a[href="{{ URL::current() }}"]').addClass("active");
    </script>
</nav>
