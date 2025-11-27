<x-extendo-block key="history"
    :header-icon="model_icon('status_changes')"
    title="Historia"
    :extended="$extended"
    id="quest-history"
    {{ $attributes }}
>
    @php
    $lastComment = is_archmage()
        ? $history->whereNotIn("changed_by", [0, 1])->last() ?? $history->whereNull("changed_by")->last()
        : $history->whereIn("changed_by", [0, 1])->last();
    @endphp

    <x-extendo-section :title="is_archmage() ? 'Ostatni komentarz klienta' : 'Ostatni mój komentarz'">
        @if($lastComment?->comment)
        {!! $lastComment !!}
        @endif
    </x-extendo-section>

    <div role="circles">
        @forelse ($history as $item)
        <div @class([
            "history-position",
            "flex",
            "down",
            "middle",
            "no-gap",
        ])>
            <div @class([
                "circle",
                "flex",
                "down",
                "center",
                "p-".$item->status->id,
                "by-client" => !is_archmage($item->changed_by),
            ]) {{ Popper::interactive()->pop(preg_replace("/\"/", "&quot;", $item)) }}
                data-comment="{{ $item->comment }}"
            >
                <x-phase-indicator-mini :status="$item->status" :pop="false" />
            </div>
            <small class="notification-counter">
                @if ($item->mail_sent >= 1)
                    @for ($i = 0; $i < $item->mail_sent; $i++)
                    <x-shipyard.app.icon name="email-fast" />
                    @endfor
                @elseif ($item->mail_sent === 0)
                <x-shipyard.app.icon name="message-fast" />
                @endif
            </small>
            @if (is_archmage() && !is_archmage($item->changed_by))
            <a @popper(Przypięcie komentarza)
                href="{{ route('showcase-pin-comment', ['comment_id' => $item->id, 'client_id' => $item->changed_by]) }}"
                class="{{ $item->pinned ? 'accent primary' : 'ghost' }}"
            >
                <x-shipyard.app.icon name="pin" />
            </a>
            @endif
        </div>
        @empty
        <p class="grayed-out">historia tego zlecenia jest pusta</p>
        @endforelse
    </div>
</x-extendo-block>
