<script setup>
import {Head, Link} from '@inertiajs/vue3';
import {onMounted, ref} from "vue";

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

const showMorePage = () => {
    if(isBusy.value === true) {
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

onMounted(() => {
    setTimeout(() => {
        window.scrollTo({ left: 0, top: document.body.scrollHeight, behavior: "auto" });
    }, 500)

    let menu = document.querySelector('.message-page-head');

    let offset = menu.offsetHeight;

    window.onscroll = function() {

        if (window.scrollY > offset) {
            menu.classList.add("sticky");
        } else if(window.scrollY + 50 < offset) {
            console.log('remove')
            menu.classList.remove("sticky");
        }
    }
})


</script>

<template>
    <Head title="Сообщение" />

    <div class="message-page-head">
        <div>
            <Link :href="route('account.chats', {account: activeAccountId})" class="message-page-head__back">Назад</Link>

            <div class="message-page-head__title">{{chat.user.name}}</div>
            <div class="message-page-head__ads">{{ chat.context }} - {{ chat.price }}</div>
        </div>
    </div>

    <div class="messages-wrap">
        <div class="messages">

            <div style="text-align: center">
                <a class="pagination" :class="{isBusy: isBusy}" v-if="hasMoreMessages" @click.prevent="showMorePage">Показать еще</a>
            </div>

            <div v-for="message in messagesAll">
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
