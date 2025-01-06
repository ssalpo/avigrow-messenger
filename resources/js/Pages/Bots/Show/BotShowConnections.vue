<script setup>

import BotShowAccountConnection from "@/Pages/Bots/Show/BotShowAccountConnection.vue";
import BotShowAdConnection from "@/Pages/Bots/Show/BotShowAdConnection.vue";
import BotShowSchedules from "@/Pages/Bots/Show/BotShowSchedules.vue";
import {ref} from "vue";
import BotShowSettings from "@/Pages/Bots/Show/BotShowSettings.vue";

const props = defineProps({
    bot: {
        type: Object,
        required: true
    },
    accounts: {
        type: Array,
        required: true
    },
})

const panel = ref([])

</script>

<template>
    <v-expansion-panels v-model="panel" class="mt-10">
        <v-expansion-panel class="mb-2" title="Расписание">
            <v-expansion-panel-text>
                <bot-show-schedules :bot="bot" />
            </v-expansion-panel-text>
        </v-expansion-panel>
        <v-expansion-panel class="mb-2" title="Прикрепленные аккаунты">
            <v-expansion-panel-text>
                <bot-show-account-connection :accounts="accounts" :bot="bot" />
            </v-expansion-panel-text>
        </v-expansion-panel>

        <v-expansion-panel class="mb-2" title="Прикрепленные объявления">
            <v-expansion-panel-text>
                <bot-show-ad-connection :bot="bot" />
            </v-expansion-panel-text>
        </v-expansion-panel>

        <v-expansion-panel class="mb-2" title="Дополнительные настройки">
            <v-expansion-panel-text>
                <bot-show-settings :bot="bot" />
            </v-expansion-panel-text>
        </v-expansion-panel>
    </v-expansion-panels>
</template>
