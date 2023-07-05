<nav>
@if (Route::currentRouteName() == "home")
    @foreach ($home as $item)
    <a href="{{ $item['link'] }}">
        <li>
            {{ $item['label'] }}
        </li>
    </a>
    @endforeach
    @guest
    <a href="{{ route("login") }}" class="auth-link"><li><i class="fa-solid fa-circle-user"></i> Zaloguj się</li></a>
    @endguest
    @auth
    <a href="{{ route("dashboard") }}" class="auth-link"><li><i class="fa-solid fa-user"></i> Moje konto</li></a>
    @endauth
@else
    @auth
        @foreach ($logged as $item)
        <a href="{{ $item['link'] }}">
            <li>
                <i class="{{ $item['icon'] }}"></i>
                {{ $item['label'] }}
            </li>
        </a>
        @endforeach
        @if (in_array(Auth::id(), [0, 1], true))
            @foreach ($archmage as $item)
            <a href="{{ $item['link'] }}">
                <li>
                    <i class="{{ $item['icon'] }}"></i>
                    {{ $item['label'] }}
                </li>
            </a>
            @endforeach
        @endif
        <a href="{{ route("logout") }}" class="auth-link"><li><i class="fa-solid fa-power-off"></i> Wyloguj się</li></a>
    @endauth
    @guest
        <a href="{{ route("login") }}" class="auth-link"><li><i class="fa-solid fa-circle-user"></i> Zaloguj się</li></a>
    @endguest
@endif
    <script>
    $('a[href="{{ URL::current() }}"]').addClass("active");
    </script>
</nav>
