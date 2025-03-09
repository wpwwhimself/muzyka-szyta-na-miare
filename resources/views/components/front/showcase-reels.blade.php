@props([
    "showcases",
])

<div id="showcase-fbs" class="flex-right center">
    @php $player_dims = [300, 575]; @endphp
    @foreach ($showcases as $showcase)
        @switch($showcase->platform)
            @case("yt")
                <iframe width="{{ $player_dims[0] }}" height="{{ $player_dims[1] }}" src="https://www.youtube.com/embed/{{ Str::between($showcase->link, "shorts/", "?") }}" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
                @break
            @case("tt")
                <iframe src="https://www.tiktok.com/player/v1/{{ Str::between($showcase->link, "video/", "?") }}" width="{{ $player_dims[0] }}" height="{{ $player_dims[1] }}" allow="autoplay; clipboard-write; encrypted-media; picture-in-picture; web-share" allowfullscreen></iframe>
                @break
            @case("ig")
                <iframe src="{{ Str::before($showcase->link, "?") }}embed" width="{{ $player_dims[0] }}" height="{{ $player_dims[1] }}" allow="autoplay; clipboard-write; encrypted-media; picture-in-picture; web-share" allowfullscreen></iframe>
                @break
            @case("fb")
        @endswitch
    @endforeach
</div>
<p class="ghost">Po więcej rolek zajrzyj na moje social media</p>
