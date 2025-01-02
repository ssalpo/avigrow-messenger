<script setup>
    import BotScheduleSlots from "@/Components/Bots/BotScheduleSlots.vue";

    defineProps({
        botId: {
            type: Number,
            required: true,
        },
        weekTitle: {
            type: String,
            required: true
        },
        weekKey: {
            type: String,
            required: true
        },
        schedule: {
            required: true
        }
    })

    const onChangeScheduleStatus = (botId, dayOfWeek) => {
        axios.post(route('bots.schedules.toggle-status', {bot: botId, dayOfWeek }))
    }
</script>

<template>
    <v-card color="#fafafa">
        <v-card-title class="text-subtitle-2 d-flex align-center" style="height: 58px">
            <span>{{ weekTitle }}</span>
            <v-spacer/>
            <v-switch
                @click="() => onChangeScheduleStatus(botId, weekKey)"
                :model-value="schedule?.is_active === true"
                hide-details color="primary"></v-switch>
        </v-card-title>
        <v-card-text>
            <bot-schedule-slots :bot-id="botId" :day-of-week="weekKey" :schedule="schedule" />
        </v-card-text>
    </v-card>
</template>
