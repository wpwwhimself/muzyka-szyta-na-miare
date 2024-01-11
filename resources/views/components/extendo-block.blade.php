<div @class([
    "extendo-block",
    "section-like",
    "warning-like" => $warning,
    "sc-line" => $scissors,
]) data-ebid="{{ $key }}">
    @if($scissors) <x-sc-scissors /> @endif

    <div class="header flex-right keep-for-mobile no-wrap">
        <div class="titles flex-right keep-for-mobile">
            <h2><i class="fas fa-{{ $headerIcon }}"></i></h2>
            <h2>{{ $title }}</h2>
            <span class="ghost">{{ $subtitle }}</span>
        </div>

        @if($buttons)
        <div class="right-side flex-right">{{ $buttons }}</div>
        @endif

        @unless($extended === "perma")
        <div class="right-side flex-right keep-for-mobile">
            <i class="fas fa-angles-{{ $extended ? "up" : "down" }} clickable" data-ebf="open"></i>
            @if ($warning)
            @php
                $warning_content = [];
                foreach ($warning as $message => $test) {
                    $warning_content[] = $message;
                }
            @endphp
            <i class="fas fa-triangle-exclamation fa-fade warning"
                {{ Popper::arrow()->pop(Illuminate\Mail\Markdown::parse(implode("<br>", $warning_content))) }}
            ></i>
            @endif
        </div>
        @endunless
    </div>

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
