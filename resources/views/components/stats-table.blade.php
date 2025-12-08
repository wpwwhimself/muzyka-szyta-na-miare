@props(["data", "title" => null, "footer" => false])

@if ($title)
<h2>{!! $title !!}</h2>
@endif
<table>
    <thead>
        <tr>
        @foreach ($data->rows[0] as $key => $val)
            <th>{{ $key }}</th>
        @endforeach
        </tr>
    </thead>
    <tbody>
    @foreach ($data->rows as $row)
        <tr>
        @foreach ($row as $key => $val)
            <td>{!! _ct_($val) !!}</td>
        @endforeach
        </tr>
    @endforeach
    </tbody>
    @if ($footer)
    <tfoot>
        <tr>
        @foreach ($data->footer as $key => $val)
            <th></th>
            <th colspan="{{ count((array)$data->rows[0]) - 2 }}">{{ $key }}</th>
            <th>{{ $val }}</th>
        @endforeach
        </tr>
    </tfoot>
    @endif
</table>
