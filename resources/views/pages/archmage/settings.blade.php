@extends('layouts.app')

@section('content')
<section id="settings">
    <div class="section-header">
        <h1><i class="fa-solid fa-cog"></i> Ustawienia systemu</h1>
    </div>
    <form action="{{ route("settings-update") }}" method="POST">
        @csrf
        <div class="grid" style="--col-count: 3;">
            @foreach ($settings as $setting)
            <x-input type="text"
                name="{{ $setting->setting_name }}" value="{{ $setting->value_str }}"
                label="{{ $setting->desc }}"
                />
            @endforeach
        </div>
        <x-button action="submit" label="Zapisz" icon="check" />
    </form>
</section>
@endsection
