<script setup>
import {ref} from "vue";

const props = defineProps({
    accountId: Number,
    chatId: String,
    message: {
        type: Object
    }
});

const emits = defineEmits(['deleted'])

const isBusy = ref(false);

function deleteMessage() {
    if (isBusy.value === true || !confirm('Вы уверены что хотите удалить?')) {
        return;
    }

    isBusy.value = true;

    axios.delete(`/api/messages/${props.accountId}/${props.chatId}/${props.message.id}`)
        .then(() => emits('deleted', props.message))
        .catch(() => alert('Ошибка при удалении сообщения!'))
}

function diffInHours(date) {
    return Math.abs((new Date).getTime() - (new Date(date)).getTime()) / 3600000
}
</script>

<template>
    <div class="message__item" :data-some="[message.id]" :class="[message.is_me ? 'right' : 'left', message.content_type]">
        <div class="message__text">
            <div v-if="message.content_type === 'image'">
                <a :href="message.content.image.sizes['640x480']" target="_blank">
                    <img class="message__image" :src="message.content.image.sizes['640x480']" />
                </a>
            </div>

            <div v-else-if="message.content_type === 'item'">
                <div class="message-ads">
                    <a :href="message.content.item.item_url" target="_blank">
                        <div>
                            <div class="message-ads__image"
                                 :style="{ backgroundImage: `url('${message.content.item.image_url}')` }"
                            ></div>
                        </div>
                        <div>
                            <div class="message-ads__price">{{message.content.item.price_string}}</div>
                            <div class="message-ads__title">{{message.content.item.title}}</div>
                        </div>
                    </a>
                </div>
            </div>

            <div v-else-if="message.content_type === 'location'">
                <b>Локация: </b> {{message.content.location.text}}
            </div>

            <div v-else-if="message.content_type === 'video'">
                <b>Пользователь отправил видео (не поддерживается)</b>
            </div>

            <div v-else-if="message.content_type === 'voice'">
                <b>Пользователь отправил голосовое сообщение (не поддерживается)</b>
            </div>

            <div v-else-if="message.content_type === 'deleted'">
                <b>Сообщение удалено</b>
            </div>

            <div v-else-if="message.content_type === 'link'">
                <a :href="message.content.link.url" target="_blank">{{message.content.link.text}}</a>
            </div>

            <div v-else>
                <div class="message__quote" v-if="message.quote !== null && message.quote?.content">
                    {{message.quote?.content.text}}
                </div>

                {{ message.content.text }}
            </div>
        </div>
        <div class="clear"></div>
        <div style="display: flex">
            <div class="message__delete-btn" v-if="message.content_type !== 'deleted' && message.is_me && diffInHours(message.created_at) < 1" @click="deleteMessage">удалить,</div>
            <div class="message__read-status" :class="{read: message.is_read}">{{message.is_read ? 'прочтен' : 'доставлено'}},</div>
            <div class="message__time">{{ message.created_at }}</div>
        </div>
    </div>
    <div class="clear"></div>
</template>

<style scoped>

</style>
