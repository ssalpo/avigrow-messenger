<script setup>

import {router} from "@inertiajs/vue3";
import {onMounted, ref} from "vue";

const props = defineProps({
    activeAccount: {
        type: Object
    },
    icon: {
        type: String,
        default: 'mdi-forum'
    }
})

let conversations = ref([])

onMounted(() => {
    axios.get(`/api/active-conversations`)
        .then((r) => {
            conversations.value = r.data
        })
});

function clear(index, conversation) {
    conversations.value.splice(index, 1)
    axios.delete(`/api/active-conversations/${conversation.id}`)
}

</script>

<template>
    <v-menu>
        <template v-slot:activator="{ props }">
            <v-icon
                v-bind="props"
                :icon="icon"
            />
        </template>
        <v-list max-width="390">
            <v-list-item
                v-for="(conversation, index) in conversations"
                :key="index"

            >
                <v-list-item-title
                    @click.self="() => router.visit(`/accounts/${conversation.account_id}/chats/${conversation.chat_id}`)"
                >{{ conversation.avito_item_name }}
                </v-list-item-title>
                <template v-slot:prepend>
                    <v-icon @click.stop="clear(index, conversation)" color="red" icon="mdi-trash-can-outline"/>
                </template>
            </v-list-item>
        </v-list>
    </v-menu>
</template>

<style scoped>

</style>
