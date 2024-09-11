<script setup>
import {router, usePage} from "@inertiajs/vue3";

defineProps(['pageTitle'])

const page = usePage()
const activeAccount = page.props.activeAccount

const navs = [
    {
        title: 'Сообщения',
        route: 'account.chats',
        params: {account: activeAccount.id}
    },
    {
        title: 'Запросы отзывов',
        route: 'schedule-reviews.index',
        params: {account: activeAccount.id}
    },
    {
        title: 'Отзывы',
        route: 'reviews.index',
        params: {account: activeAccount.id}
    },
    {
        title: 'Ключи',
        route: 'code-keys.index',
        params: null
    }
];

const exceptRoutes = ['home', 'code-keys.histories', 'code-keys.index']
</script>

<template>
    <v-toolbar
        :title="pageTitle || `Ак: ${activeAccount.name}`"
        :elevation="6"
        color="blue-darken-1"
        density="comfortable">

        <v-btn icon="mdi-key-chain" variant="text" @click="router.visit(route('code-keys.index'))"></v-btn>

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
                    @click="() => router.visit(route(exceptRoutes.includes(route().current())  ? 'account.chats' : route().current(), {account: account.id}))"
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
                    v-for="(nav, index) in navs"
                    color="primary"
                    :title="nav.title"
                    @click="() => router.visit(route(nav.route, nav.params))"
                />
            </v-list>
        </v-menu>
    </v-toolbar>
</template>

<style scoped>

</style>
