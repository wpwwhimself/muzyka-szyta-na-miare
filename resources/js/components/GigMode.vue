<script setup>
import { onMounted, ref } from 'vue';
import { faList, faCompactDisc, faVials, faRotateRight } from '@fortawesome/free-solid-svg-icons';
import Section from "./x/Section.vue"
import Tile from './x/Tile.vue';
import Loader from './x/Loader.vue';
import Button from './x/Button.vue';
import DjSampleSetTileWrapper from './x/DjSampleSetTileWrapper.vue';
import { set } from 'lodash';

const url = "/api/dj/gig-mode/"
const params = new URLSearchParams(window.location.search)

const songs = ref([])
const sets = ref([])
const sampleSets = ref([])
const song = ref(null)
const songset = ref(null)

const show_loader = ref(true)

const headerFields = [
    { name: "key", label: "Tonacja" },
    { name: "tempo_pretty", label: "Tempo" },
    { name: "dj_sample_set_id", label: "Sample" },
]

// #region functions
function getInitData() {
    show_loader.value = true
    fetch(url + "init-data")
        .then(res => res.json())
        .then(data => {
            songs.value = data.songs
            sets.value = data.sets
            sampleSets.value = data.sampleSets
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

function openSet(s, mode = 'set') {
    const id = s.id ?? s
    show_loader.value = true
    fetch(url + `${mode}/${id}`)
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
    return parts?.map(part => ({ part: part, color: `hsl(${alphabet.find(a => part.startsWith(a.letter)).color}, 70%, 60%)` }))
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

    <Section title="Sample" :icon="faVials">
        <div class="flex-right">
            <Tile v-for="set in sampleSets" @click="openSet(set, 'sample-set')">
                <span>{{ set.id }}</span>
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

        <div class="flex right nowrap folding">
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

        <div class="flex right center middle">
            <div class="flex down center" v-for="field in headerFields">
                <span class="grayed-out">{{ field.label }}</span>

                <span v-if="field.name == 'songmap'" class="flex right center" style="gap: 3px">
                    <span v-for="part in coloredParts(song.parts)" :style="{ color: part.color }">{{ part.part }}</span>
                </span>
                <span v-else>{{ song[field.name] }}</span>
            </div>
        </div>

        <div id="song-innards" class="flex down">
            <template v-for="part in coloredParts(song.parts)">
                <h2 :style="{ color: part.color }">{{ part.part }}</h2>
                <DjSampleSetTileWrapper :data="song.samples ? song.samples[part.part] : null" />
                <div class="lyrics">{{ song.lyrics ? song.lyrics[part.part] : null }}</div>
                <pre class="chords">{{ song.chords ? song.chords[part.part] : null }}</pre>
                <div class="ghost">{{ song.extra_notes ? song.extra_notes[part.part] : null }}</div>
            </template>
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
    display: grid;
    grid-template-columns: 1.5em auto auto auto 1fr;
    gap: var(--size-m);
    align-items: flex-start;

    & .lyrics {
        white-space-collapse: preserve-breaks;
    }

    & h2 {
        text-align: center;
    }

    & h2, & pre {
        margin: 0;
    }
}
</style>
