@props([
    "data",
])

@if($data?->has_showcase_file)
<audio controls><source src="{{ route('showcase-file-show', ['id' => $data->id]) }}?{{ time() }}" type="audio/ogg" /></audio>
@else
<span class="grayed-out">Brak showcase'u</span>
@endif

@if ($data)
<script>
function setFormToFileUpload() {
    const form = document.querySelector('form');
    form.enctype = 'multipart/form-data';
    form.action = `{{ route("showcase-file-upload") }}`;
    form.submit();
}
</script>
<x-shipyard::ui.input type="file" name="showcase_file" label="Nowy plik showcase'u" icon="file-music"
    onchange="setFormToFileUpload();"
    accept=".ogg"
/>
@endif
