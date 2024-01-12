@props([
    'title', 'data',
    'maxHeight' => 100, 'labelsVert' => false,
    'percentages' => false,
    'allPln' => false,
    'raw' => false,
])

@if ($title)
<h2>{{ $title }}</h2>
@endif
<div class="plot" style="grid-template-columns: repeat({{ count($percentages ? (array)$data->split : (array)$data) }}, 1fr)">
@foreach ($percentages ? ($raw ? $data->raw : $data->split) : $data as $label => $val)
    <div class="bar-container">
        <div class="bar" style='height:{{ $val/(max($percentages ? ($raw ? (array)$data->raw : (array)$data->split) : (array)$data))*$maxHeight }}px'></div>
        <span class="value">
            @if ($percentages)
            <small class="ghost">
                ({{ _c_(round($val / $data->total * 100)) }}%)
            </small>
            <br />
            @endif
            @if ($allPln)
            <small>{{ _c_(as_pln($val)) }}</small>
            @else
            {{ _c_($val) }}
            @endif
        </span>
    </div>
    <div class="label @if($labelsVert) vertical @endif">{{ $label }}</div>
@endforeach
</div>
