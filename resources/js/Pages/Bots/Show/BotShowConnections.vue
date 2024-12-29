<script setup>

import BotShowAccountConnection from "@/Pages/Bots/Show/BotShowAccountConnection.vue";
import BotShowAdConnection from "@/Pages/Bots/Show/BotShowAdConnection.vue";

const props = defineProps({
    bot: {
        type: Object,
        required: true
    },
    accounts: {
        type: Array,
        required: true
    }
})

</script>

<template>
    <v-expansion-panels class="mt-10" variant="inset">
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
    </v-expansion-panels>
</template>
