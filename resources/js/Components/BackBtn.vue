<script setup>
import {router} from "@inertiajs/vue3";

defineProps(['url'])
</script>

<template>
    <v-icon @click="router.visit(url)" icon="mdi-arrow-left mr-4" size="small"/>
</template>

<style scoped>

</style>
