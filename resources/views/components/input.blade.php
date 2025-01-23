@props([
    'type', 'name', 'label',
    'autofocus' => false,
    'required' => false,
    "disabled" => false,
    "hint" => null,
    "value" => null,
    "small" => false,
    "links" => false,
])

<div {{
    $attributes
        ->filter(fn($val, $key) => (!in_array($key, ["autofocus", "required", "placeholder", "small"])))
        ->merge([
            "class" => ($small) ? "input-container input-small" : "input-container",
            "for" => $name
        ])
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
            {{ $attributes->filter(fn($val, $key) => (!in_array($key, ["autofocus", "required", "class"]))) }}
            onfocus="highlightInput(this)" onblur="clearHighlightInput(this)"
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
            onfocus="highlightInput(this)" onblur="clearHighlightInput(this)"
            />
    @endif

    @if($type != "hidden")
    <label for="{{ $name }}">{{ $label }}</label>
    @endif

    @if (($type == "url" || $links) && $value)
    <div class="flex-right center">
        @foreach (explode(",", $value) ?? [] as $link)
        <x-a :href="$link" target="_blank" />
        @endforeach
    </div>
    @endif
</div>
