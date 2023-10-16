<div id="quest-history">
    @forelse ($history as $item)
    <div class="history-position p-{{ $item->new_status_id }} {{ $item->changed_by == 1 ? "by-me" : "by-client" }}">
        <span>
            <span class="client-name ghost">{{ $clientName($item->changed_by) }}</span>
            <br>
            {!! $statusSymbol($item->new_status_id) !!} {{ $statusName($item->new_status_id) }}
            @if ($item->mail_sent >= 1)
                @for ($i = 0; $i < $item->mail_sent; $i++)
                <i class="fa-solid fa-envelope-circle-check" @popper(Mail wysłany)></i>
                @endfor
            @elseif ($item->mail_sent === 0)
            <i class="fa-solid fa-comment" @popper(Wiadomość wysłana pozamailowo)></i>
            @endif
            <ul class="qh-values ghost">
            @foreach (json_decode($item->values) ?? [] as $key => $val)
                <li>{{ $key }}: {{ $val }}</li>
            @endforeach
            </ul>
            <div class="qh-comment ghost">
                @php
                    $output = $item->comment;
                    if($item->new_status_id == 32) $output = as_pln($output);
                    if($item->changed_by != 1) $output = _ct_($output);
                    if(!$output) $output = "";
                @endphp
                {!! Illuminate\Mail\Markdown::parse($output) !!}
            </div>
        </span>
        <span>{!! str_replace(" ", "<br>", $item->date) !!}</span>
    </div>
    @empty
    <p class="grayed-out">historia tego zlecenia jest pusta</p>
    @endforelse
</div>
