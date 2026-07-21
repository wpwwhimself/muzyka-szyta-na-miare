@props(['raw', 'editable' => null])

@unless(empty($raw))
<div class="quest-links center {{ $editable ? 'flex down' : 'flex right' }}">
    @foreach (explode(",", $raw) as $link)
        @php $link = Str::of($link)->trim() @endphp

        @if (filter_var($link, FILTER_VALIDATE_URL))
        <x-shipyard::ui.button
            :action="$link"
            label="Link"
            target="_blank"
            icon="open-in-new"
        />
            @if ($link->match("/youtu\.?be/") && is_archmage())
            <x-shipyard::ui.button
                action="none"
                onclick="
                    navigator.clipboard.writeText(`{{ $link }}`);
                    alert(`Skopiowano link. Przeklej go w pole, jakie za chwilę się pojawi.`);
                    window.open(`https://mp3now.com/en2`, `_blank`);
                "
                icon="download"
                pop="Pobieranie"
                class="tertiary"
            />
            @endif

        @endif
    @endforeach

    @if($editable)
    <x-button action="none" class="link-edit-trigger" icon="pencil" label="" :small="true" />
    <div class="link-edit-field hidden">
        <x-input type="text" name="link" label="Linki" :value="$link" :small="true" :data-editable="$editable" />
    </div>
    @endif
</div>
@endunless
