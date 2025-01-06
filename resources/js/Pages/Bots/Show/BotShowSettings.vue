<script setup>
import {router} from "@inertiajs/vue3";

const props = defineProps({
    bot: {
        type: Object,
        required: true
    }
})

const onSave = (setting, value) => {
    const settings =  {[setting]: value}
    router.post(route('bots.update-settings', props.bot.id), {settings})
}
</script>

<template>
    <v-switch
        @click="() => onSave('mark_chat_as_unread', !bot.mark_chat_as_unread)"
        label="Оставлять чат непрочитанным, когда бот отвечает"
        color="primary"
        :model-value="bot.mark_chat_as_unread"
    ></v-switch>
</template>
