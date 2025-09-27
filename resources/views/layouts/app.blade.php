@extends("layouts.shipyard.admin")

@section("appends")
<div id="background-division">
    @foreach (["podklady", "organista", "dj", "msznm"] as $name)
    <img
        src="{{ asset("assets/divisions/$name.svg") }}"
        alt="division logo"
        class="white-on-black"
    >
    @endforeach
</div>
@endsection

@hasSection("sidebar")
@yield("sidebar")
@endif

@hasSection("content")
@yield("content")
@endif
