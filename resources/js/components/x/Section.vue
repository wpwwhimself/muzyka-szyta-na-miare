<script setup>
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';

defineProps(["title", "icon"])
</script>

<template>

<section class="container">
    <div class="section-header">
        <h1>
            <FontAwesomeIcon :icon="icon" />
            {{ title }}
        </h1>

        <slot name="buttons" />
    </div>

    <slot />
</section>

</template>
