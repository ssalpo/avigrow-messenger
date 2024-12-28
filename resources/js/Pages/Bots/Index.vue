<script setup>
import {Link, router} from "@inertiajs/vue3";
import PageTitle from "@/Components/PageTitle.vue";

defineProps(['bots'])

const onChangeStatus = (bot) => {
    bot.is_active = !bot.is_active

    axios.post(route('bots.change-activity', bot.id))
}
</script>

<template>
    <page-title text="Чат Боты">
        <template v-slot:append>
            <v-spacer></v-spacer>
            <Link :href="route('bots.create')" class="mr-3">
                <v-btn color="success" size="small" icon="mdi-plus-circle-outline"/>
            </Link>
        </template>
    </page-title>

    <v-sheet class="mx-auto" max-width="500">
        <v-sheet
            border
            rounded
            hover
            v-for="bot in bots"
            class="mb-3 d-flex align-center px-5 cursor-pointer"
        >
            <div @click="router.visit(route('bots.show', bot.id))" class="w-100">{{ bot.name }}</div>
            <v-spacer/>
            <v-switch color="primary" @click="() => onChangeStatus(bot)" hide-details :model-value="bot.is_active"></v-switch>
        </v-sheet>
    </v-sheet>
</template>
