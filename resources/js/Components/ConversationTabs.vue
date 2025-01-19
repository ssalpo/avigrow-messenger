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
    <v-sheet color="blue-darken-1" class="px-3 py-2 d-flex align-center justify-between" style="z-index: 1">
        <v-icon icon="mdi-arrow-left" @click="() => router.visit(`/accounts/${activeAccount.id}/chats/`)"/>

        <v-sheet v-if="chat" color="blue-darken-1" class="ml-3 text-truncate" max-width="220" :style="{fontSize: '15px'}" >
            {{ chat.user.name }}
        </v-sheet>

        <v-spacer/>

        <active-conversation-list
            :active-account="activeAccount"
            icon="mdi-forum-outline"
        />
    </v-sheet>

    <v-sheet
        v-if="chat.context !== ''"
        @click="goToItem(chat.url)"
        color="blue-darken-1" class="pr-3 px-4 py-2 text-truncate d-flex" style="border-top: 1px solid #ffffff8f; font-size: 12px">
        <v-sheet color="blue-darken-1" class="pr-3 text-truncate">{{ chat.context }}</v-sheet>
        <v-sheet color="blue-darken-1" class="pr-3">{{ chat.price }}</v-sheet>
    </v-sheet>
</template>

<style scoped>

</style>
