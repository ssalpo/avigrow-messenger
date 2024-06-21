<script setup>
import { router } from '@inertiajs/vue3'

const props = defineProps({
    activeAccountId: {
        type: Number,
        default: 0
    },
    chats: {
        type: Array
    },
    hasMore: {
        type: Boolean
    }
});

const openItem = (id) => {
    router.visit(route('account.chat.messages', {account: props.activeAccountId, chat: id}))
}
</script>

<template>
    <div class="chats-wrap">
        <div class="chats">
            <div class="chats-item" @click="openItem(chat.id)" v-for="chat in chats">
                <div>
                    <div class="chats-item__avatar">
                        <img :src="chat.image">
                    </div>
                </div>
                <div>
                    <div class="chats-item__title">{{ chat.user.name }}</div>
                    <div class="chats-item__ads">{{ chat.context }}</div>
                    <div class="chats-item__last-message">{{ chat.last_message.content.text ?? '-' }}</div>
                </div>
            </div>
        </div>
    </div>
</template>
