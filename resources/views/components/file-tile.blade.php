@props(['id', 'file'])

<a
    href="{{ Storage::exists($file)
        ? route('download', ['id' => $id, 'filename' => basename($file)])
        : '#/'
    }}"
    class="file-tile {{ pathinfo($file, PATHINFO_EXTENSION) }}"
>
    <div class="container flex down center middle">
        @if (!Storage::exists($file))
        <span class="accent danger"><x-shipyard.app.icon name="alert" /></span>
        @else
        @switch(pathinfo($file, PATHINFO_EXTENSION))
            @case("pdf")
            <x-shipyard.app.icon name="file-document" />
            @break

            @case("zip")
            <x-shipyard.app.icon name="folder-zip" />
            @break

            @case("mp4")
            <x-shipyard.app.icon name="file-video" />
            @break

            @case("mp3")
            @case("flac")
            @case("wav")
            @case("ogg")
            <x-shipyard.app.icon name="file-music" />
            @break

            @default
            <x-shipyard.app.icon name="file" />
        @endswitch
        @endif
    </div>
    <span>{{ pathinfo($file, PATHINFO_EXTENSION) }}</span>
</a>
