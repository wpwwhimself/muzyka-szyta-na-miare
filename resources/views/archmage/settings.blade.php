@extends('layouts.app')

@section('content')
<section id="settings">
    <div class="section-header">
        <h1><i class="fa-solid fa-cog"></i> Ustawienia systemu</h1>
    </div>
    <div class="flex-right">
    @foreach ($settings as $name => $value)
        <x-input type="text"
            name="{{ $name }}" value="{{ $value }}"
            label="{{ $name }}"
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
