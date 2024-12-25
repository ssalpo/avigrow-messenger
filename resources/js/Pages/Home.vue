<script setup>
import {Head, router, usePage} from '@inertiajs/vue3';
import Conversations from "@/Components/Chats/Conversations.vue";
import {onMounted} from "vue";
import Navbar from "@/Components/Navbar.vue";
import ConversationTabs from "@/Components/ConversationTabs.vue";

const props = defineProps({
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

const page = usePage()

onMounted(() => {
    setTimeout(() => {
        document.body.scrollTop = document.documentElement.scrollTop = 0;
    }, 100)
})

</script>

<template>
    <Head title="Переписка" />

    <navbar />

    <conversation-tabs :active-account="page.props.activeAccount" />

    <conversations
        :unreadChatIds="unreadChatIds"
        :active-account-id="page.props.activeAccount.id"
        :conversations="conversations"
        :has-more="hasMore"
    />
</template>
