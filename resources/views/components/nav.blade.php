<nav>
    <a href="#"><li>lorem</li></a>
    <a href="#"><li>ipsum</li></a>
    <a href="#"><li>dolor</li></a>
    @guest
        <a href="/auth"><li>Zaloguj się</li></a>
    @endguest
    @auth
        <a href="/dashboard"><li>Moje zlecenia</li></a>
        <a href="/auth/logout"><li>Wyloguj się</li></a>
    @endauth
</nav>
