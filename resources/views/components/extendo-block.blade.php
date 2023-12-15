<div @class([
    "extendo-block",
    "section-like",
    "warning-like" => $warning,
    "sc-line" => $scissors,
]) data-ebid="{{ $key }}">
    @if($scissors) <x-sc-scissors /> @endif

    @unless($extended === "perma")
    <div class="header flex-right keep-for-mobile">
        <div class="titles flex-right keep-for-mobile">
            <h2><i class="fas fa-{{ $headerIcon }}"></i></h2>
            <h2>{{ $title }}</h2>
            <span class="ghost">{{ $subtitle }}</span>
        </div>
        <div class="right-side flex-right keep-for-mobile">
            <i class="fas fa-angles-{{ $extended ? "up" : "down" }} clickable" data-ebf="open"></i>
            @if (array_filter($warning ?? [], fn($t) => $t))
            @php
                $warning_content = [];
                foreach ($warning as $message => $test) {
                    if($test) $warning_content[] = $message;
                }
            @endphp
            <i class="fas fa-triangle-exclamation fa-fade warning"
                {{ Popper::arrow()->pop(Illuminate\Mail\Markdown::parse(implode("\n", $warning_content))) }}
            ></i>
            @endif
        </div>
    </div>
    @endunless

    <div @class(['body', 'flex-right', 'hidden' => !$extended])>
        {{ $slot }}
    </div>

    <script>
    $(document).ready(() => {
        $("[data-ebid='{{ $key }}'] [data-ebf='open']").click(function(){
            $("[data-ebid='{{ $key }}'] .body").toggleClass("hidden")
            $(this).toggleClass("fa-angles-down").toggleClass("fa-angles-up")
        })
    })
    </script>
</div>
