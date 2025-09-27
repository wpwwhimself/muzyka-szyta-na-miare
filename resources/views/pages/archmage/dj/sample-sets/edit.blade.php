@extends("layouts.app", ["title" => implode(" | ", array_filter([$set?->name, "Edycja sampla"]))])

@section("content")

<section>
    <form action="{{ route('dj-process-sample-set') }}" method="POST">
        @csrf

        <div class="flex-right center">
            <x-input name="id" label="ID"
                type="text" :value="$set?->id"
            />
            <x-input name="name" label="Nazwa"
                type="text" :value="$set?->name"
            />
            <x-input name="description" label="Opis"
                type="TEXT" :value="$set?->description"
            />
        </div>

        <div>
            <x-button :action="route('dj-list-sample-sets')" label="Wróć" icon="angles-left" small />
            <x-button action="submit" name="action" value="save" icon="check" label="Zapisz" />
            @if ($set)
            <x-button action="submit" name="action" value="delete" label="Usuń" icon="trash" danger />
            @endif
        </div>
    </form>
</section>

@endsection
