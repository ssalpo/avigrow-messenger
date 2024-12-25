<script setup>

import Navbar from "@/Components/Menu/Navbar.vue";
import {Head, router} from "@inertiajs/vue3";
import PageTitle from "@/Components/PageTitle.vue";

const props = defineProps(['reviews']);

function remove(account, id) {
    router.delete(route('schedule-reviews.destroy', {account: account, 'schedule_review': id}), {
        preserveScroll: true,
        preserveState: true,
    })
}

</script>

<template>
    <page-title text="Запросы отзывов"/>

    <v-card
        v-for="review in reviews"
        :key="review.id"
        class="mb-4"
        rel="noopener"
        elevation="4"
    >
        <v-card-text>
            Запустится <b>{{ review.send_at }}</b>
        </v-card-text>

        <template v-slot:actions>
            <v-btn color="error" icon="mdi-trash-can-outline"
                   @click="() => remove(review.account_id, review.id)"></v-btn>
            <v-spacer/>
            <v-btn icon="mdi-open-in-new"
                   :href="route('account.chat.messages', {account: review.account_id, chat: review.chat_id})"></v-btn>
        </template>

    </v-card>
</template>

<style scoped>

</style>
