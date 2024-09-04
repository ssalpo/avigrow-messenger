<script setup>
import { Head, router } from '@inertiajs/vue3';
import Conversations from "@/Components/Chats/Conversations.vue";

const props = defineProps({
    activeAccount: {
        type: Object
    },
    accounts: {
        type: Array,
    },
    unreadChatIds: {
        type: Array,
    },
    conversations: {
        type: Array,
    },
    hasMore: {
        type: Boolean,
    }
});

</script>

<template>
    <Head title="Переписка" />

    <v-toolbar
        :title="`Ак: ${props.activeAccount.name}`"
        :elevation="6"
        color="blue-darken-1"
        density="comfortable">
        <v-menu>
            <template v-slot:activator="{ props }">
                <v-btn icon="mdi-account-multiple-outline" variant="text" v-bind="props"></v-btn>
            </template>

            <v-list density="compact">
                <v-list-item
                    v-for="account in accounts"
                    :key="account.id"
                    :value="account.id"
                    :active="account.id === activeAccount.id"
                    color="primary"
                    @click="() => router.visit(route('account.chats', account.id))"
                >
                    <v-list-item-title>{{ account.name }}</v-list-item-title>
                </v-list-item>
            </v-list>
        </v-menu>

        <v-btn icon>
            <v-icon>mdi-dots-vertical</v-icon>
        </v-btn>
    </v-toolbar>

    <conversations
        :unreadChatIds="unreadChatIds"
        :active-account-id="activeAccount.id"
        :conversations="conversations"
        :has-more="hasMore"
    />
</template>
