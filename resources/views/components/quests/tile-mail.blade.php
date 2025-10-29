@props([
    "quest",
])

<table>
    <thead>
        <tr>
            <th>ID zlecenia</th>
            <th>Utwór</th>
            <th>Typ zlecenia</th>
            <th>Status</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>{{ $quest->id }}</td>
            <td>{{ $quest->song }}</td>
            <td>{{ $quest->quest_type }}</td>
            <td>{{ $quest->status->status_name }}</td>
            <td><a href="{{ route('quest', ['id' => $quest->id]) }}" class="button">Przejdź do zlecenia</a></td>
        </tr>
    </tbody>
</table>
