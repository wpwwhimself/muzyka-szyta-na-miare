<script setup>
import { onMounted, ref } from 'vue';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { faCog, faPauseCircle, faHourglass, faTrash, faSnowplow } from '@fortawesome/free-solid-svg-icons';

const url = "/api/clock/"
const [id] = window.location.href
    .split("/")
    .slice(-1)

let modes = ref([])
let song = ref()

//////// time tracking and display ////////

let time_major = ref(0)
let time_minor = ref(0)
let nowWorking = ref(0)
let timerId

const tickTime = () => {
    time_minor.value++
    time_major.value++
}

/**
 * Starts the clocks and highlights current mode
 * @param {number} status_id - if nothing, then 0
 */
const startTime = (status_id) => {
    if (!timerId) timerId = setInterval(tickTime, 1e3)
    nowWorking.value = status_id
}

const stopTime = () => {
    clearInterval(timerId)
    timerId = undefined
    nowWorking.value = 0
}

/**
 * Interpret seconds as time string
 * @param {number} sec - number of seconds
 */
const renderTime = (sec) => {
    const hours = Math.floor(sec / 3600).toString()
    const minutes = Math.floor((sec % 3600) / 60).toString().padStart(2, 0)
    const seconds = (sec % 60).toString().padStart(2, 0)

    return `${hours}:${minutes}:${seconds}`
}

/**
 * Parse time string to seconds
 * @param {string} timestring - time string in format H:MM:SS
 */
const parseTime = (timestring) => {
    const [hours, minutes, seconds] = timestring
        .split(":")
        .map(no => parseInt(no))

    return hours * 3600 + minutes * 60 + seconds
}

//////// requests ////////

const getSong = () => {
    fetch(url + `song-data-by-quest/${id}/`)
        .then(res => res.json())
        .then(data => {
            song.value = data.song
            if (!nowWorking.value) time_major.value = parseTime(data.song.work_time_total)

            if (data.status_id != 12) {
                window.location.href = `/studio-view/`
            }
        })
}

/**
 * Start or stop timer for a song
 * @param {string} song_id
 * @param {string} status_id - log status (use 13 to stop)
 */
const startStop = (song_id, status_id) => {
    fetch(url + "start-stop", {
        method: "post",
        body: JSON.stringify({
            status_id: status_id,
            song_id: song_id,
        }),
        headers: {
            "Content-Type": "application/json",
        }
    }).then(res => res.json())
        .then(res => {
            getSong()
            if (res.now_working) {
                time_minor.value = parseTime(res.started?.time || "00:00:00")
                startTime(res.started?.status_id)
            } else {
                stopTime()
            }
        })
}

/**
 * Remove log for a particular mode and song
 * @param {string} song_id
 * @param {string} status_id
 */
const remove = (song_id, status_id) => {
    if (!confirm("Ostrożnie! Czy na pewno chcesz usunąć ten wpis?")) return

    fetch(url + `remove/${song_id}/${status_id}/`)
        .then(res => res.json())
        .then(res => {
            getSong()
        })
}

//////// boot ////////

onMounted(() => {
    fetch(url + "modes")
        .then(res => res.json())
        .then(data => {
            modes.value = data
        })

    getSong()
})

setInterval(getSong, 10e3)
</script>

<template>
<div class="flex right center"
    v-if="!(modes && song)"
>
    <FontAwesomeIcon :icon="faHourglass" spin size="10x" />
</div>
<div class="flex down center" v-else>
    <div id="clocks-outer" class="flex right center">
        <FontAwesomeIcon :icon="faCog" :spin="nowWorking != 0" size="8x" />
        <div id="clocks" class="flex down center">
            <strong>{{ renderTime(time_major) }}</strong>
            <strong class="ghost" v-if="nowWorking != 0">
                {{ modes.find(m => m.id == nowWorking)?.status_symbol }}
                {{ renderTime(time_minor) }}
            </strong>
        </div>
    </div>

    <div class="grid" style="--col-count: 2;" style="align-items: normal;">
        <div class="buttons">
            <div v-for="mode in modes"
                class="submit tight interactive"
                @click="() => startStop(song.id, mode.id)"
            >
                {{ mode.status_symbol }}
            </div>
            <div
                class="submit tight interactive"
                style="grid-column: span 2"
                @click="() => startStop(song.id, 13)"
            >
                <FontAwesomeIcon :icon="faPauseCircle" />
                Stop
            </div>
        </div>

        <div id="log-table" class="flex right keep-for-mobile center nowrap">
            <div v-for="log in song?.work_time"
                class="plot-column"
            >
                <div class="plot-bar-container">
                    <div :class="[
                        'plot-bar',
                        log.now_working && 'accent'
                    ].filter(Boolean).join(' ')"
                        :style="{
                            height: `${parseTime(log.time_spent) / Math.max(...song?.work_time.map(t => parseTime(t.time_spent))) * 100}px`,
                        }"
                    ></div>
                </div>

                <span class="plot-small-label">{{ modes.find(m => m.id == log.status_id).status_symbol }}</span>
                <span class="plot-big-label">
                    {{ modes.find(m => m.id == log.status_id).status_symbol }}
                    <b>{{ modes.find(m => m.id == log.status_id).status_name }}</b>:
                    {{ log.time_spent }}
                </span>
                <span class="bin interactive">
                    <FontAwesomeIcon :icon="faTrash"
                        @click="() => remove(song.id, log.status_id)"
                    />
                </span>
            </div>
        </div>
    </div>
</div>
</template>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Sometype+Mono:wght@400..700&display=swap');
#clocks-outer {
    align-items: center;
    margin-bottom: var(--size-m);
}
#clocks > * {
    font-size: calc(var(--size-xxl) * 1.5);
    font-family: "Sometype Mono";
    line-height: 90%;

    &:first-child {
        font-size: calc(var(--size-xxl) * 3);
    }
}
.buttons {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
}
.accent {
    font-weight: bold;
}

#log-table {
    gap: 0 0.25em;
    position: relative;

    & .plot-column {
        display: grid;
        grid-auto-flow: column;
        grid-template-rows: auto 1.5em 1.5em;
        justify-items: center;

        & .plot-bar-container {
            position: relative;
            width: 1.25em;
            height: 7em;

            & .plot-bar {
                background-color: var(--bg-lite2);
                position: absolute;
                bottom: 0;
                width: 100%;
                border-radius: 0.25em;

                &.accent {
                    background-color: var(--primary);
                }
            }
        }

        & .plot-big-label {
            display: none;
            position: absolute;
            top: 0; left: 0; right: 0;
            z-index: 2;
            text-align: center;

            .plot-column:hover & {
                display: block;
            }
        }

        & .bin {
            opacity: 0;

            .plot-column:hover & {
                opacity: 1;
            }
        }
    }
}
</style>
