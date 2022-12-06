@props([
    'type', 'name', 'label',
    'autofocus' => false,
    'required' => false,
    "disabled" => false,
    "hint" => null,
    "value" => null,
    "small" => false
])

<div {{
    $attributes
        ->filter(fn($val, $key) => (!in_array($key, ["autofocus", "required", "placeholder", "small"])))
        ->merge(["class" => ($small) ? "input-container input-small" : "input-container"])
    }}>

    @if ($hint && $type != "hidden")
    <div class="input-hint">
        <i class="fa-solid fa-circle-info"
            {{ Popper::size('small')->pop(implode("<br>", array_map(function($key, $val){
                return "<b>$key</b>: $val";
            }, array_keys($hint), array_values($hint))))
            }}
            ></i>
        {{-- <div class="input-hint-content">
        @foreach ($hint as $key => $val)
            <span>{{ $key }}</span>
            <span>{{ $val }}</span>
        @endforeach
        </div> --}}
    </div>
    @endif

    @if ($type == "TEXT")
        <textarea
            name="{{ $name }}"
            id="{{ $name }}"
            {{ $autofocus ? "autofocus" : "" }}
            {{ $required ? "required" : "" }}
            {{ $disabled ? "disabled" : "" }}
            {{ $attributes->filter(fn($val, $key) => (!in_array($key, ["autofocus", "required"]))) }}
            >{{ html_entity_decode($value) }}</textarea>
    @else
        <input
            type="{{ $type }}"
            name="{{ $name }}"
            id="{{ $name }}"
            @if ($type == "checkbox" && $value)
            checked
            @else
            {{ $attributes->merge(["value" => html_entity_decode($value)]) }}
            @endif
            {{ $autofocus ? "autofocus" : "" }}
            {{ $required ? "required" : "" }}
            {{ $disabled ? "disabled" : "" }}
            {{ $attributes->filter(fn($val, $key) => (!in_array($key, ["autofocus", "required", "class"]))) }}
            />
    @endif

    @if($type != "hidden")
    <label for="{{ $name }}">{{ $label }}</label>
    @endif
</div>
