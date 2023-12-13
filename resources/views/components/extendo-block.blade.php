<div class="extendo-block section-like" data-ebid="{{ $key }}">
    @unless($extended === "perma")
    <div class="header flex-right">
        <h1>
            <i class="fas fa-{{ $headerIcon }}"></i>
            {{ $header }}
        </h1>
        <div class="right-side flex-right">
            <i class="fas fa-angles-down clickable" data-ebf="open"></i>
            @if ($warning) <i class="fas fa-triangle-exclamation fa-fade warning"></i> @endif
        </div>
    </div>
    @endunless

    <div @class(['body', 'flex-right', 'hidden' => !$extended])>
        <div class="flex-down center">
            <i class="fas fa-{{ $icon }} quest-type" {{ $iconLabel ? Popper::pop($iconLabel) : null }}></i>
            <span class="ghost">{{ $textBelowIcon }}</span>
        </div>

        <div class="flex-down">
            <h2>{{ $title }}</h2>
            <span>{{ $subtitle }}</span>
        </div>

        <ul class="list-data">
        @foreach ($listData as $label => $content)
            <li><span class="ghost">{{ $label }}</span>: {!! $content !!}</li>
        @endforeach
        </ul>

        @foreach ($sectionData as $label => $content)
        <div class="flex-down center">
            <span class="grayed-out">{{ $label }}</span>
            {{ Illuminate\Mail\Markdown::parse($content) }}
        </div>
        @endforeach
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
