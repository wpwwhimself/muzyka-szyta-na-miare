<x-shipyard::app.card
    id="songs"
    title="Wszystkie utwory, których się podjąłem"
    title-lvl="2"
    :icon="model_icon('compositions')"
>
    <p>
        Kliknij ikonę <span class="accent primary">
            <x-shipyard::app.icon :name="model_icon('compositions')" />
        </span>, aby przejrzeć wykonane aranże
    </p>

    <x-shipyard::app.section
        icon="filter"
        title="Filtruj"
        title-lvl="3"
        :extended="true"
    >
        <x-slot:actions>
            <x-shipyard::ui.button
                icon="content-copy"
                pop="Kliknij, aby skopiować link do aktualnego filtru"
                onclick="copySongListLink(`query=${document.querySelector('#query').value}`);"
                action="none"
                class="tertiary"
            />
        </x-slot:actions>

        <div class="flex down" role="{{ $for }}-filters">
            <x-shipyard::ui.input type="text"
                name="query"
                placeholder="Kliknij tutaj, żeby wyszukać tytułu lub kompozytora..."
                icon="magnify"
                oninput="filterSongs(`podklady`, 'query', event.target.value)"
            />

            <p class="ghost">...lub wybierz kategorię utworu:</p>

            <div class="flex right keep-for-mobile center">
                <x-shipyard::ui.button
                    action="none"
                    class="tertiary"
                    label="wszystkie"
                    icon="close-circle"
                    onclick="filterSongs(`podklady`)"
                />

                {{-- @foreach ($genres as $genre)
                <x-shipyard::ui.button
                    action="none"
                    class="toggle"
                    :label="$genre->name"
                    icon="radio"
                    onclick="filterSongs(`podklady`, 'genre', {{ $genre->id }})"
                />
                @endforeach --}}

                @foreach ($song_tags as $tag)
                <x-shipyard::ui.button
                    action="none"
                    class="toggle"
                    :label="$tag->icon"
                    :pop="$tag->name"
                    :icon="$tag->icon ? null : 'tag'"
                    onclick="filterSongs(`podklady`, 'tag', {{ $tag->id }})"
                />
                @endforeach
            </div>

            <div class="filter-descriptions">
                @foreach ($song_tags as $tag)
                <div data-description="tag-{{ $tag->id }}"
                    class="flex down center middle no-gap hidden interactive"
                    onclick="copySongListLink(`tag={{ $tag->id }}`);"
                    @popper(Kliknij, aby skopiować aktualny filtr)
                >
                    <strong class="accent tertiary" role="name">{{ $tag->name }}</strong>
                    <span role="description">{{ $tag->description }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </x-shipyard::app.section>

    <ul id="{{ $for }}-song-list">
        <x-shipyard::app.loader />
    </ul>
</x-shipyard::app.card>

<script defer>getSongList("{{ $for }}");</script>
