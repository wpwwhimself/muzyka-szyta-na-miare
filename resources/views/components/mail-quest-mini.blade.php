@props(['quest'])

<table>
    <tr>
        <td class="framed-cell">
            <p>
            @if ($quest->song)
            {{ song_quest_type($quest->song->id)->type }}
            @else
            {{ DB::table("quest_types")->find($quest->quest_type_id)->type }}
            @endif
            </p>
            <h2>{{ $quest->song->title ?? $quest->title ?? "bez tytułu" }}</h2>
            <p>{{ $quest->song->artist ?? $quest->artist }}</p>
        </td>
    </tr>
</table>
