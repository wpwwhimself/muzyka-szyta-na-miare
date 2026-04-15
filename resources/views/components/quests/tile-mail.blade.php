@props([
    "quest",
])

<table>
    <tbody>
        <tr>
            <td>{{ $quest->id }}</td>
            <td>{{ $quest->song }}</td>
            <td>{{ $quest->quest_type }}</td>
            <td><a href="{{ route('quest', ['id' => $quest->id]) }}" class="button">Przejdź do zlecenia</a></td>
        </tr>
    </tbody>
</table>
