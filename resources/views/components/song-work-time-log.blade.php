<x-extendo-block key="time-log"
    header-icon="snowplow"
    title="Log tworzenia"
    :subtitle="$quest->song->work_time"
    :extended="$extended"
    :warning="['Zegar tyka' => $workhistory->search(fn($entry) => $entry->now_working) !== false]"
>
    <table id="stats-log">
        <thead>
            <tr>
                <th>Etap</th>
                <th colspan="2">Czas</th>
            </tr>
        </thead>
        <tbody>
        @forelse ($workhistory as $entry)
            <tr @if($entry->now_working) class="active" @endif>
                <td>
                    {{ DB::table("statuses")->find($entry->status_id)->status_symbol }}
                    {{ DB::table("statuses")->find($entry->status_id)->status_name }}
                </td>
                <td>
                    @if ($entry->now_working)
                    <i class="fa-solid fa-gear fa-spin" @popper(zegar tyka)></i>
                    @else
                    <a class="log-delete" href="{{ route('work-clock-remove', ['status_id' => $entry->status_id, 'song_id' => $entry->song_id]) }}">
                        <i class="fa-solid fa-trash" @popper(usuń wpis)></i>
                    </a>
                    @endif
                </td>
                <td>
                    {{ $entry->time_spent->format("H:i:s") }}
                </td>
            </tr>
        @empty
        <tr>
            <td colspan=3 class="grayed-out">
                Prace jeszcze nie zaczęte
            </td>
        </tr>
        @endforelse
        </tbody>
        <tfoot>
            <tr>
                <th>Razem</th>
                <th colspan="2">
                {{ gmdate("H:i:s", DB::table("song_work_times")
                        ->where("song_id", $quest->song_id)
                        ->sum(DB::raw("TIME_TO_SEC(time_spent)"))) }}
                </th>
            </tr>
        </tfoot>
        </tbody>
    </table>

    @if ($quest->status_id == 12)
    <form method="POST" action="{{ route("work-clock") }}" class="flex-right">
        <div id="stats-buttons">
            @csrf
            <input type="hidden" name="song_id" value="{{ $quest->song_id }}" />
            @foreach ($stats_statuses as $option)
            <x-button
                label="{{-- $option->status_name --}}" icon="{{ $option->id }}"
                action="submit" value="{{ $option->id }}" name="status_id"
                :small="true" :pop="$option->status_name"
                />
            @endforeach
            <x-button
                label="stop" icon="circle-pause"
                action="submit" value="13" name="status_id"
                :small="true"
                />
        </div>
    </form>
    <x-a :href="route('work-clock-big', ['entity' => 'quest', 'id' => $quest->id])">Studio</x-a>
    @endif
</x-extendo-block>
