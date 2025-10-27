@props([
    "data"
])

<div role="model-card">
    <div role="top-part">
        @isset($data["client_name"]) <h3>{{ $data["client_name"] }}</h3> @endisset
    </div>

    <div role="bottom-part">
        <div class="flex down no-gap">
            @isset($data["email"]) <x-shipyard.app.icon :name="model_field_icon('users', 'email')" /> <a href="mailto:{{ $data["email"] }}">{{ $data["email"] }}</a> @endisset
            @isset($data["phone"]) <x-shipyard.app.icon :name="model_field_icon('users', 'phone')" /> <a href="tel:{{ $data["phone"] }}">{{ $data["phone"] }}</a> @endisset
            @isset($data["other_medium"]) <x-shipyard.app.icon :name="model_field_icon('users', 'other_medium')" /> {{ $data["other-medium"] }} @endisset
        </div>
    </div>
</div>
