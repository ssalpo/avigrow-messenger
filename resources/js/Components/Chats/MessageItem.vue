<script setup>
const props = defineProps({
    message: {
        type: Object
    }
});
</script>

<template>
    <div class="message__item" :class="[message.is_me ? 'right' : 'left', message.content_type]">
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
                <b>Пользователь отправил локацию (не поддерживается)</b>
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

            <div v-else>
                {{ message.content.text }}
            </div>
        </div>
        <div class="clear"></div>
        <div class="message__time">{{ message.created_at }}</div>
    </div>
</template>

<style scoped>

</style>
