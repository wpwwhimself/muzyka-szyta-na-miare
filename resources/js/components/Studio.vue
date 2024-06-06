<script setup>
import { onMounted, ref } from 'vue';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { faHourglass, faAnglesRight, faPersonDigging, faCheck, faPeoplePulling, faReplyAll } from '@fortawesome/free-solid-svg-icons';

const url = "/api/clock/"

const quests = ref([])

const dIcons = {
    12: faPersonDigging,
    14: faCheck,
    16: faPeoplePulling,
    96: faReplyAll,
}

//////// requests ////////

const getQuests = () => {
    fetch(url + `active-quests`)
        .then(res => res.json())
        .then(data => {
            quests.value = data
        })
}

//////// boot ////////

onMounted(() => {
    getQuests()
})

setInterval(getQuests, 10e3)
</script>

<template>
<FontAwesomeIcon :icon="faHourglass" spin size="10x"
    v-if="!(true)"
/>
<template v-else>
    <span v-if="quests.length == 0" class="grayed-out">brak zleceń</span>
    <div class="quests-table" v-else>
        <div class="table-header table-row">
            <span></span>
            <span>Zlecenie</span>
            <span>Utwór</span>
            <span>Klient</span>
            <span></span>
        </div>
        <hr>
        <div v-for="quest in quests" :class="[
            `table-row`,
            `p-${quest.status_id}`,
        ].filter(Boolean).join(' ')">
            <span style="color: rgb(var(--q-clr))"><FontAwesomeIcon :icon="dIcons[quest.status_id]" /></span>
            <a :href="`/quests/view/${quest.id}`">{{ quest.id }}</a>
            <a :href="`/songs?search=${quest.song_id}`">{{ quest.song.full_title }}</a>
            <a :href="`/clients/list?search=${quest.client.client_name}`">{{ quest.client.client_name }}</a>
            <a :href="`/studio-view/${quest.id}`" class="submit small">
                <FontAwesomeIcon :icon="faAnglesRight" />
            </a>
        </div>
    </div>
</template>
</template>

<style scoped>
.table-row {
    grid-template-columns: 2em 1fr 4fr 2fr 2em;
}
</style>
