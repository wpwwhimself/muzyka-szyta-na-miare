@props(['label', 'icon', 'danger' => false, 'action', "id" => null, 'small' => false])

@if ($action == "submit")
<button type="submit" class="hover-lift {{ $danger ? "danger" : "" }} {{ $small ? "small" : "" }}" {!! $danger ? "onclick='dangerConfirm()'" : "" !!} {{ $attributes->merge(['id' => $id]) }}>
@else
<a href="{{ $action }}" class="submit hover-lift {{ $danger ? "danger" : "" }} {{ $small ? "small" : "" }}" {!! $danger ? "onclick='dangerConfirm()'" : "" !!} {{ $attributes->merge(['id' => $id]) }}>
@endif
    @if (is_numeric($icon))
        @if ($icon >= 100)
        <span>{{ DB::table("statuses")->where("id", $icon)->value("status_symbol") }}</span>
        @else
        <i class="fa-solid {{ DB::table("statuses")->where("id", $icon)->value("status_symbol") }}"></i>
        @endif
    @else
    <i class="fa-solid fa-{{ $icon }}"></i>
    @endif
    {{ $label }}
@if ($action == "submit")
</button>
@else
</a>
@endif
