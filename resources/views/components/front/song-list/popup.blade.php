<div id="song-demo-popup" class="popup">
    <x-shipyard.app.loader />

    <div class="popup-contents flex down center middle">
        <h3 class="song-full-title"></h3>
        <h4 class="ghost">Aranże, jakie wykonałem:</h4>
        <ul class="song-list"></ul>
        <p class="ghost">
            Kliknij ikonę <span class="accent primary">
                <x-shipyard.app.icon :name="model_icon('songs')" />
            </span>, aby odtworzyć próbkę
        </p>

        <x-file-player type="ogg" file="" is-showcase />

        <x-shipyard.ui.button
            label="Zamknij"
            icon="close"
            action="none"
            onclick="openSongDemo();"
            class="tertiary"
        />
    </div>
</div>
