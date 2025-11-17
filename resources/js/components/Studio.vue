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
    <div class="flex down" v-else>
        <div v-for="quest in quests" :class="[
            `card`,
            `flex`, `right`, `middle`, `spread`,
        ].filter(Boolean).join(' ')">
            <div class="flex right middle">
                <span><FontAwesomeIcon :icon="dIcons[quest.status_id]" /></span>
                <div>
                    <h3>{{ quest.song.full_title }}</h3>
                    <span><a :href="`/quests/view/${quest.id}`">{{ quest.id }}</a></span>
                </div>
            </div>

            <span><a :href="`/clients/list?search=${quest.user.notes.client_name}`">{{ quest.user.notes.client_name }}</a></span>

            <a :href="`/studio-view/${quest.id}`" class="button submit tight"><FontAwesomeIcon :icon="faAnglesRight" /></a>
        </div>
    </div>
</template>
</template>

<style scoped>
.table-row {
    grid-template-columns: 2em 1fr 4fr 2fr 4em;
}
</style>
