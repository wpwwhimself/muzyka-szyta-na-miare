@extends("layouts.app")
@section("title", "Napisz maila")

@section("content")

<x-shipyard.app.form :action="route('client-mail-send')" method="POST">
    <div class="grid but-mobile-down" style="--col-count: 2;">
        <x-shipyard.app.section title="Adresat" :icon="model_icon('user-notes')">
            <x-shipyard.ui.input type="select"
                name="clients[]"
                label="Klient"
                :icon="model_icon('user-notes')"
                :select-data="['options' => $clients]"
                :value="$client_id"
                multiple
            />
        </x-shipyard.app.section>

        <x-shipyard.app.section title="Treść" icon="pencil">
            <x-shipyard.ui.input type="text" name="subject" icon="text-short" label="Temat" />
            <x-shipyard.ui.input type="TEXT" name="content" icon="text" label="Wiadomość" />
        </x-shipyard.app.section>
    </div>

    <x-slot:actions>
        <x-shipyard.ui.button action="submit" label="Wyślij" icon="send" class="danger" />
    </x-slot:actions>
</x-shipyard.app.form>

@endsection
