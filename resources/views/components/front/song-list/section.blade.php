<div id="songs">
    <h2>Wszystkie utwory, których się podjąłem</h2>
    <p>
        Kliknij ikonę <span class="accent primary">
            <x-shipyard.app.icon :name="model_icon('compositions')" />
        </span>, aby przejrzeć wykonane aranże
    </p>

    <h3>Filtruj:</h3>
    <div class="flex right keep-for-mobile center">
        <x-shipyard.ui.button
            action="none"
            class="tertiary"
            label="wszystkie"
            icon="close-circle"
            onclick="filterSongs(`podklady`)"
        />

        @foreach ($genres as $genre)
        <x-shipyard.ui.button
            action="none"
            class="tertiary"
            :label="$genre->name"
            icon="radio"
            onclick="filterSongs(`podklady`, 'genre', {{ $genre->id }})"
        />
        @endforeach

        @foreach ($song_tags as $tag)
        <x-shipyard.ui.button
            action="none"
            class="tertiary"
            :label="$tag->name"
            icon="tag"
            onclick="filterSongs(`podklady`, 'tag', {{ $tag->id }})"
        />
        @endforeach
    </div>

    <ul id="{{ $for }}-song-list">
        <x-shipyard.app.loader />
    </ul>
    <script defer>getSongList("{{ $for }}");</script>
</div>
