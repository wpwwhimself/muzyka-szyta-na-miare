@props([
    'type', 'name', 'label',
    'autofocus' => false,
    'required' => false,
    "disabled" => false,
    'options',
    'emptyOption' => false,
    'value' => null,
    'small' => false
])

<div {{
    $attributes
        ->filter(fn($val, $key) => (!in_array($key, ["autofocus", "required", "placeholder", "small"])))
        ->merge(["class" => ($small) ? "input-container input-small" : "input-container"])
    }}>
    <select
        name="{{ $name }}"
        id="{{ $name }}"
        {{ $autofocus ? "autofocus" : "" }}
        {{ $disabled ? "disabled" : "" }}
        {{ $required ? "required" : "" }}
        onfocus="highlightInput(this)" onblur="clearHighlightInput(this)"
        >
        @if ($emptyOption)
            <option value="" {{ $value ? "" : "selected" }}></option>
        @endif
        @foreach ($options as $key => $val)
            <option value="{{ $key }}" {{  $value == $key ? "selected" : "" }}>{{ $val }}</option>
        @endforeach
    </select>
    <label for="{{ $name }}">{{ $label }}</label>
</div>
