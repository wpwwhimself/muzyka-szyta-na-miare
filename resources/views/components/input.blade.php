@props([
    'type', 'name', 'label',
    'autofocus' => false,
    'required' => false,
    "hint" => null
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
            {{ $attributes->filter(fn($val, $key) => (!in_array($key, ["autofocus", "required"]))) }}
            ></textarea>
    @else
        <input
            type="{{ $type }}"
            name="{{ $name }}"
            id="{{ $name }}"
            {{ $autofocus ? "autofocus" : "" }}
            {{ $required ? "required" : "" }}
            {{ $attributes->filter(fn($val, $key) => (!in_array($key, ["autofocus", "required"]))) }}
            />
    @endif
    <label for="{{ $name }}">{{ $label }}</label>
    @if ($hint)
        <div class="input-hint">
            <i class="fa-solid fa-circle-info"></i>
            <div class="input-hint-content">
            @foreach ($hint as $key => $val)
                <span>{{ $key }}</span>
                <span>{{ $val }}</span>
            @endforeach
            </div>
        </div>
    @endif
</div>
