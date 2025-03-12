<script setup>
import { onMounted, ref } from 'vue';
import { faList, faCompactDisc } from '@fortawesome/free-solid-svg-icons';
import Section from "./x/Section.vue"
import Tile from './x/Tile.vue';
import Loader from './x/Loader.vue';
import Button from './x/Button.vue';

const url = "/api/dj/gig-mode/"
const params = new URLSearchParams(window.location.search)

const mode = ref("init")
const songs = ref([])
// const sets = ref([])
const song = ref(null)
// const set = ref(null)

const show_loader = ref(true)

const headerFields = [
    { name: "key", label: "Tonacja" },
    { name: "tempo_pretty", label: "Tempo" },
    { name: "songmap", label: "Mapa utworu" },
]

// #region functions
function getInitData() {
    show_loader.value = true
    fetch(url + "init-data")
        .then(res => res.json())
        .then(data => {
            songs.value = data.songs
            // sets.value = data.sets
        })
        .finally(() => show_loader.value = false)

}

function openSong(s) {
    show_loader.value = true
    mode.value = "song"
    fetch(url + `song/${s.id}`)
        .then(res => res.json())
        .then(data => {
            song.value = data
        })
        .finally(() => show_loader.value = false)
}

function backToInit() {
    mode.value = "init"
    song.value = null
    getInitData()
}

function coloredParts(parts) {
    const alphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ".split("")
        .map((a, i) => ({ letter: a, color: ((26 * 3 + 1) * i) % 360 }))
    return parts.map(part => ({ part: part, color: `hsl(${alphabet.find(a => part.startsWith(a.letter)).color}, 70%, 60%)` }))
}
// #endregion

// #region boot
onMounted(() => {
    getInitData()
})
// #endregion
</script>

<template>

<Loader v-if="show_loader" />
<template v-else>
    <div v-if="mode == 'init'">
        <!-- <Section title="Zestawy" :icon="faList">
        </Section> -->

        <Section title="Utwory" :icon="faCompactDisc">
            <div class="flex-right wrap">
                <Tile v-for="song in songs" @click="openSong(song)">
                    <span class="accent">{{ song.title }}</span>
                    <span>{{ song.artist }}</span>
                </Tile>
            </div>
        </Section>
    </div>

    <div v-if="mode == 'song'">
        <Section :title="song.full_title" :icon="faCompactDisc">
            <template #buttons>
                <Button @click="backToInit()">Powrót</Button>
            </template>

            <div class="grid-3">
                <div class="flex-down center" v-for="field in headerFields">
                    <span class="grayed-out">{{ field.label }}</span>

                    <span v-if="field.name == 'songmap'" class="flex-right center" style="gap: 3px">
                        <span v-for="part in coloredParts(song.parts)" :style="{ color: part.color }">{{ part.part }}</span>
                    </span>
                    <span v-else>{{ song[field.name] }}</span>
                </div>
            </div>

            <div id="song-innards" class="flex-down">
                <div v-for="part in coloredParts(song.parts)">
                    <h2 :style="{ color: part.color }">{{ part.part }}</h2>
                    <div class="lyrics" v-if="song.lyrics && song.lyrics[part]">{{ song.lyrics[part] }}</div>
                    <pre class="chords" v-if="song.chords && song.chords[part]">{{ song.chords[part] }}</pre>
                    <pre v-if="song.notes && song.notes[part]">{{ song.notes[part] }}</pre>
                </div>
            </div>
        </Section>
    </div>
</template>

</template>

<style scoped>
.accent {
    font-weight: bold;
}

#song-innards {
    gap: var(--size-m);

    & > div {
        display: grid;
        gap: var(--size-s);
        grid-template-columns: auto 1fr auto auto;

        & .lyrics {
            white-space-collapse: preserve-breaks;
        }

        & h2 {
            margin: 0;
        }
    }
}
</style>
