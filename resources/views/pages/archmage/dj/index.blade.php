@extends("layouts.shipyard.admin")
@section("title", "Koncert")

@section("content")

<div class="grid but-mobile-down" style="--col-count: 2;">
    <x-shipyard.app.section
        icon="spotlight"
        title="Koncert"
        subtitle="Występ pianistyczny"
    >
        <x-slot:actions>
            <x-shipyard.ui.button
                :action="route('admin.model.list', ['model' => 'compositions', 'fltr[djready]' => 1])"
                label="Kompozycje gotowe na koncert"
                :icon="model_icon('compositions')"
            />
        </x-slot:actions>

        <p>
            Tryb koncertowy wyświetli do wyboru wszystkie kompozycje gotowe na koncert, tj. posiadające co najmniej mapę utworu.
        </p>

        <ul>
            <li>Gotowych utworów: {{ $djReadyCount }}</li>
        </ul>

        <div class="flex down spread">
            <x-shipyard.ui.button :action="route('dj-lottery-mode')"
                label="Loteria koncertowa"
                icon="slot-machine"
                class="primary"
            />
        </div>
    </x-shipyard.app.section>

    <x-shipyard.app.section
        :icon="model_icon('dj-sets')"
        title="Impreza"
        subtitle="Granie z brassami"
    >
        <x-slot:actions>
            <x-shipyard.ui.button
                :action="route('admin.model.list', ['model' => 'dj-sets'])"
                label="Zestawy"
                :icon="model_icon('dj-sets')"
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

        <div class="flex down spread">
            <x-shipyard.ui.button :action="route('dj-gig-mode')"
                label="Panel DJa"
                icon="headphones"
                class="primary"
            />
        </div>
    </x-shipyard.app.section>
</div>

@endsection
