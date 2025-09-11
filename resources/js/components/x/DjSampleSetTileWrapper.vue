<script setup>
import { onMounted, ref } from 'vue';
import DjSampleSetTile from "./DjSampleSetTile.vue";

const props = defineProps(["data"]);

const tileCodes = ref(props.data?.split(" ") || []);
</script>

<template>

<div class="samples">
    <DjSampleSetTile v-for="tileCode in tileCodes" :tile-code="tileCode" />
</div>

</template>

<style scoped>
.samples {
    display: flex;
    flex-wrap: wrap;
    gap: 3px;
}
</style>