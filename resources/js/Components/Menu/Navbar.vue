<script setup>
import {router, usePage} from "@inertiajs/vue3";
import {ref} from "vue";

import ActiveConversationList from "@/Components/Conversation/ActiveConversationList.vue";

defineProps(['pageTitle'])

const page = usePage()
const activeAccount = ref(page.props.activeAccount)

const navs = [
    {
        title: 'Сообщения',
        route: 'account.chats',
        icon: 'mdi-email-outline',
        params: {account: activeAccount.value.id}
    },
    {
        title: 'Запросы отзывов',
        route: 'schedule-reviews.index',
        icon: 'mdi-timer-star',
        params: {account: activeAccount.value.id}
    },
    {
        title: 'Отзывы',
        route: 'reviews.index',
        icon: 'mdi-star-outline',
        params: {account: activeAccount.value.id}
    },
    {
        title: 'Ключи',
        route: 'code-keys.index',
        icon: 'mdi-key-variant',
        params: null
    },
    {
        title: 'Продукты',
        route: 'products.index',
        icon: 'mdi-cart-outline',
        params: null
    },
    {
        title: 'Заказы',
        route: 'orders.index',
        icon: 'mdi-order-bool-descending-variant',
        params: {account: activeAccount.value.id}
    },
    {
        title: 'Касса',
        route: 'transactions.index',
        icon: 'mdi-cash-register',
        params: null
    }
];

const exceptRoutes = ['home', 'code-keys.histories', 'code-keys.index']

function selectActive(account) {
    activeAccount.value = account

    router.visit(route(exceptRoutes.includes(route().current())  ? 'account.chats' : route().current(), {account: account.id}))
}
</script>

<template>
    <v-toolbar
        :elevation="6"
        color="blue-darken-1"
        class="px-3"
        density="comfortable">

        <v-menu>
            <template v-slot:activator="{ props }">
                <v-btn
                    variant="outlined"
                    size="small"
                    v-bind="props"
                    prepend-icon="mdi-account-multiple-outline"
                    append-icon="mdi-menu-down"
                    min-width="140"
                    max-width="180"
                >
                    {{activeAccount.name}}
                </v-btn>
            </template>

            <v-list density="compact" class="py-0 mt-1">
                <v-list-item
                    min-height="30"
                    v-for="account in page.props.accounts"
                    :key="account.id"
                    :value="account.id"
                    :active="account.id === activeAccount.id"
                    color="primary"
                    @click="selectActive(account)"
                >
                    <v-list-item-title>{{ account.name }}</v-list-item-title>
                </v-list-item>
            </v-list>
        </v-menu>

        <v-spacer />

        <active-conversation-list
            :active-account="activeAccount" />

        <v-menu>
            <template v-slot:activator="{ props }">
                <v-icon icon="mdi-dots-vertical" class="ml-4" v-bind="props"></v-icon>
            </template>

            <v-list density="compact">
                <v-list-item
                    :prepend-icon="nav.icon"
                    v-for="(nav, index) in navs"
                    color="primary"
                    :title="nav.title"
                    @click="() => router.visit(route(nav.route, nav.params))"
                />
            </v-list>
        </v-menu>
    </v-toolbar>
</template>
