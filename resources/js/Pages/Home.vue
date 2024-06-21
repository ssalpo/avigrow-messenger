<script setup>
import { Head, Link } from '@inertiajs/vue3';
import Accounts from "@/Components/Chats/Accounts.vue";
import {ref, watch} from "vue";
import Conversations from "@/Components/Chats/Conversations.vue";

defineProps({
    activeAccountId: {
        type: Number
    },
    accounts: {
        type: Array,
    },
    conversations: {
        type: Array,
    },
    hasMore: {
        type: Boolean,
    },
});

</script>

<template>
    <Head title="Переписка" />

    <div class="head-list">
        <span>Переписка</span>
    </div>

    <accounts :items="accounts"
              v-if="accounts.length"
              :active-account-id="activeAccountId" />

    <conversations
        :active-account-id="activeAccountId"
        :conversations="conversations"
        :has-more="hasMore"
    />
</template>
