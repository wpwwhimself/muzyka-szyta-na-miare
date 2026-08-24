@props([
    "showcases",
])

<div id="showcase-fbs" class="flex right center">
    @php $player_dims = [300, 575]; @endphp
    @forelse ($showcases as $showcase)
        @php
        $platforms_available = collect($showcase->links?->keys());
        $platform = $platforms_available->random();
        $link = $showcase->links?->get($platform);
        @endphp
        @switch($platform)
            @case("yt")
                <iframe width="{{ $player_dims[0] }}" height="{{ $player_dims[1] }}" src="https://www.youtube.com/embed/{{ Str::contains($link, "shorts") ? Str::between($link, "shorts/", "?") : Str::after($link, "watch?v=") }}" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
                @break
            @case("tt")
                <iframe src="https://www.tiktok.com/player/v1/{{ Str::between($link, "video/", "?") }}" width="{{ $player_dims[0] }}" height="{{ $player_dims[1] }}" allow="autoplay; clipboard-write; encrypted-media; picture-in-picture; web-share" allowfullscreen></iframe>
                @break
            @case("ig")
                <iframe src="{{ Str::before($link, "?") }}embed" width="{{ $player_dims[0] }}" height="{{ $player_dims[1] }}" allow="autoplay; clipboard-write; encrypted-media; picture-in-picture; web-share" allowfullscreen></iframe>
                @break
            @case("fb")
        @endswitch
    @empty
    <span>🚧 Na razie nic tu nie ma...</span>
    @endforelse
</div>
<p class="ghost">Po więcej rolek zajrzyj na moje social media</p>
