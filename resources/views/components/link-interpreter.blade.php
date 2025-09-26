@props(['raw', 'editable' => null])

@unless(empty($raw))
<div class="quest-links center {{ $editable ? 'flex-down' : 'flex-right' }}">
    @foreach (explode(",", $raw) as $link)
    @php $link = Str::of($link)->trim() @endphp
    @if (filter_var($link, FILTER_VALIDATE_URL))
    <x-button action="{{ $link }}" target="_blank" icon="up-right-from-square" label="Link" :small="true" />
        @if ($link->match("/youtu\.?be/") && is_archmage())
        <x-button action="https://www.y2mate.gg/youtube/{{ preg_replace('/^.*(v=|be\/)([a-zA-Z0-9-_]{11}).*$/', '$2', $link) }}" target="_blank" icon="download" label="" :small="true" />
        @endif
    @else
    <span>{{ $link }}</span>
    @endif
    @endforeach
    @if($editable)
    <x-button action="#/" class="link-edit-trigger" icon="pencil" label="" :small="true" />
    <div class="link-edit-field hidden">
        <x-input type="text" name="link" label="Linki" :value="$link" :small="true" :data-editable="$editable" />
    </div>
    @endif
</div>
@endunless
