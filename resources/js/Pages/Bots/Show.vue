<script setup>
import PageTitle from "@/Components/PageTitle.vue";
import {ref} from "vue";
import BotEditModal from "@/Pages/Bots/Modals/BotEditModal.vue";
import BotShowStandard from "@/Pages/Bots/Show/BotShowStandard.vue";
import BotShowQuiz from "@/Pages/Bots/Show/BotShowQuiz.vue";
import ChangeBotType from "@/Pages/Bots/Show/ChangeBotType.vue";
import {BOT_TYPE_QUIZ, BOT_TYPE_STANDARD} from "@/Constants/BotTypes.js";

defineProps(['bot'])
defineOptions({inheritAttrs: false})

const mainBotEditModal = ref(false)
</script>

<template>
    <page-title
        :text="bot.name"
        :back-url="route('bots.index')"
    >
        <template v-slot:append>
            <v-spacer></v-spacer>

            <v-btn color="primary" @click="mainBotEditModal = true" size="small" icon="mdi-pencil"/>
        </template>
    </page-title>

    <change-bot-type :bot="bot" />

    <bot-show-standard v-if="bot.type === BOT_TYPE_STANDARD" :bot="bot" />

    <bot-show-quiz v-if="bot.type === BOT_TYPE_QUIZ" :bot="bot" />

    <bot-edit-modal
        :selected="bot"
        v-model="mainBotEditModal"
    />
</template>
