<div class="extendo-block section-like" data-ebid="{{ $key }}">
    @unless($extended === "perma")
    <div class="header flex-right">
        <div class="titles flex-right">
            <h2><i class="fas fa-{{ $headerIcon }}"></i></h2>
            <h2>{{ $title }}</h2>
            <span class="ghost">{{ $subtitle }}</span>
        </div>
        <div class="right-side flex-right">
            <i class="fas fa-angles-down clickable" data-ebf="open"></i>
            @if ($warning) <i class="fas fa-triangle-exclamation fa-fade warning"></i> @endif
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
