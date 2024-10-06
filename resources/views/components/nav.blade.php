<nav>
    @if (Str::contains(Route::currentRouteName(), "home"))
        @foreach ([
            ["link" => "#offer", "label" => "Oferta"],
            ["link" => "#recomms", "label" => "Opinie"],
            ["link" => "#showcases", "label" => "Jak to brzmi"],
            ["link" => "#prices", "label" => "Cennik"],
            ["link" => "#about", "label" => "O mnie"],
            ["link" => "#contact", "label" => "Złóż zapytanie"],
        ] as ["link" => $link, "label" => $label])
            <a href="{{ $link }}"><li>{{ $label }}</li></a>
        @endforeach
        @auth
        <a href="{{ route("dashboard") }}" class="auth-link"><li><i class="fa-solid fa-user"></i> Moje konto</li></a>
        @endauth
    @else
        @auth
            @foreach ($logged as $item)
            <a href="{{ $item['link'] }}">
                <li {{ Popper::pop($item['label']) }}>
                    <i class="{{ $item['icon'] }}"></i>
                </li>
            </a>
            @endforeach
            @if (is_archmage())
                @foreach ($archmage as $item)
                <a href="{{ $item['link'] }}">
                    <li {{ Popper::pop($item['label']) }}>
                        <i class="{{ $item['icon'] }}"></i>
                    </li>
                </a>
                @endforeach
            @endif
            <a href="{{ route("logout") }}" class="auth-link"><li><i class="fa-solid fa-power-off"></i> Wyloguj się</li></a>
        @endauth
    @endif

    @guest
    <a href="{{ route("login") }}" class="auth-link"><li><i class="fa-solid fa-circle-user"></i> Zaloguj się</li></a>
    @endguest

    <script>
    $('a[href="{{ URL::current() }}"]').addClass("active");
    </script>
</nav>
