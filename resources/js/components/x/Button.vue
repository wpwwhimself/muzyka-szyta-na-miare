<script setup>
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { faAnglesLeft } from '@fortawesome/free-solid-svg-icons';

defineProps(["icon"])
</script>

<template>
    <div class="submit small">
        <FontAwesomeIcon :icon="icon ?? faAnglesLeft" />

        <slot />
    </div>
</template>

<style scoped>
.submit {
    cursor: pointer;
    user-select: none;
}
</style>
