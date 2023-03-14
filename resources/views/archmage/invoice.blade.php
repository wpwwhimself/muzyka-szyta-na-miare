@extends('layouts.app', compact("title"))

@section('content')

<x-invoice :invoice="$invoice" />

<form action="{{ route('invoice-visibility') }}" method="post" class="flex-right hide-for-print">
    @csrf
    <input type="hidden" name="id" value="{{ $invoice->id }}" />
    <input type="hidden" name="visible" value="{{ intval(!$invoice->visible) }}" />
    <x-button action="submit"
        icon="{{ $invoice->visible ? 'eye-slash' : 'eye' }}"
        label="{{ $invoice->visible ? 'Ukryj' : 'Pokaż' }}"
        />

    <x-button action="{{ route('quest', ['id' => $invoice->quest_id]) }}"
        icon="angles-left" label="Wróć do zlecenia"
        />

    <x-button action="#/" id="print_invoice"
        icon="download" label="Drukuj"
        />
    <script>
    $(document).ready(function(){
        $("#print_invoice").click(function(){
            $("header, footer>div>p").addClass("hide-for-print");
            window.print();
        });
    });
    </script>
</form>

@endsection
