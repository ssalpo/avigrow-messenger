<script setup>
import {onMounted, ref} from "vue";
import {router} from "@inertiajs/vue3";

    const props = defineProps({
        activeAccount: {
            type: Object
        },
        chat: {
            type: Object
        },
    })

    let conversations = ref([])

    onMounted(() => {
        axios.get(`/api/active-conversations`)
            .then((r) => {
                conversations.value = r.data
            })
    });

    function goToItem(url) {
        window.location.href = url
    }

    function clear(index, conversation) {
        conversations.value.splice(index, 1)
        axios.delete(`/api/active-conversations/${conversation.id}`)
    }

</script>

<template>
    <v-sheet color="deep-purple-darken-4" class="px-3 py-2 d-flex align-center justify-between">
        <v-icon icon="mdi-chat-processing-outline" @click="() => router.visit(`/accounts/${activeAccount.id}/chats/`)" />

        <v-sheet v-if="chat" color="deep-purple-darken-4" class="ml-3" max-width="220" style="font-size: 12px">
            <v-sheet color="deep-purple-darken-4" class="mb-2 text-truncate pr-3">{{chat.user.name}}</v-sheet>
            <v-sheet
                @click="goToItem(chat.url)"
                color="deep-purple-darken-4" class="pr-3 text-truncate d-flex">
                <v-sheet color="deep-purple-darken-4" class="pr-3 text-truncate">{{chat.context}}</v-sheet>
                <v-sheet color="deep-purple-darken-4" class="pr-3">{{chat.price}}</v-sheet>
            </v-sheet>
        </v-sheet>

        <v-spacer />

        <v-menu>
            <template v-slot:activator="{ props }">
                <v-icon
                    v-bind="props"
                    icon="mdi-menu"
                >
                </v-icon>
            </template>
            <v-list max-width="390">
                <v-list-item
                    v-for="(conversation, index) in conversations"
                    :key="index"

                >
                    <v-list-item-title
                        @click.self="() => router.visit(`/accounts/${activeAccount.id}/chats/${conversation.chat_id}`)"
                    >{{ conversation.avito_item_name }}</v-list-item-title>
                    <template v-slot:prepend>
                        <v-icon @click.stop="clear(index, conversation)" color="red" icon="mdi-trash-can-outline" />
                    </template>
                </v-list-item>
            </v-list>
        </v-menu>
    </v-sheet>
</template>

<style scoped>

</style>
