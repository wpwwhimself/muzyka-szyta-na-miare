@extends('layouts.mail', [
    "title" => "Goniec przynosi wieści od Sprzątacza"
])

@section('content')
    <h2>Sprzątacz wykonał swoją robotę</h2>
    <p>
        Poniżej lista operacji wykonanych przez Sprzątacza ostatniej nocy:
    </p>
    <table>
        <thead>
            <tr>
                <th>ReQuest</th>
                <th>Operacja</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($summary as $i)
            <tr>
                <td>
                    <a href="{{ route($i["is_request"] ? "request" : "quest", ["id" => $i["re_quest"]->id]) }}">
                        {{ $i["re_quest"]->id }}
                        <br>
                        {{ $i["is_request"] ? $i["re_quest"]->title : $i["re_quest"]->song->title }}
                    </a>
                </td>
                <td>
                    {{ $i["operation"] }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
@endsection
