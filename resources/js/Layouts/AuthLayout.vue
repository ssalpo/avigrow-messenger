<script setup>
import InternetStateChecker from "@/Components/InternetStateChecker.vue";
import StateLoader from "@/Components/StateLoader.vue";
</script>

<template>
    <internet-state-checker />

    <state-loader />

    <slot />
</template>
