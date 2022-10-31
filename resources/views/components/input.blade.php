@props([
    'type', 'name', 'label',
    'autofocus' => false,
    'required' => false,
    "disabled" => false,
    "hint" => null,
    "value" => null
])

<div {{
    $attributes
        ->filter(fn($val, $key) => (!in_array($key, ["autofocus", "required", "placeholder"])))
        ->merge(["class" => "input-container"])
    }}>
    @if ($type == "TEXT")
        <textarea
            name="{{ $name }}"
            id="{{ $name }}"
            {{ $autofocus ? "autofocus" : "" }}
            {{ $required ? "required" : "" }}
            {{ $disabled ? "disabled" : "" }}
            {{ $attributes->filter(fn($val, $key) => (!in_array($key, ["autofocus", "required"]))) }}
            >{{ $value }}</textarea>
    @else
        <input
            type="{{ $type }}"
            name="{{ $name }}"
            id="{{ $name }}"
            {!! $value ? "value=\"$value\"" : "" !!}
            {{ $autofocus ? "autofocus" : "" }}
            {{ $required ? "required" : "" }}
            {{ $disabled ? "disabled" : "" }}
            {{ $attributes->filter(fn($val, $key) => (!in_array($key, ["autofocus", "required", "class"]))) }}
            />
    @endif
    <label for="{{ $name }}">{{ $label }}</label>
    @if ($hint)
        <div class="input-hint">
            <i class="fa-solid fa-circle-info"
                {{ Popper::pop(implode(" ", array_map(function($key, $val){
                    return "$key";
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
</div>
