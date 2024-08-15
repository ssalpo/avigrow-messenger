<script setup>
import {Link} from '@inertiajs/vue3';
import {router} from '@inertiajs/vue3'
import {computed, onBeforeUnmount, onMounted, ref} from "vue";
import {filter, map, omit, orderBy, sortBy} from "lodash";

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

    newMessageChannel = Echo.channel(`avito.${this.activeAccountId}.new.message`);

    newMessageChannel.listen('NewMessage', (e) => {
        if (e.account !== props.activeAccountId) {
            return
        }

        const data = e.data.chat.value;

        if (chatIds.value.includes(data.chat_id)) {
            e.data.unreadChatIds.forEach((e) => {
                unreadChats.value.push(e)
            })

            let index = chatIds.value.indexOf(data.chat_id);

            chats.value[index]['last_message']['content'] = data.content;
            chats.value[index]['last_message']['is_read'] = data.read !== undefined;
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

function truncate(text, limit) {
    if (text.length > limit) {
        for (let i = limit; i > 0; i--){
            if(text.charAt(i) === ' ' && (text.charAt(i-1) != ','||text.charAt(i-1) != '.'||text.charAt(i-1) != ';')) {
                return text.substring(0, i) + '...';
            }
        }
        return text.substring(0, limit) + '...';
    } else {
        return text;
    }
}

</script>

<template>
    <div class="chats-wrap">
        <div class="chats">
            <div class="chats-item"
                 :class="{unread: unreadChats.includes(chat.id)}"
                 @click="openItem(chat.id)" v-for="chat in chats">
                <div>
                    <div class="chats-item__avatar">
                        <img :src="chat.image">
                    </div>
                </div>
                <div>
                    <div class="chats-item__title">{{ chat.user.name }}</div>
                    <div class="chats-item__ads">{{ chat.context }}</div>
                    <div class="chats-item__last-message">
                        <div v-if="chat.last_message.content_type === 'image'">
                            Фото
                        </div>
                        <div v-if="chat.last_message.content_type === 'video'">
                            Видео
                        </div>
                        <div v-if="chat.last_message.content_type === 'location'">
                            Локация: {{chat.last_message.content.location.text}}
                        </div>
                        <div v-if="chat.last_message.content_type === 'voice'">
                            Голосовое сообщение
                        </div>
                        <div v-if="chat.last_message.content_type === 'deleted'">
                            Сообщение удалено
                        </div>
                        <div v-if="chat.last_message.content_type === 'item'">
                            Объявление
                        </div>
                        <div v-if="chat.last_message.content_type === 'link'">
                            Ссылка
                        </div>
                        <div v-else>
                            {{ truncate(chat.last_message.content.text ?? '', 90) }}
                        </div>
                    </div>
                </div>
            </div>

            <div style="text-align: center">
                <a class="pagination" :class="{isBusy: isBusy}" v-if="hasMoreChats" @click.prevent="showMorePage">Показать
                    еще</a>
            </div>
        </div>
    </div>
</template>
