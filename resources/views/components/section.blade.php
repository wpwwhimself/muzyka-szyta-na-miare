@props([
    "title" => null,
    "icon" => null,
    "scissors" => false,
])

<section {{ $attributes->class(["bordered", "padded", "rounded", "sc-line" => $scissors]) }}>
    @if ($scissors)
    <x-sc-scissors />
    @endif

    @if ($title)
    <div class="section-header flex right middle spread">
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

    <div role="contents">
        @isset($slot)
        {{ $slot }}
        @endisset
    </div>
</section>
