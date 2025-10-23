<x-extendo-block key="time-log"
    :header-icon="model_icon('song_work_times')"
    title="Log tworzenia"
    :subtitle="$quest->song->work_time_total"
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
                    @endif
                </td>
                <td>
                    {{ $entry->time_spent }}
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

    <x-slot:buttons>
        <x-shipyard.ui.button
            :icon="model_icon('song_work_times')"
            pop="Studio"
            :action="route('studio-view', ['quest_id' => $quest->id])"
        />
    </x-slot:buttons>
</x-extendo-block>
