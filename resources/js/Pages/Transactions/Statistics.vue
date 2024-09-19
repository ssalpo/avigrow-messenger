<script setup>
import Navbar from "@/Components/Navbar.vue";
import {Head} from "@inertiajs/vue3";

defineProps(['transaction', 'errors'])
</script>

<template>
    <Head title="Статистика"/>

    <navbar />

    <v-container>

    </v-container>
</template>
