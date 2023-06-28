<div>
    @if ($title)
    <h2>{!! $title !!}</h2>
    @endif
    <div class="stats-highlight-h" style="grid-template-columns: repeat({{ count((array)$data1) }}, 1fr);">
        @foreach ($data1 as $name => $val)
        <p>{{ $name }}</p>
        <h3>
            {{ $allPln ? _c_(as_pln($val)) : _ct_($val) }}
            @if($bracketedNumbers)
            <small class="ghost">
                @switch($bracketedNumbers)
                    @case("percentages")
                        ({{ _c_(round($val / $data2 * 100)) }}%)
                        @break
                    @case("comparison")
                        <span class="{{ $data2->{$name} > 0 ? 'diff-hot' : 'diff-cold' }}">
                            {{ _c_(sprintf("%+d", $data2->{$name})) }}
                        </span>
                        @break
                @endswitch
            </small>
            @endif
        </h3>
        @endforeach
    </div>
</div>
