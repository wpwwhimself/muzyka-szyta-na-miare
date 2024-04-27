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
            song.value = data
            time_major.value = parseTime(data.work_time_total)
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
</script>

<template>
<FontAwesomeIcon :icon="faHourglass" spin size="10x"
    v-if="!(modes && song)"
/>
<div class="flex-down center" v-else>
    <div id="clocks-outer" class="flex-right center">
        <FontAwesomeIcon :icon="faCog" :spin="nowWorking != 0" size="10x" />
        <div id="clocks" class="flex-down center">
            <strong>{{ renderTime(time_major) }}</strong>
            <strong class="ghost" v-if="nowWorking != 0">
                {{ modes.find(m => m.id == nowWorking)?.status_symbol }}
                {{ renderTime(time_minor) }}
            </strong>
        </div>
    </div>

    <div class="grid-2">
        <div class="buttons">
            <div v-for="mode in modes"
                class="submit clickable"
                @click="() => startStop(song.id, mode.id)"
            >
                {{ mode.status_symbol }}
            </div>
            <div
                class="submit clickable"
                style="grid-column: span 2"
                @click="() => startStop(song.id, 13)"
            >
                <FontAwesomeIcon :icon="faPauseCircle" />
                Stop
            </div>
        </div>

        <div>
            <h2>
                <FontAwesomeIcon :icon="faSnowplow" />
                Log tworzenia
            </h2>
            <table id="log-table">
                <tr v-for="log in song?.work_time"
                    :class="[
                        log.now_working && 'accent'
                    ].filter(Boolean).join(' ')"
                >
                    <td>
                        {{ modes.find(m => m.id == log.status_id).status_symbol }}
                        {{ modes.find(m => m.id == log.status_id).status_name }}
                    </td>
                    <td class="bin clickable">
                        <FontAwesomeIcon :icon="faTrash"
                            @click="() => remove(song.id, log.status_id)"
                        />
                    </td>
                    <td>{{ log.time_spent }}</td>
                </tr>
            </table>
        </div>
    </div>
</div>
</template>

<style scoped>
#clocks-outer {
    align-items: center;
}
#clocks > * {
    font-size: 4em;
    font-family: monospace;
    line-height: 90%;

    &:first-child {
        font-size: 9em;
    }
}
.buttons {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
}
.bin {
    opacity: 0;

    #log-table tr:hover & {
        opacity: 1;
    }
}
.accent {
    font-weight: bold;
}
</style>
