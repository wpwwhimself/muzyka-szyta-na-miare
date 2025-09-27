@props([
    "title" => null,
    "icon" => null,
    "scissors" => false,
])

<section {{ $attributes->class(["bordered", "padded", "rounded", "container", "sc-line" => $scissors]) }}>
    @if ($scissors)
    <x-sc-scissors />
    @endif

    @if ($title)
    <div class="section-header flex right spread">
        <h1>
            @if ($icon)
            <x-shipyard.app.icon :name="$icon" />
            @endif
            {{ $title }}
        </h1>

        @isset($buttons)
        <div class="flex right middle">
            {{ $buttons }}
        </div>
        @endisset
    </div>
    @endif

    @isset($slot)
    {{ $slot }}
    @endisset
</section>
