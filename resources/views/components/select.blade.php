@props([
    'type', 'name', 'label',
    'autofocus' => false,
    'required' => false,
    'options',
    'emptyOption' => false,
    'value' => null
])

<div {{
    $attributes
        ->filter(fn($val, $key) => (!in_array($key, ["autofocus", "required", "placeholder"])))
        ->merge(["class" => "input-container"])
    }}>
    <select
        name="{{ $name }}"
        id="{{ $name }}"
        {{ $autofocus ? "autofocus" : "" }}
        {{ $required ? "required" : "" }}
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
