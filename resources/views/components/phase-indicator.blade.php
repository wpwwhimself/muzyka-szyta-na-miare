<div class="quest-phase p-{{ $statusId }}" status="{{ $statusId }}">
    <div class="quest-phase-label">
        @if ($small)
            <p><i class="fa-solid {{ $statusSymbol($statusId) }}"></i> {{ $statusName($statusId) }}</p>
        @else
            <span>Faza:</span>
            <h3><i class="fa-solid {{ $statusSymbol($statusId) }}"></i> {{ $statusName($statusId) }}</h3>
        @endif
    </div>
    <div class="quest-phase-bars">
        @for ($i = 0; $i < 9; $i++)
        <div {{ ($i < $bars($statusId)) ? "class=highlighted" : "" }}></div>
        @endfor
    </div>
</div>
