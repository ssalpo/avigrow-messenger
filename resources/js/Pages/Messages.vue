<script setup>
import {Head, router, useForm, usePage} from '@inertiajs/vue3';
import {computed, onBeforeUnmount, onMounted, ref, watch} from "vue";
import MessageItem from "@/Components/Chats/MessageItem.vue";
import FastMessages from "@/Components/FastTemplates/FastMessages.vue";
import {useTextareaAutosize} from "@vueuse/core";
import ScheduleReviewRequest from "@/Components/ScheduleReviewRequest.vue";
import CodeKeysSheet from "@/Components/CodeKeysSheet.vue";
import ConversationTabs from "@/Components/ConversationTabs.vue";
import BaseLayoutWithoutNav from "@/Layouts/BaseLayoutWithoutNav.vue";
import AdBottomSheetList from "@/Components/Ads/AdBottomSheetList.vue";
import FastTemplateInline from "@/Components/FastTemplateInline.vue";

defineOptions({layout: BaseLayoutWithoutNav})

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
const fastMessagesDialog = ref(false);
const adListDialog = ref(false);
const messagesAll = ref(props.messages);
const hasMoreMessages = ref(props.has_more);
const currentPage = ref(1);
const isBusy = ref(false);
const reloadIsHide = ref(false);
const fileInput = ref(null)
const isExternalSelect = ref(false)

const imageForm = useForm({
    image: null
})

let messageAllIds = computed(() => messagesAll.value.map(m => m.id))

const showMorePage = () => {
    if (isBusy.value === true) {
        return;
    }

    isBusy.value = true;

    currentPage.value++;

    axios
        .get(route('messages.getPaginated', {account: props.activeAccount.id, chatId: props.chat.id, page: currentPage.value}))
        .then((response) => {
            messagesAll.value = response.data.messages.concat(messagesAll.value)

            hasMoreMessages.value = response.data.messages.length > 0

            isBusy.value = false;
        })
}

const scrollToEnd = () => {
    let messages = document.querySelector('.messages');
    messages.scrollTop = messages.scrollHeight;
}

const onResize = () => {
    setTimeout(scrollToEnd, 0)
}

onMounted(() => {
    setTimeout(scrollToEnd, 100)

    window.addEventListener('resize', onResize);
})

onBeforeUnmount(() => {
    window.removeEventListener('resize', onResize)
})

let newMessageChannel = null;

onMounted(() => {
    newMessageChannel = Echo.private(`avito.${props.activeAccount.id}.message`)

    newMessageChannel.listen('NewMessage', (e) => {
        const data = e.data.message;

        if (data.chat_id !== props.chat.id || messageAllIds.value.includes(data.id)) {
            return
        }

        console.log(messageAllIds, messagesAll);

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

    newMessageChannel.listenForWhisper(`typing.${props.chat.id}`, function (v) {
        sendFromOther.value = true
        sendFromOtherText.value = v
    })

    newMessageChannel.listenForWhisper(`typing-stop.${props.chat.id}`, function (v) {
        sendFromOther.value = false
        sendFromOtherText.value = null
    })
})

watch(input, () => {
    newMessageChannel.whisper(`typing.${props.chat.id}`, input.value);
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
        .post(route('messages.send', {account: props.activeAccount.id, chatId: props.chat.id}), {message: {text: input.value || text}})
        .then((response) => {
            messagesAll.value.push(response.data);

            input.value = '';

            newMessageChannel.whisper(`typing-stop.${props.chat.id}`);

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
function onBlurTextarea() {
    if (input.value) return;

    newMessageChannel.whisper(`typing-stop.${props.chat.id}`)
}

function reloadPage() {
    router.get(route('account.chat.messages', {account: props.activeAccount.id, chat: props.chat.id}))
}

const selectFile = () => {
    fileInput.value.click();
}

const uploadFile = (event) => {
    const file = event.target.files[0];

    if (!file) return;

    imageForm.image = file

    const url = route('messages.send-image', {account: props.activeAccount.id, chatId: props.chat.id})

    imageForm.post(url, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            const data = usePage().props.backData.imageSendResponse

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

            imageForm.reset();
        },
        onError: () => {
            setTimeout(() => {
                imageForm.setError('image', null)
            }, 7000)
        }
    })
}

const onTemplateSelected = (e, isExternal) => {
    input.value = e
    isExternalSelect.value = isExternal
}

</script>

<template>
    <Head title="Сообщение"/>

    <div style="display: flex; flex-direction: column; position: relative">
        <conversation-tabs
            :active-account="activeAccount"
            :chat="chat"
            style="position: fixed; width: 100%"/>

        <div class="messages">
            <div style="text-align: center">
                <a class="pagination"
                   :class="{isBusy: isBusy}"
                   v-if="hasMoreMessages"
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
            <div class="message-send-errors" v-if="imageForm.errors.image">
                {{imageForm.errors.image}}
            </div>
            <div class="message-input">
                <fast-template-inline
                    :is-external-select="isExternalSelect"
                    @selected="(e) => onTemplateSelected(e, true)"
                    v-model="input" :account-id="activeAccount.id">
                    <div class="main-input">
                        <textarea
                            :value="input"
                            @keyup="() => isExternalSelect = false"
                            ref="textarea"
                            @blur="onBlurTextarea"
                            :disabled="isBusy || sendFromOther"
                            @keydown.meta.enter="sendMessage"
                            @input="event => input = event.target.value"
                            :placeholder="sendFromOtherText || `Cообщение`">
                    </textarea>


                        <v-menu v-if="!input">
                            <template v-slot:activator="{ props }">
                                <button class="left-btn message-icon"
                                        type="button"
                                        v-bind="props">⋮
                                </button>
                            </template>

                            <v-list density="compact">
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

                                <code-keys-sheet :tabs="tabs"
                                                 :keys="keys"
                                                 @selected="onCodeKeysSelect">
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

                        <input
                            ref="fileInput"
                            type="file"
                            @change="uploadFile"
                            style="display: none;"
                        />

                        <v-menu v-if="!input">
                            <template v-slot:activator="{ props }">
                                <button class="left-btn message-icon"
                                        type="button"
                                        v-bind="props">📎
                                </button>
                            </template>

                            <v-list density="compact">
                                <v-list-item
                                    @click="() => adListDialog = true"
                                    prepend-icon="mdi-advertisements"
                                    title="Список объявлений"
                                />

                                <v-list-item
                                    :disabled="isBusy"
                                    @click="selectFile"
                                    prepend-icon="mdi-image"
                                    title="Прикрепить фото"
                                />
                            </v-list>
                        </v-menu>

                        <button v-show="!input && !reloadIsHide"
                                :disabled="isBusy"
                                @click="reloadPage"
                                class="left-btn message-icon"
                                type="button">🔄
                        </button>

                        <button @click="() => fastMessagesDialog = true"
                                class="left-btn message-icon"
                                type="button">
                            📝
                        </button>

                        <button :disabled="isBusy"
                                type="button"
                                @click="sendMessage"> ➤
                        </button>
                    </div>
                </fast-template-inline>
            </div>
        </div>
    </div>

    <fast-messages
        :activeAccount="activeAccount"
        v-model="fastMessagesDialog"
        @sendFastly="(text) => sendMessage(text)"
        @selected="(e) => onTemplateSelected(e, true)"
    />

    <ad-bottom-sheet-list
        @selected="(e) => onTemplateSelected(e, true)"
        :account-id="activeAccount.id"
                          v-model="adListDialog"/>
</template>
