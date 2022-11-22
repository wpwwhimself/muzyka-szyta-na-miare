@extends('layouts.app', ["title" => ($quest->song->title ?? "bez tytułu")." | Focus Booth"])

@section('content')
@foreach (["success", "error"] as $status)
@if (session($status))
    <x-alert :status="$status" />
@endif
@endforeach



@endsection
