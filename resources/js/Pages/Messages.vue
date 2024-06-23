<script setup>
import {Head, Link, router} from '@inertiajs/vue3';
import {computed, onBeforeUnmount, onMounted, ref} from "vue";
import {map, orderBy} from "lodash";
import MessageItem from "@/Components/Chats/MessageItem.vue";

const props = defineProps({
    activeAccountId: {
        type: Number
    },
    chat: {
        type: Object
    },
    has_more: {
        type: Boolean
    },
    messages: {
        type: Array
    }
});

const messagesAll = ref(props.messages);
const hasMoreMessages = ref(props.has_more);
const currentPage = ref(1);
const isBusy = ref(false);
let messageAllIds = computed(() => messagesAll.value.map(m => m.id))

const showMorePage = () => {
    if (isBusy.value === true) {
        return;
    }

    isBusy.value = true;

    currentPage.value++;

    axios.get(`/api/messages/${props.activeAccountId}/${props.chat.id}?page=${currentPage.value}`)
        .then((response) => {
            messagesAll.value = response.data.messages.concat(messagesAll.value)

            hasMoreMessages.value = response.data.messages.length > 0

            isBusy.value = false;
        })
}

const scrollToEnd = () => {
    let messageContainer = document.querySelector('.messages-wrap');

    messageContainer.scrollTop = messageContainer.scrollHeight;
}

onMounted(() => {
    setTimeout(scrollToEnd, 100)
})

let newMessageChannel = null;

onMounted(() => {
    newMessageChannel = Echo.channel(`avito.new.message`)

    newMessageChannel.listen('NewMessage', (e) => {
            const data = e.data.chat.value;

            if (data.chat_id !== props.chat.id || messageAllIds.value.includes(data.id)) {
                return
            }

            messagesAll.value.push({
                id: data.id,
                is_me: data.is_me,
                content_type: data.type,
                content: data.content,
                is_read: false,
                created_at: data.created_at,
                created_at_timestamp: data.created,
            })

            setTimeout(scrollToEnd, 100)

            axios.post(route('chats.mark-as-read', {account: props.activeAccountId, chatId: props.chat.id}))
        });
})

onBeforeUnmount(() => {
    newMessageChannel.stopListening('NewMessage')
})


let message = ref('');

const sendMessage = () => {
    if (isBusy.value === true) {
        return;
    }

    isBusy.value = true;

    axios
        .post(`/api/messages/${props.activeAccountId}/${props.chat.id}/send`, {message: {text: message.value}})
        .then((response) => {
            messagesAll.value.push(response.data);

            message.value = '';

            setTimeout(scrollToEnd, 50);

        })
        .finally(() => isBusy.value = false)
}


</script>

<template>
    <Head title="Сообщение"/>

    <div style="display: flex; flex-direction: column; height: 100vh;">
        <div class="message-page-head">
            <div>
                <Link :href="route('account.chats', {account: activeAccountId})" class="message-page-head__back">
                    🔙
                </Link>
            </div>
            <div>
                <div class="message-page-head__title">{{ chat.user.name }}</div>
                <div class="message-page-head__ads">{{ chat.context }} - {{ chat.price }}</div>
            </div>
        </div>

        <div class="messages-wrap">
            <div class="messages">

                <div style="text-align: center">
                    <a class="pagination" :class="{isBusy: isBusy}" v-if="hasMoreMessages"
                       @click.prevent="showMorePage">Показать еще</a>
                </div>

                <div v-for="message in messagesAll">
                    <message-item
                        :message="message"
                        :key="message.id"
                    />
                </div>
            </div>
        </div>

        <div class="message-send-input">
            <div>
                <button :disabled="isBusy" type="button">📎</button>

                <input :disabled="isBusy" type="text" v-model="message" placeholder="Введите сообщение...">

                <button :disabled="isBusy" type="button" @click="sendMessage"> ➤</button>
            </div>
        </div>
    </div>
</template>
