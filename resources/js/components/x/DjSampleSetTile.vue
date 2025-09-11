<script setup>
import { onMounted, ref } from 'vue';

defineProps(["tile-code"]);

// #region functions
function codeToColor(letter) {
    const alphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ".split("")
        .map((a, i) => ({ letter: a, color: (i / 26 * 360 * 8) % 360 }));
    return `hsl(${alphabet.find(a => a.letter == letter).color}, 70%, 60%)`;
}
// #endregion
</script>

<template>

<div class="tile" :style="{ '--color': `${codeToColor(tileCode.charAt(0))}` }">
    {{ tileCode.charAt(1) }}
</div>

</template>

<style scoped>
.tile {
    --size: 1.75em;
    --color: var(--bg1);

    width: var(--size);
    height: var(--size);
    display: flex; justify-content: center; align-items: center;
    border-radius: var(--size-xs);
    background-color: var(--color);

    font-family: "Krona One";
}
</style>
