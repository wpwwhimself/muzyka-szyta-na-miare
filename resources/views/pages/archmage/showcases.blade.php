@extends("shipyard::layouts.admin", compact("title"))

@section('content')

<div class="grid" style="--col-count: 2;">

<x-shipyard::app.section title="Moje rolki" :icon="model_icon('showcases')">
    <table>
        <thead>
            <tr>
                <th>Typ</th>
                <th>Liczba</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @foreach ([
                ["Podkłady", "showcases"],
                ["Organy", "organ-showcases"],
                ["DJ", "dj-showcases"],
            ] as [$label, $scope])
            <tr>
                <td>{{ $label }}</td>
                <td>{{ model($scope)::count() }}</td>
                <td>
                    <x-shipyard::ui.button
                        icon="plus"
                        label="Dodaj"
                        class="small primary"
                        :action="route('admin.model.edit', ['model' => $scope])"
                    />
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</x-shipyard::app.section>

<x-section id="client-showcases-list" title="Reklamy klienta" icon="list">
    <form action="{{ route('add-client-showcase') }}" method="POST" class="flex right">
        @csrf
        <x-select name="song_id" label="Utwór" :options="$all_songs" :small="true" />
        <x-input type="text" name="embed" label="Embed" :small="true" />
        <x-shipyard::ui.button class="primary" action="submit" label="Dodaj" icon="plus" />
    </form>

    <table>
        <thead>
            <tr>
                <th>Tytuł</th>
                <th>Embed</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($client_showcases as $showcase)
            <tr>
                <td><a href="{{ route('songs', ['search' => $showcase->song_id]) }}">{{ $showcase->song->full_title }}</a></td>
                <td>{!! $showcase->embed ?? "<span></span>" !!}</td>
            </tr>
            @empty
            <tr>
                <td colspan="2" class="grayed-out">Nie ma żadnych reklam</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    {{ $client_showcases->links("shipyard::components.pagination.default") }}
</x-section>

</div>

@endsection
