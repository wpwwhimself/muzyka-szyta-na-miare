<x-extendo-block key="history"
    header-icon="timeline"
    title="Historia"
>
    <x-extendo-section title="Najnowszy komentarz">
        @if(is_archmage() && $history->whereNotIn("changed_by", [0, 1])->last()?->comment)
        {{ Illuminate\Mail\Markdown::parse($history->whereNotIn("changed_by", [0, 1])->last()->comment) }}
        @endif
    </x-extendo-section>

    <div id="quest-history">
        @forelse ($history as $item)
        <div @class([
            "history-position",
            "flex-down",
            "center",
        ])>
            <div @class([
                "circle",
                "flex-down",
                "center",
                "p-".$item->status->id,
                "by-client" => !is_archmage($item->changed_by),
            ]) {{ Popper::arrow()->interactive()->pop($entryLabel($item)) }}>
                <i class="fas {{ $item->status->status_symbol }} quest-status p-{{ $item->new_status_id }}"></i>
            </div>
            <small class="notification-counter">
                @if ($item->mail_sent >= 1)
                    @for ($i = 0; $i < $item->mail_sent; $i++)
                    <i class="fa-solid fa-envelope-circle-check" @popper(Mail wysłany)></i>
                    @endfor
                @elseif ($item->mail_sent === 0)
                <i class="fa-solid fa-comment" @popper(Wiadomość wysłana pozamailowo)></i>
                @endif
            </small>
        </div>
        @empty
        <p class="grayed-out">historia tego zlecenia jest pusta</p>
        @endforelse
    </div>
</x-extendo-block>
