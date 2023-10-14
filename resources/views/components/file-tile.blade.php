@props(['id', 'file'])

<a
  href="{{ route('download', ['id' => $id, 'filename' => basename($file)]) }}"
  class="file-tile {{ pathinfo($file, PATHINFO_EXTENSION) }}">
  <div class="container">
    @switch(pathinfo($file, PATHINFO_EXTENSION))
      @case("pdf")
        <i class="fa-solid fa-file-pdf"></i>
        @break
      @case("zip")
        <i class="fa-solid fa-file-zipper"></i>
        @break
      @case("mp4")
        <i class="fa-solid fa-file-video"></i>
        @break
      @case("mp3")
      @case("flac")
      @case("wav")
      @case("ogg")
        <i class="fa-solid fa-file-audio"></i>
        @break
      @default
        <i class="fa-solid fa-file"></i>
    @endswitch
    <i class="fa-solid fa-download"></i>
  </div>
  <span>{{ pathinfo($file, PATHINFO_EXTENSION) }}</span>
</a>
