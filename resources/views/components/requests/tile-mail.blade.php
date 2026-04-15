@props([
    "request",
])

<table>
    <tbody>
        <tr>
            <td>{{ $request->full_title }}</td>
            <td>{{ $request->quest_type }}</td>
            <td><a href="{{ route('request', ['id' => $request->id]) }}" class="button">Przejdź do zapytania</a></td>
        </tr>
    </tbody>
</table>
