@props([
    "request",
])

<table>
    <thead>
        <tr>
            <th>Utwór</th>
            <th>Typ zlecenia</th>
            <th>Status</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>{{ $request->full_title }}</td>
            <td>{{ $request->quest_type }}</td>
            <td>{{ $request->status->status_name }}</td>
            <td><a href="{{ route('request', ['id' => $request->id]) }}" class="button">Przejdź do zapytania</a></td>
        </tr>
    </tbody>
</table>
