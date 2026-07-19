@extends("shipyard::layouts.admin")

@section("sidebar")

<div class="card">
    <x-shipyard::ui.button
        icon="account"
        action="/"
        pop="hello"
    />
</div>

@endsection

@section("content")

<div @class(["card", "stagger" => setting("animations_mode") >= 1, "stagger-contents" => setting("animations_mode") >= 2])>
    <h2>Your Shipyard starter kit is ready to go!</h2>

    <p>You can now start doing things:</p>
    <ul>
        <li>a thing,</li>
        <li>a thing,</li>
        <li>a thing,</li>
        <li>a thing,</li>
        <li>...</li>
    </ul>
</div>

@endsection
