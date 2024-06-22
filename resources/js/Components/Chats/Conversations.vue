<script setup>
import {router} from '@inertiajs/vue3'
import {computed, onMounted, ref} from "vue";
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

const openItem = (id) => {
    router.visit(route('account.chat.messages', {account: props.activeAccountId, chat: id}))
}

onMounted(() => {
    chatIds.value = map(chats.value, 'id');

    Echo.channel(`avito.new.message`)
        .listen('NewMessage', (e) => {
            if(e.account !== props.activeAccountId) {
                return
            }

            const data = e.data.chat.value;

            if (chatIds.value.includes(data.chat_id)) {
                let index = chatIds.value.indexOf(data.chat_id);

                chats.value[index]['last_message']['content'] = data.content;
                chats.value[index]['last_message']['is_read'] = data.read !== undefined;
                chats.value[index]['last_message']['created'] = data.created;

                chats.value = orderBy(chats.value, 'last_message.created', 'desc')
            } else {
                axios.get(`/api/chats/${e.account}/${data.chat_id}/info`)
                    .then((response) => {
                        unreadChats.value.concat(response.data.unreadChatIds);

                        chats.value.unshift(response.data.chat)

                        chatIds.value = map(chats.value, 'id');
                    })
            }
        });

})

</script>

<template>
    <div class="chats-wrap">
        <div class="chats">
            <div class="chats-item"
                 :class="{unread: unreadChats.includes(chat.id)}"
                 :data-some="[unreadChats, chat.id]"
                 @click="openItem(chat.id)" v-for="chat in chats">
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
