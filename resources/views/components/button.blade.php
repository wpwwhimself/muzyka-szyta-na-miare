@props(['label', 'icon', 'danger' => false, 'action'])

@if ($action == "submit")
<button type="submit" class="hover-lift {{ $danger ? "danger" : "" }}">
    <i class="fa-solid fa-{{ $icon }}"></i>
    {{ $label }}
</button>
@else
<a href="{{ $action }}" class="submit hover-lift {{ $danger ? "danger" : "" }}">
    <i class="fa-solid fa-{{ $icon }}"></i>
    {{ $label }}
</a>
@endif