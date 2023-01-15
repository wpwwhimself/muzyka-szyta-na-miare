@props([
    "data"
])

<div class="stats-highlight-h" style="grid-template-columns: repeat({{ count($data) }}, 1fr);">
    @foreach ($data as $name => $val)
    <p>{{ $name }}</p>
    <h2>{{ $val }}</h2>
    @endforeach
</div>
