<script setup>
defineProps(["icon"])
</script>

<template>
    <div class="submit small">
        <FontAwesomeIcon v-if="icon" :icon="icon" />

        <slot />
    </div>
</template>

<style scoped>
.submit {
    cursor: pointer;
    user-select: none;
}
</style>
