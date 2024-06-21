<script setup>
import {Head, Link} from '@inertiajs/vue3';
import {onMounted} from "vue";

const props = defineProps({
    activeAccountId: {
        type: Number
    },
    chat: {
        type: Object
    },
    messages: {
        type: Array
    }
});

onMounted(() => {
    setTimeout(() => {
        window.scrollTo({ left: 0, top: document.body.scrollHeight, behavior: "auto" });
    }, 500)
})
</script>

<template>
    <Head title="Сообщение" />

    <div class="message-page-head">
        <div>
            <Link :href="route('account.chats', {account: activeAccountId})" class="message-page-head__back">Назад</Link>

            <div class="message-page-head__title">{{chat.user.name}}</div>
            <div class="message-page-head__ads">{{ chat.context }}</div>
        </div>
    </div>

    <div class="messages-wrap">
        <div class="messages">
            <div v-for="message in messages">
                <div class="message__item" :class="[message.is_me ? 'right' : 'left']">
                    <div class="message__text">{{message.content.text}}</div>
                    <div class="clear"></div>
                    <div class="message__time">{{message.created_at}}</div>
                </div>

                <div class="clear"></div>
            </div>
        </div>
    </div>
</template>
