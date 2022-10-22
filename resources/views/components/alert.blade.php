@props(['status'])

<div class="alert {{ $status }}">
    {{ session($status) }}
</div>
