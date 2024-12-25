<script setup>
import {router, usePage} from "@inertiajs/vue3";
import {ref} from "vue";

import ActiveConversationList from "@/Components/Conversation/ActiveConversationList.vue";
import DrawerNav from "@/Components/Menu/DrawerNav.vue";

defineProps(['pageTitle'])

const page = usePage()
const activeAccount = ref(page.props.activeAccount)
const drawer = ref(false);

function selectActive(account) {
    const exceptRoutes = [
        'home', 'code-keys.histories', 'code-keys.index',
        'transactions.index', 'products.index'
    ]

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

        <v-icon icon="mdi-dots-vertical" class="ml-4" @click="drawer = !drawer"></v-icon>
    </v-toolbar>

    <drawer-nav v-model="drawer" />
</template>
