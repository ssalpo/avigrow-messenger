<script setup>
import {Head, Link, router} from '@inertiajs/vue3';
import {computed, onBeforeUnmount, onMounted, ref, watch} from "vue";
import MessageItem from "@/Components/Chats/MessageItem.vue";
import FastMessages from "@/Components/FastMessages.vue";
import {useTextareaAutosize} from "@vueuse/core";
import ScheduleReviewRequest from "@/Components/ScheduleReviewRequest.vue";
import CodeKeysSheet from "@/Components/CodeKeysSheet.vue";
import NewOrderModal from "@/Components/NewOrderModal.vue";

const props = defineProps({
    errors: Object,
    tabs: Object,
    keys: Object,
    activeAccount: {
        type: Object
    },
    chat: {
        type: Object
    },
    hasReviewSchedules: {
        type: Boolean
    },
    has_more: {
        type: Boolean
    },
    messages: {
        type: Array
    }
});

const {textarea, input} = useTextareaAutosize({input: ''});

const sendFromOtherText = ref('');
const sendFromOther = ref(false);
const messagesAll = ref(props.messages);
const hasMoreMessages = ref(props.has_more);
const currentPage = ref(1);
const isBusy = ref(false);
const reloadIsHide = ref(false);
let messageAllIds = computed(() => messagesAll.value.map(m => m.id))

const showMorePage = () => {
    if (isBusy.value === true) {
        return;
    }

    isBusy.value = true;

    currentPage.value++;

    axios.get(`/api/messages/${props.activeAccount.id}/${props.chat.id}?page=${currentPage.value}`)
        .then((response) => {
            messagesAll.value = response.data.messages.concat(messagesAll.value)

            hasMoreMessages.value = response.data.messages.length > 0

            isBusy.value = false;
        })
}

const scrollToEnd = () => {
    window.scrollTo(0, document.body.scrollHeight);
}

onMounted(() => {
    setTimeout(scrollToEnd, 100)
})

let newMessageChannel = null;

onMounted(() => {
    newMessageChannel = Echo.private(`avito.${props.activeAccount.id}.new.message`)

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

        axios.post(route('chats.mark-as-read', {account: props.activeAccount.id, chatId: props.chat.id}))
    });

    newMessageChannel.listenForWhisper(`typing.${props.activeAccount.id}`, function (v) {
        sendFromOther.value = true
        sendFromOtherText.value = v
    })

    newMessageChannel.listenForWhisper(`typing-stop.${props.activeAccount.id}`, function (v) {
        sendFromOther.value = false
        sendFromOtherText.value = null
    })
})

watch(input, () => {
    newMessageChannel.whisper(`typing.${props.activeAccount.id}`, input.value);
})

onBeforeUnmount(() => {
    newMessageChannel.stopListening('NewMessage')
})


const sendMessage = (text) => {
    if (isBusy.value === true) {
        return;
    }

    isBusy.value = true;

    axios
        .post(`/api/messages/${props.activeAccount.id}/${props.chat.id}/send`, {message: {text: input.value || text}})
        .then((response) => {
            messagesAll.value.push(response.data);

            input.value = '';

            newMessageChannel.whisper(`typing-stop.${props.activeAccount.id}`);

            setTimeout(scrollToEnd, 50);
        })
        .finally(() => isBusy.value = false)
}

function onDeleteMessage(message) {
    messagesAll.value[messagesAll.value.indexOf(message)].content_type = 'deleted';
}


function onCodeKeysSelect(text) {
    input.value = text;
}

function onFastTemplateSelect(e) {
    input.value = e.content;
}

function onBlurTextarea() {
    if (input.value) return;

    newMessageChannel.whisper(`typing-stop.${props.activeAccount.id}`)
}

function reloadPage() {
    reloadIsHide.value = true

    router.get(route('account.chat.messages', {account: props.activeAccount.id, chat: props.chat.id}), {
        preserveState: true,
        preserveScroll: true,
        onSuccess: () => {
            setTimeout(() => {
                reloadIsHide.value = false
            }, 5000)
        }
    })
}

</script>

<template>
    <Head title="Сообщение"/>


    <div style="display: flex; flex-direction: column; position: relative">
        <v-sheet :elevation="6" color="primary" class="message-page-head">
            <v-list class="pa-1 bg-blue-darken-1">
                <v-list-item
                    :title="chat.user.name"
                >
                    <template v-slot:prepend>
                        <Link :href="route('account.chats', {account: props.activeAccount.id})" class="mr-3">
                            <v-icon color="white">mdi-arrow-left</v-icon>
                        </Link>
                    </template>

                    <template v-slot:subtitle>
                        <small>
                            <a v-if="chat?.url" :href="chat.url" target="_blank"
                               class="text-white text-decoration-none">{{ chat.context }}</a>
                            <span v-else>{{ chat.context }}</span>
                        </small>
                    </template>

                    <template v-slot:append>
                        <small class="pl-2">{{ chat.price }}</small>
                    </template>
                </v-list-item>
            </v-list>
        </v-sheet>

        <div class="messages">

            <div style="text-align: center">
                <a class="pagination" :class="{isBusy: isBusy}" v-if="hasMoreMessages"
                   @click.prevent="showMorePage">Показать еще</a>
            </div>

            <div v-for="message in messagesAll">
                <message-item
                    :message="message"
                    :accountId="props.activeAccount.id"
                    :chatId="chat.id"
                    :key="message.id"
                    @deleted="onDeleteMessage"
                />
            </div>
        </div>

        <div class="message-send-input">
            <div>
                <textarea
                    ref="textarea"
                    @blur="onBlurTextarea"
                    :disabled="isBusy || sendFromOther"
                    @keydown.meta.enter="sendMessage"
                    v-model="input"
                    :placeholder="sendFromOtherText || `Введите сообщение...`">
                </textarea>

                <!--                <button v-show="!input" :disabled="isBusy" class="left-btn message-icon" type="button">📎</button>-->

<!--                <button v-show="!input && !reloadIsHide" :disabled="isBusy" @click="reloadPage"
                        class="left-btn message-icon"
                        type="button">⋮
                </button>-->


                <v-menu v-if="!input">
                    <template v-slot:activator="{ props }">
                        <button class="left-btn message-icon" type="button" v-bind="props">⋮</button>
                    </template>

                    <v-list density="compact">
                        <new-order-modal :errors="errors" :chat-id="chat.id" :account-id="activeAccount.id">
                            <template v-slot:default="{props}">
                                <v-list-item
                                    v-bind="props"
                                    prepend-icon="mdi-cart-outline"
                                    title="Новый заказ"
                                />
                            </template>
                        </new-order-modal>

                        <schedule-review-request
                            :chat-id="chat.id"
                            :account-id="activeAccount.id"
                            v-if="!hasReviewSchedules"
                        >
                            <template v-slot:default="{props}">
                                <v-list-item
                                    v-bind="props"
                                    prepend-icon="mdi-alarm"
                                    title="Запросить отзыв позже"
                                />
                            </template>
                        </schedule-review-request>

                        <code-keys-sheet :tabs="tabs" :keys="keys" @selected="onCodeKeysSelect">
                            <template v-slot:default="{props}">
                                <v-list-item
                                    v-bind="props"
                                    prepend-icon="mdi-key-chain"
                                    title="Ключи и аккаунты"
                                />
                            </template>
                        </code-keys-sheet>
                    </v-list>
                </v-menu>

            <button v-show="!input && !reloadIsHide" :disabled="isBusy" @click="reloadPage" class="left-btn message-icon"
                        type="button">🔄
                </button>

                <fast-messages v-if="!input" class="message-icon" @sendFastly="(text) => sendMessage(text)"
                               @selected="onFastTemplateSelect"/>

                <button :disabled="isBusy" type="button" @click="sendMessage"> ➤</button>
            </div>
        </div>
    </div>
</template>
