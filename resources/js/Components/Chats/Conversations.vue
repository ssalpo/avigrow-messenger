<script setup>
import {router} from '@inertiajs/vue3'
import {onBeforeUnmount, onMounted, ref} from "vue";
import {map, orderBy} from "lodash";

const props = defineProps({
    currentUserId: {
        type: Number
    },
    activeAccountId: {
        type: Number,
        default: 0
    },
    unreadChatIds: {
        type: Array,
        default: []
    },
    conversations: {
        type: Array,
        default: []
    },
    hasMore: {
        type: Boolean
    }
});

const chats = ref(props.conversations)
const unreadChats = ref(props.unreadChatIds);
const chatIds = ref([]);
const hasMoreChats = ref(props.hasMore);
const currentPage = ref(1);
const isBusy = ref(false);

const openItem = (id) => {
    router.visit(route('account.chat.messages', {account: props.activeAccountId, chat: id}))
}

let newMessageChannel = null;

onMounted(() => {
    chatIds.value = map(chats.value, 'id');

    newMessageChannel = Echo.private(`avito.${props.activeAccountId}.new.message`);

    newMessageChannel.listen('NewMessage', (e) => {
        if (e.account !== props.activeAccountId) {
            return
        }

        const data = e.data.message;

        if (chatIds.value.includes(data.chat_id)) {
            e.data.unreadChatIds.forEach((e) => {
                unreadChats.value.push(e)
            })

            let index = chatIds.value.indexOf(data.chat_id);

            chats.value[index]['last_message']['content'] = data.content;
            chats.value[index]['last_message']['is_read'] = data.isRead;
            chats.value[index]['last_message']['created'] = data.created;

            chats.value = orderBy(chats.value, 'last_message.created', 'desc')
        } else {
            axios.get(`/api/chats/${e.account}/${data.chat_id}/info`)
                .then((response) => {
                    unreadChats.value.push(response.data.id);

                    chats.value.unshift(response.data)

                    chatIds.value = map(chats.value, 'id');
                })
        }
    });

    onBeforeUnmount(() => {
        newMessageChannel.stopListening('NewMessage')
    })

})

const showMorePage = () => {
    if (isBusy.value === true) {
        return;
    }

    isBusy.value = true;

    currentPage.value++;

    axios.get(`/api/chats/${props.activeAccountId}?page=${currentPage.value}`)
        .then((response) => {
            chats.value = chats.value.concat(response.data.chats)

            hasMoreChats.value = response.data.has_more

            isBusy.value = false;
        })
}

function contextType(chat) {
    let currentType;

    switch (chat.last_message.content_type) {
        case 'image':
            currentType = 'Фото'
            break;
        case 'video':
            currentType = 'Видео'
            break;
        case 'location':
            currentType = `Локация: ${chat.last_message.content.location.text}`
            break;
        case 'voice':
            currentType = 'Голосовое сообщение'
            break;
        case 'deleted':
            currentType = 'Сообщение удалено'
            break;
        case 'item':
            currentType = 'Объявление'
            break;
        case 'link':
            currentType = 'Ссылка'
            break;
        case 'file':
            currentType = 'Файл'
            break;
        default:
            currentType = chat.last_message.content.text
    }

    return currentType
}

</script>

<template>
    <v-list lines="two" class="pt-0">
        <template
            v-for="chat in chats"
            :key="chat.id"
        >
            <v-list-item
                @click="openItem(chat.id)"
                :title="chat.user.name"
                :subtitle="contextType(chat)"
                :prepend-avatar="chat.image"
            >
                <template v-slot:title="{title}">
                    <div class="v-list-item-title" :class="{'text-red-darken-1': unreadChats.includes(chat.id)}">{{ title }}</div>
                    <small :class="{'text-red-darken-1': unreadChats.includes(chat.id)}">{{ chat.context }}</small>
                </template>

                <template v-slot:subtitle="{subtitle}">
                    <small>{{ subtitle }}</small>
                </template>

                <template v-slot:prepend>
                    <v-avatar :image="chat.image" size="45"></v-avatar>
                </template>
            </v-list-item>
            <v-divider />
        </template>
    </v-list>

    <div class="text-center mt-3 mb-5">
        <v-btn variant="outlined" size="x-small" :disabled="isBusy" color="blue-darken-1" v-if="hasMoreChats" @click.prevent="showMorePage">
            Показать еще
        </v-btn>
    </div>
</template>
