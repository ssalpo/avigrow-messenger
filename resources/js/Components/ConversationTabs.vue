<script setup>
import {router} from "@inertiajs/vue3";
import ActiveConversationList from "@/Components/Conversation/ActiveConversationList.vue";

const props = defineProps({
    activeAccount: {
        type: Object
    },
    chat: {
        type: Object
    },
})

function goToItem(url) {
    window.location.href = url
}
</script>

<template>
    <v-sheet color="deep-purple-darken-4" class="px-3 py-2 d-flex align-center justify-between" style="z-index: 1">
        <v-icon icon="mdi-chat-processing-outline" @click="() => router.visit(`/accounts/${activeAccount.id}/chats/`)"/>

        <v-sheet v-if="chat" color="deep-purple-darken-4" class="ml-3" max-width="220" style="font-size: 12px">
            <v-sheet color="deep-purple-darken-4" class="mb-2 text-truncate pr-3">{{ chat.user.name }}</v-sheet>
            <v-sheet
                @click="goToItem(chat.url)"
                color="deep-purple-darken-4" class="pr-3 text-truncate d-flex">
                <v-sheet color="deep-purple-darken-4" class="pr-3 text-truncate">{{ chat.context }}</v-sheet>
                <v-sheet color="deep-purple-darken-4" class="pr-3">{{ chat.price }}</v-sheet>
            </v-sheet>
        </v-sheet>

        <v-spacer/>

        <active-conversation-list
            :active-account="activeAccount"
            icon="mdi-menu"
        />
    </v-sheet>
</template>

<style scoped>

</style>
