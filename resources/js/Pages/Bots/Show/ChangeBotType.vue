<script setup>
import {router} from "@inertiajs/vue3";
import {BOT_TYPES} from "@/Constants/BotTypes.js";

const props = defineProps({
    bot: {
        type: Object,
        required: true
    }
})

const types = Object.keys(BOT_TYPES).map(key => ({ key, value: BOT_TYPES[key] }));

const onChangeType = (type) => {
    router.post(route('bots.change-type', {bot: props.bot.id, type}))
}

</script>

<template>
    <v-select
        variant="solo-filled"
        label="Режим бота"
        :items="types"
        :model-value="bot.type.toString()"
        item-title="value"
        item-value="key"
        @update:modelValue="onChangeType"
    ></v-select>
</template>
