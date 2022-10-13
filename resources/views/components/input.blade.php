<div {{ $attributes->filter(fn($val, $key) => ($key != "autofocus" && $key != "required")) }}>
    <input
        type="{{ $type }}"
        name="{{ $name }}"
        id="{{ $name }}"
        {{ $autofocus ? "autofocus" : "" }}
        {{ $required ? "required" : "" }}
        {{ $selected ? "checked" : "" }}
        />
    <label for="{{ $name }}">{{ $label }}</label>
</div>
