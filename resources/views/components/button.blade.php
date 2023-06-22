@props(['label', 'icon', 'danger' => false, 'action', "id" => null, 'small' => false, "pop" => null])

@if ($action == "submit")
<button type="submit" {{ $attributes->class(['clickable', 'hover-lift', 'danger' => $danger, 'small' => $small])->merge(['id' => $id, 'class' => $attributes->get('class')]) }} {!! $danger ? "onclick='dangerConfirm()'" : "" !!} {{ !$pop ?: Popper::pop($pop) }}>
@else
<a href="{{ $action }}" {{ $attributes->class(['submit', 'hover-lift', 'danger' => $danger, 'small' => $small])->merge(['id' => $id]) }} {!! $danger ? "onclick='dangerConfirm()'" : "" !!}>
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
