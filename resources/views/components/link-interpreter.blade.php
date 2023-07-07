@props(['raw', 'editable' => null])

<div class="quest-links {{ $editable ? 'flex-down center' : '' }}">
    @foreach (explode(",", $raw) as $link)
    @php $link = Str::of($link)->trim() @endphp
    @if (filter_var($link, FILTER_VALIDATE_URL))
    <x-button action="{{ $link }}" target="_blank" icon="up-right-from-square" label="Link" :small="true" />
    @endif
    @endforeach
    @if($editable)
    <x-button action="#/" class="link-edit-trigger" icon="pencil" label="" :small="true" />
    <div class="link-edit-field gone">
        <x-input type="text" name="link" label="Linki" :value="$link" :small="true" :data-editable="$editable" />
    </div>
    @endif
</div>
