<table>
    <thead>
        <tr>
            <th>Dzień</th>
            <th>ReQuesty</th>
        </tr>
    </thead>
    <tbody>
    @foreach ($calendar as $date => $meta)
        <tr class="cal-row {{ $clickDays ? "clickable" : "" }} {{ $meta['suggest_date'] && $clickDays ? 'suggest' : '' }}" date="{{ $meta['date_val'] }}">
            <td class="{{ $meta['day_type'] }}">{{ $date }}</td>
            <td>
                @foreach ($meta["quests"] as $quest)
                <a class="quest" href="{{ route('quest', ['id' => $quest->id]) }}" target="_blank" >
                    <i class="fa-regular fa-square"></i>
                    {{ $quest->song->title ?? "bez tytułu" }}
                </a>
                @endforeach

                @foreach ($meta["quests_done"] as $quest)
                <a class="quest ghost" href="{{ route('quest', ['id' => $quest->id]) }}" target="_blank" >
                    <i class="fa-solid fa-square-check"></i>
                    {{ $quest->song->title ?? "bez tytułu" }}
                </a>
                @endforeach

                @foreach ($meta["requests"] as $request)
                <a class="request ghost" href="{{ route('request', ['id' => $request->id]) }}" target="_blank" >
                    <i class="fa-solid fa-envelope-open"></i>
                    {{ $request->title ?? "bez tytułu" }}
                </a>
                @endforeach
            </td>
        </tr>
    @endforeach
    </tbody>
</table>

@if ($clickDays)
<x-input type="checkbox" name="work_on_weekends" label="Licz weekendy" :small="true" value="{{ setting('work_on_weekends') }}" />
<script>
    $(document).ready(function(){
        $("tr[date]").click((el)=>{
            $("#deadline").val($(el.currentTarget).attr("date"));
        });

        $("#work_on_weekends").change(function(){
            $.ajax({
                url: "{{ url('settings_change') }}",
                type: "post",
                data: {
                    _token: "{{ csrf_token() }}",
                    setting_name: $(this).attr("name"),
                    value_str: +($(this).prop("checked")) //bool -> int
                },
                success: function(res){
                    window.location.reload();
                }
            });
        });
});
</script>
@endif
