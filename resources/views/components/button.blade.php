@props(['label', 'icon', 'danger' => false, 'action', "id" => null, 'small' => false, "pop" => null])

@if ($action == "submit")
<button type="submit" class="clickable hover-lift {{ $danger ? "danger" : "" }} {{ $small ? "small" : "" }}" {!! $danger ? "onclick='dangerConfirm()'" : "" !!} {{ $attributes->merge(['id' => $id]) }} {{ !$pop ?: Popper::pop($pop) }}>
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
