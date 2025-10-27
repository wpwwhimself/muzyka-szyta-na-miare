<div {{ $attributes->class([
    "extendo-block",
    "section",
    "warning-like" => $warning,
    "sc-line" => $scissors,
]) }} data-ebid="{{ $key }}">
    @if($scissors) <x-sc-scissors /> @endif

    <div class="header flex right keep-for-mobile nowrap">
        <div class="titles flex right keep-for-mobile">
            <h2><x-shipyard.app.icon :name="$headerIcon" /></h2>
            <h2>{{ $title }}</h2>
            <span class="ghost">{!! $subtitle !!}</span>
        </div>

        <div class="right-side flex right middle keep-for-mobile">
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

            @if($buttons)
            {{ $buttons }}
            @endif

            @unless($extended === "perma")
            <x-shipyard.ui.button
                icon="unfold-less-horizontal"
                pop="Zwiń"
                action="none"
                onclick="openExtendoBlock(this, '{{ $key }}')"
                class="toggles tertiary {{ $extended ? '' : 'hidden' }}"
            />
            <x-shipyard.ui.button
                icon="unfold-more-horizontal"
                pop="Rozwiń"
                action="none"
                onclick="openExtendoBlock(this, '{{ $key }}')"
                class="toggles tertiary {{ $extended ? 'hidden' : '' }}"
            />
            @endunless
        </div>
    </div>

    <div @class(['body', 'hidden' => !$extended])>
        {{ $slot }}
    </div>
</div>
