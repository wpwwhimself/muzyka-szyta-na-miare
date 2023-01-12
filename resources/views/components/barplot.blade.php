@props([
    'title', 'data',
    'maxHeight' => 100, 'labelsVert' => false,
])

@if ($title)
<h2>{{ $title }}</h2>
@endif
<div class="plot" style="grid-template-columns: repeat({{ count($data) }}, {{ 1/count($data)*100 }}%)">
@foreach ($data as $label => $value)
    <div class="bar-container">
        <div class="bar" style='height:{{ $value/max($data)*$maxHeight }}px'></div>
        <span class="value">{{ $value }}</span>
    </div>
    <div class="label @if($labelsVert) vertical @endif">{{ $label }}</div>
@endforeach
</div>
