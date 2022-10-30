<div class="quest-phase p-{{ $statusId }}">
    <div class="quest-phase-label">
        <span>Faza:</span>
        <h3>{{ $statusName($statusId) }}</h3>
    </div>
    <div class="quest-phase-bars">
        @for ($i = 0; $i < 10; $i++)
        <div {{ ($i < $bars($statusId)) ? "class=highlighted" : "" }}></div>
        @endfor
    </div>
</div>
