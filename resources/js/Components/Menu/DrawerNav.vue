<script setup>
import {router, usePage} from "@inertiajs/vue3";
import {ref} from "vue";
import CompanyToggle from "@/Components/CompanyToggle.vue";

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
    // {
    //     title: 'Продукты',
    //     route: 'products.index',
    //     icon: 'mdi-cart-outline',
    //     params: null
    // },
    // {
    //     title: 'Заказы',
    //     route: 'orders.index',
    //     icon: 'mdi-order-bool-descending-variant',
    //     params: {account: activeAccount.value.id}
    // },
    // {
    //     title: 'Касса',
    //     route: 'transactions.index',
    //     icon: 'mdi-cash-register',
    //     params: null
    // },
    {
        title: 'Операторы',
        route: 'employees.index',
        icon: 'mdi-account-group-outline',
        params: null
    },
    {
        title: 'Аккаунты',
        route: 'accounts.index',
        icon: 'mdi-account-cog-outline',
        params: null
    },
    {
        title: 'Чаты Боты',
        route: 'bots.index',
        icon: 'mdi-robot-outline',
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
        <template v-slot:prepend>

            <company-toggle />

            <v-sheet class="d-flex align-center px-4 py-3">
                <v-sheet class="mr-3">
                    <v-avatar color="#bdbdbd">
                        <v-icon color="white" icon="mdi-account" />
                    </v-avatar>
                </v-sheet>
                <v-sheet>
                    <v-sheet class="text-subtitle-2 font-weight-bold">{{ page.props.auth.user.name }}</v-sheet>
                    <v-sheet class="text-body-2">{{ page.props.auth.user.email }}</v-sheet>
                </v-sheet>
            </v-sheet>

            <v-divider/>
        </template>

        <v-list>
            <v-list-item
                :prepend-icon="nav.icon"
                v-for="nav in navs"
                color="blue-darken-1"
                :title="nav.title"
                @click="() => goTo(nav)"
            />
        </v-list>

        <template v-slot:append>
            <v-sheet class="mb-5 ml-3">
                <v-btn
                    variant="text"
                    color="warning"
                    prepend-icon="mdi-power"
                    @click="router.post(route('logout'))"
                >
                    Выйти
                </v-btn>
            </v-sheet>
        </template>

    </v-navigation-drawer>
</template>

<style scoped>

</style>
