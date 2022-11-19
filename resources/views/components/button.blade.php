@props(['label', 'icon', 'danger' => false, 'action'])

@if ($action == "submit")
<button type="submit" class="hover-lift {{ $danger ? "danger" : "" }}">
@else
<a href="{{ $action }}" class="submit hover-lift {{ $danger ? "danger" : "" }}" {!! $danger ? "onclick='dangerConfirm()'" : "" !!}>
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