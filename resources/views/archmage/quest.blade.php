@extends('layouts.app', ["title" => $quest->song->title." | $quest->id"])

@section('content')
@foreach (["success", "error"] as $status)
@if (session($status))
    <x-alert :status="$status" />
@endif
@endforeach


@endsection
