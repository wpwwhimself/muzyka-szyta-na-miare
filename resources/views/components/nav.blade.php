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
            @foreach ([
                [route("dashboard"), "Pulpit", "house-chimney-user", true],
                [route("quests"), "Zlecenia", "boxes-stacked", true],
                [route("requests"), "Zapytania", "envelope-open-text", true],
                [route("prices"), "Cennik", "barcode", true],
                [route("songs"), "Utwory", "compact-disc", is_archmage()],
                [route("clients"), "Klienci", "users", is_archmage()],
                [route("finance"), "Finanse", "sack-dollar", is_archmage()],
                [route("showcases"), "Reklama", "bullhorn", is_archmage()],
                [route("dj"), "DJ", "!msznm-dj", is_archmage()],
                [route("stats"), "Statystyki", "chart-line", is_archmage()],
                [route("ppp"), "PPP", "circle-question", is_archmage()],
                [route("settings"), "Ustawienia", "cog", is_archmage()],
            ] as [$link, $label, $icon, $condition])
            @if ($condition)
            <a href="{{ $link }}">
                <li {{ Popper::pop($label) }}>
                    @if (Str::startsWith($icon, "!"))
                    @php $icon = substr($icon, 1); @endphp
                    <img class="icon small" src="{{ asset("assets/$icon.svg") }}" alt="{{ $icon }}">
                    @else
                    <i class="fas fa-{{ $icon }}"></i>
                    @endif
                </li>
            </a>
            @endif
            @endforeach
            <a href="{{ route("logout") }}" class="auth-link"><li><i class="fa-solid fa-power-off"></i> Wyloguj się</li></a>
        @endauth
    @endif

    @guest
    @if (Str::contains(request()->getHost(), env("PODKLADY_SUBDOMAIN").".".env("APP_DOMAIN")))
    <a href="{{ route("login") }}" class="auth-link"><li><i class="fa-solid fa-circle-user"></i> Zaloguj się</li></a>
    @endif
    @endguest

    <script>
    $('a[href="{{ URL::current() }}"]').addClass("active");
    </script>
</nav>
