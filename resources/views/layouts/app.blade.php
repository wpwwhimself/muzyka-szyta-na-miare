@extends("layouts.shipyard.admin")

@section("prepends")
<script>
const USER_ID = {{ Auth::id() ?? 'null' }};
const IS_VETERAN = {{ is_archmage() ? 0 : intval(Auth::user()?->is_veteran ?? "") }};
</script>
@endsection

@section("appends")
<div id="background-division">
    @for ($i = 0; $i < 2; $i++)
    <img
        src="{{ asset("assets/divisions/"
            .($i == 0 ? (Str::between(Request::root(), "://", ".".env("APP_DOMAIN")) ?? "msznm") : "msznm")
            .".svg") }}"
        alt="division logo"
        class="white-on-black"
    >
    @endfor
</div>
@endsection

@hasSection("sidebar")
@yield("sidebar")
@endif

@hasSection("content")
@yield("content")
@endif
