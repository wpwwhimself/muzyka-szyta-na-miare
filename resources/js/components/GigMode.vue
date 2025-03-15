<script setup>
import { onMounted, ref } from 'vue';
import { faList, faCompactDisc, faRotateRight } from '@fortawesome/free-solid-svg-icons';
import Section from "./x/Section.vue"
import Tile from './x/Tile.vue';
import Loader from './x/Loader.vue';
import Button from './x/Button.vue';
import { set } from 'lodash';

const url = "/api/dj/gig-mode/"
const params = new URLSearchParams(window.location.search)

const songs = ref([])
const sets = ref([])
const song = ref(null)
const songset = ref(null)

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
            sets.value = data.sets
        })
        .finally(() => show_loader.value = false)

}

function openSong(s) {
    const id = s.id ?? s
    show_loader.value = true
    fetch(url + `song/${id}`)
        .then(res => res.json())
        .then(data => {
            song.value = data
        })
        .finally(() => show_loader.value = false)
}

function openSet(s) {
    const id = s.id ?? s
    show_loader.value = true
    fetch(url + `set/${id}`)
        .then(res => res.json())
        .then(data => {
            songset.value = data
        })
        .finally(() => show_loader.value = false)
}

function refresh() {
    show_loader.value = true

    if (song.value) {
        fetch(url + `song/${song.value.id}`)
            .then(res => res.json())
            .then(data => {
                song.value = data
            })
    }
    if (songset.value) {
        fetch(url + `set/${songset.value.id}`)
            .then(res => res.json())
            .then(data => {
                songset.value = data
            })
    }

    show_loader.value = false
}

function backToInit() {
    song.value = null
    songset.value = null
    getInitData()
}

function coloredParts(parts) {
    const alphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ".split("")
        .map((a, i) => ({ letter: a, color: (i / 26 * 360 * 17) % 360 }))
    return parts.map(part => ({ part: part, color: `hsl(${alphabet.find(a => part.startsWith(a.letter)).color}, 70%, 60%)` }))
}
// #endregion

// #region boot
onMounted(() => {
    if (params.get("song")) {
        openSong(params.get("song"))
    } else if (params.get("set")) {
        openSet(params.get("set"))
    } else {
        getInitData()
    }
})
// #endregion
</script>

<template>

<Loader v-if="show_loader" />

<template v-if="!song && !songset">
    <Section title="Zestawy" :icon="faList">
        <template #buttons>
            <Button :icon="faRotateRight" @click="getInitData()">Odśwież</Button>
        </template>

        <div class="flex-right">
            <Tile v-for="set in sets" @click="openSet(set)">
                <span class="accent">{{ set.name }}</span>
                <span>{{ set.songs_count }} utworów</span>
            </Tile>
        </div>
    </Section>

    <Section title="Utwory" :icon="faCompactDisc">
        <div class="flex-right">
            <Tile v-for="song in songs" @click="openSong(song)">
                <span>{{ song.tempo_pretty }}</span>
                <span class="accent">{{ song.title }}</span>
                <span>{{ song.artist }}</span>
            </Tile>
        </div>
    </Section>
</template>

<template v-if="songset">
    <Section :title="songset.name" :icon="faList">
        <template #buttons>
            <Button :icon="faRotateRight" @click="refresh()">Odśwież</Button>
            <Button @click="backToInit()">Powrót</Button>
        </template>

        <div class="flex-right nowrap folding">
            <Tile v-for="song in songset.songs" @click="openSong(song)">
                <span>{{ song.tempo_pretty }}</span>
                <span class="accent">{{ song.title }}</span>
                <span>{{ song.artist }}</span>
            </Tile>
        </div>
    </Section>
</template>

<template v-if="song">
    <Section :title="song.full_title" :icon="faCompactDisc">
        <template #buttons v-if="!songset">
            <Button :icon="faRotateRight" @click="refresh()">Odśwież</Button>
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
            <div v-for="part in coloredParts(song.parts)" class="flex-right">
                <h2 :style="{ color: part.color }">{{ part.part }}</h2>
                <div class="lyrics" v-if="song.lyrics && song.lyrics[part.part]">{{ song.lyrics[part.part] }}</div>
                <pre class="chords" v-if="song.chords && song.chords[part.part]">{{ song.chords[part.part] }}</pre>
                <div class="ghost" v-if="song.notes && song.notes[part.part]">{{ song.notes[part.part] }}</div>
            </div>
        </div>
    </Section>
</template>

</template>

<style scoped>
.accent {
    font-weight: bold;
}

.folding {
    & span {
        white-space: nowrap;
    }
}

#song-innards {
    gap: var(--size-m);

    & > div {
        align-items: flex-start;

        & .lyrics {
            white-space-collapse: preserve-breaks;
        }

        & h2, & pre {
            margin: 0;
        }
    }
}
</style>
