@props([
    "data", "title" => null,
    "percentages" => false,
])

<div>
    @if ($title)
    <h2>{!! $title !!}</h2>
    @endif
    <div class="stats-highlight-h" style="grid-template-columns: repeat({{ count($percentages ? (array)$data->split : (array)$data) }}, 1fr);">
        @foreach ($percentages ? $data->split : $data as $name => $val)
        <p>{{ $name }}</p>
        <h3>
            {{ $val  }}
            @if ($percentages)
            <small class="ghost">
                ({{ round($val / $data->total * 100) }}%)
            </small>
            @endif
        </h3>
        @endforeach
    </div>
</div>
