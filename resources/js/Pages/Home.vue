<script setup>
import {Head, usePage} from '@inertiajs/vue3';
import Conversations from "@/Components/Chats/Conversations.vue";
import {onMounted} from "vue";

const props = defineProps({
    accounts: {
        type: Array,
    },
    unreadChatIds: {
        type: Array,
    },
    conversations: {
        type: Object,
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

    <conversations
        :unreadChatIds="unreadChatIds"
        :active-account-id="page.props.activeAccount.id"
        :conversations="conversations.chats"
        :has-more="conversations.hasMore"
    />
</template>
