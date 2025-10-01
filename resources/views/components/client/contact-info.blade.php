@props([
    "data"
])

<div role="model-card">
    <div role="top-part">
        @isset($data["client_name"]) <h3>{{ $data["client_name"] }}</h3> @endisset
    </div>

    <div role="bottom-part">
        <div class="flex down no-gap">
            @isset($data["email"]) <x-shipyard.app.icon name="email" /> <a href="mailto:{{ $data["email"] }}">{{ $data["email"] }}</a> @endisset
            @isset($data["phone"]) <x-shipyard.app.icon name="phone" /> <a href="tel:{{ $data["phone"] }}">{{ $data["phone"] }}</a> @endisset
            @isset($data["other_medium"]) <x-shipyard.app.icon name="human-greeting-proximity" /> {{ $data["other-medium"] }} @endisset
        </div>
    </div>
</div>
