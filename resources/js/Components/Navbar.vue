<script setup>
import {router, usePage} from "@inertiajs/vue3";

defineProps(['pageTitle'])

const page = usePage()
const activeAccount = page.props.activeAccount
</script>

<template>
    <v-toolbar
        :title="pageTitle || `Ак: ${activeAccount.name}`"
        :elevation="6"
        color="blue-darken-1"
        density="comfortable">
        <v-menu>
            <template v-slot:activator="{ props }">
                <v-btn icon="mdi-account-multiple-outline" variant="text" v-bind="props"></v-btn>
            </template>

            <v-list density="compact">
                <v-list-item
                    v-for="account in page.props.accounts"
                    :key="account.id"
                    :value="account.id"
                    :active="account.id === activeAccount.id"
                    color="primary"
                    @click="() => router.visit(route(route().current() === 'home' ? 'account.chats' : route().current(), {account: account.id}))"
                >
                    <v-list-item-title>{{ account.name }}</v-list-item-title>
                </v-list-item>
            </v-list>
        </v-menu>
        <v-menu>
            <template v-slot:activator="{ props }">
                <v-btn icon="mdi-dots-vertical" variant="text" v-bind="props"></v-btn>
            </template>

            <v-list density="compact">
                <v-list-item
                    color="primary"
                    title="Сообщения"
                    @click="() => router.visit(route('account.chats', {account: activeAccount.id}))"
                />

                <v-list-item
                    color="primary"
                    title="Запросы отзывов"
                    @click="() => router.visit(route('schedule-reviews.index', {account: activeAccount.id}))"
                />
            </v-list>
        </v-menu>
    </v-toolbar>
</template>

<style scoped>

</style>
