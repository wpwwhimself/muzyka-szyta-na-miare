@extends('layouts.app')

@section('content')
<section id="settings">
    <div class="section-header">
        <h1><i class="fa-solid fa-cog"></i> Ustawienia systemu</h1>
    </div>
    <div class="grid-2">
    @foreach ($settings as $setting)
        <span>{{ $setting->desc }}</span>
        <x-input type="text"
            name="{{ $setting->setting_name }}" value="{{ $setting->value_str }}"
            label="{{ $setting->setting_name }}" :small="true"
            />
    @endforeach
    </div>
    <script>
    $(document).ready(function(){
        $("input").change(function(){
            $.ajax({
                type: "POST",
                url: "{{ url('settings_change') }}",
                data: {
                    setting_name: $(this).attr("name"),
                    value_str: $(this).val(),
                    _token: '{{ csrf_token() }}'
                },
                success: function (response) {
                    window.location.reload();
                }
            });
        });
    });
    </script>
</section>
@endsection
