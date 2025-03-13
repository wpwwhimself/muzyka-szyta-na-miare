<script setup>
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { faCog } from '@fortawesome/free-solid-svg-icons';
</script>

<template>
    <div class="loader-wrapper flex-right center middle">
        <FontAwesomeIcon :icon="faCog" spin size="10x" />
    </div>
</template>

<style scoped>
.loader-wrapper {
    position: fixed;
    top: 0; left: 0; right: 0; bottom: 0;
    background-color: rgba(0, 0, 0, 0.5);
}
</style>
