@extends('layouts.app', compact("title"))

@section('content')

<x-invoice :invoice="$invoice" />

<div class="flex-right hide-for-print">
    <x-button action="{{ route('quest', ['id' => $invoice->quest_id]) }}"
        icon="angles-left" label="Wróć do zlecenia"
        />

    <x-button action="#" id="print_invoice"
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
</div>

@endsection
