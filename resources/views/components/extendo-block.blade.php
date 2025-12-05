<x-shipyard.app.section
    :title="$title"
    :subtitle="$subtitle"
    :icon="$headerIcon"
    :extended="$extended"
    @class([
        "warning-like" => $warning,
        "sc-line" => $scissors,
    ])
    {{ $attributes }}
>
    <x-slot:actions>
        @if ($warning)
        @php
            $warning_content = [];
            foreach ($warning as $message => $test) {
                $warning_content[] = $message;
            }
        @endphp
        <span class="accent danger"
            {{ Popper::arrow()->pop(Illuminate\Mail\Markdown::parse(implode("<br>", $warning_content))) }}
        >
            <x-shipyard.app.icon name="alert" />
        </span>
        @endif

        @isset ($buttons)
        {{ $buttons }}
        @endisset
    </x-slot:actions>

    @isset ($slot)
    {{ $slot }}
    @endisset

    @if($scissors) <x-sc-scissors /> @endif
</x-shipyard.app.section>
