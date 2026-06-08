@extends("layouts.app")
@section("title", "Koncert")

@section("content")

<x-shipyard.app.section
    icon="spotlight"
    title="Koncert"
    subtitle="Występ pianistyczny"
>
    <x-slot:actions>
        <x-shipyard.ui.button :action="route('dj-lottery-mode')"
            label="Loteria koncertowa"
            icon="slot-machine"
            class="primary"
        />
    </x-slot:actions>

    <p>
        Tryb koncertowy wyświetli do wyboru wszystkie kompozycje gotowe na koncert, tj. posiadające co najmniej mapę utworu.
    </p>

    <ul>
        <li>Gotowych utworów: {{ $djReadyCount }}</li>
    </ul>

    <x-shipyard.app.card
        title="Zarządzanie"
        :icon="model_icon('compositions')"
    >
        <x-shipyard.ui.button
            :action="route('admin.model.list', ['model' => 'compositions', 'fltr[djready]' => 1])"
            label="Kompozycje gotowe na koncert"
            :icon="model_icon('compositions')"
        />
    </x-shipyard.app.card>
</x-shipyard.app.section>

<x-shipyard.app.section
    :icon="model_icon('dj-sets')"
    title="Impreza"
    subtitle="Granie z brassami"
>
    <x-slot:actions>
        <x-shipyard.ui.button :action="route('dj-gig-mode')"
            label="Panel DJa"
            icon="headphones"
            class="primary"
        />
    </x-slot:actions>

    <p>
        Tryb imprezowy korzysta z zestawów piosenek.
        Każdy docelowo składa się z kilku podobnych stylistycznie utworów, co pozwala na ok. 10-15 minut grania.
        Na cały set muzyczny składało by się 2-3 takich zestawów.
    </p>

    <ul>
        <li>Gotowych zestawów: {{ $djSetsCount }}</li>
    </ul>

    <x-shipyard.app.card
        title="Zarządzanie"
        :icon="model_icon('dj-sets')"
    >
        <x-shipyard.ui.button
            :action="route('admin.model.list', ['model' => 'dj-sets'])"
            label="Zestawy"
            :icon="model_icon('dj-sets')"
        />
    </x-shipyard.app.card>
</x-shipyard.app.section>

@endsection
