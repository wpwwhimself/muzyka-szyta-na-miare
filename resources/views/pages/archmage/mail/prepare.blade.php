@extends("layouts.app", ["title" => "Napisz maila"])

@section("content")

<form action="{{ route('client-mail-send') }}" method="POST">
    @csrf

    <div class="grid-2">
        <x-section title="Adresat" icon="user">
            <x-select name="clients[]" label="Klient" :options="$clients" />
            <script defer>
            $("[name='clients[]']").select2({
                placeholder: "Wiadomość dla wszystkich",
                multiple: true,
                allowClear: true,
            })
            </script>
        </x-section>

        <x-section title="Treść" icon="pencil">
            <x-input type="text" name="subject" label="Temat" />
            <x-input type="TEXT" name="content" label="Wiadomość" />
        </x-section>
    </div>

    <div>
        <x-button action="submit" label="Wyślij" icon="paper-plane" />
    </div>
</form>

@endsection
