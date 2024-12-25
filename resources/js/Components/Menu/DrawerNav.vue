<script setup>
import {router, usePage} from "@inertiajs/vue3";
import {ref} from "vue";

const model = defineModel();

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
    },
    {
        title: 'Аккаунты',
        route: 'accounts.index',
        icon: 'mdi-account-cog-outline',
        params: null
    }
];

function goTo(nav) {
    model.value = false;

    router.visit(route(nav.route, nav.params))
}
</script>

<template>
    <v-navigation-drawer v-model="model" location="right" temporary>
        <v-list-item
            :prepend-icon="nav.icon"
            v-for="nav in navs"
            color="primary"
            :title="nav.title"
            @click="() => goTo(nav)"
        />
    </v-navigation-drawer>
</template>

<style scoped>

</style>
