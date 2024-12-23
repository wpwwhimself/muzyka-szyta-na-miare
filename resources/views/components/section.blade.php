@props([
    "title",
    "icon",
])

<section {{ $attributes }}>
    <div class="section-header">
        <h1><i class="fa-solid fa-{{ $icon }}"></i> {{ $title }}</h1>

        @isset($buttons)
        <div class="flex-right middle">
            {{ $buttons }}
        </div>
        @endisset
    </div>

    @isset($slot)
    {{ $slot }}
    @endisset
</section>
