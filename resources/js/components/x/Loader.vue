<script setup>
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { faHourglass } from '@fortawesome/free-solid-svg-icons';
</script>

<template>
    <div class="loader-wrapper flex-right center middle">
        <FontAwesomeIcon :icon="faHourglass" spin size="10x" />
    </div>
</template>

<style scoped>
.loader-wrapper {
    height: 50vh;
}
</style>
