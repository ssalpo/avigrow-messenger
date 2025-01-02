<script setup>
import {keyBy} from "lodash";
import BotSchedule from "@/Components/Bots/BotSchedule.vue";
import {WEEK_LABELS} from "@/Constants/WeekDays.js";
import {computed} from "vue";

const props = defineProps({
    bot: {
        type: Object,
        required: true
    }
})

const groupedSchedules = computed(() => keyBy(props.bot.schedules, (s) => s.day_of_week));
const listOfDayWeeks = Object.keys(WEEK_LABELS).map(key => ({key, title: WEEK_LABELS[key]}))
</script>

<template>
    <v-row>
        <v-col cols="12" v-for="weekDay in listOfDayWeeks">
            <bot-schedule
                :schedule="groupedSchedules[weekDay.key]"
                :week-key="weekDay.key"
                :week-title="weekDay.title"
                :bot-id="bot.id"
            />
        </v-col>
    </v-row>
</template>
