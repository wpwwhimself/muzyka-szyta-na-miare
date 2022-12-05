<div id="quest-history">
    @forelse ($history as $item)
    <div class="history-position p-{{ $item->new_status_id }} {{ $item->changed_by == 1 ? "by-me" : "by-client" }}">
        <span>
            <span class="client-name ghost">{{ $clientName($item->changed_by) }}</span>
            <br>
            {!! $statusSymbol($item->new_status_id) !!} {{ $statusName($item->new_status_id) }}
            @if ($item->mail_sent === true)
            <i class="fa-solid fa-envelope-circle-check" @popper(Mail wysłany)></i>
            @elseif ($item->mail_sent === false)
            <i class="fa-solid fa-comment" @popper(Wiadomość wysłana pozamailowo)></i>
            @endif
            <ul>
            @if (substr($item->comment, 0, 1) != "{")
            {{ $item->comment }}{{ $item->new_status_id != 32 ? "" : " zł" }}
            @else
            @foreach (json_decode($item->comment) ?? [] as $key=>$val)
            <li>{{ $key }}: {{ $val }}</li>
            @endforeach
            @endif
            </ul>
        </span>
        <span>{!! str_replace(" ", "<br>", $item->date) !!}</span>
    </div>
    @empty
    <p class="grayed-out">historia tego zlecenia jest pusta</p>
    @endforelse
</div>
