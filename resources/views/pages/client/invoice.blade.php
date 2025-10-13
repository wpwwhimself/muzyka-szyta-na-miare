@extends('layouts.app', compact("title"))

@section('content')

<x-invoice :invoice="$invoice" />

<div class="flex right hide-for-print">
    <x-button action="#/" id="print_invoice"
        icon="download" label="Drukuj"
        />
    <script defer>
    $("#print_invoice").click(function(){
        $("header, footer>div>p").addClass("hide-for-print");
        window.print();
    });
    </script>
</div>

@endsection
