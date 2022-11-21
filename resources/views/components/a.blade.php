@props(['href', 'icon' => 'angles-right'])

<a href="{{ $href }}" {{ $attributes }}>{{ $slot }} <i class="fa-solid fa-{{ $icon }}"></i></a>