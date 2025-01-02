<script setup>
import BotScheduleSlotEdit from "@/Components/Bots/BotScheduleSlotEdit.vue";
import {ref} from "vue";
import {router} from "@inertiajs/vue3";

const props = defineProps({
    botId: {
        type: Number,
        required: true,
    },
    dayOfWeek: {
        type: String,
        required: true
    },
    schedule: {
        required: true
    }
})

const selectedSlot = ref(null)

const onDelete = (slot) => {
    router.delete(
        route('bots.schedules.slots.update', {bot: props.botId, dayOfWeek: props.dayOfWeek, slot}),
    )
}
</script>

<template>
    <div v-for="slot in (schedule?.slots || [])">
        <bot-schedule-slot-edit
            v-if="selectedSlot && selectedSlot.id === slot.id"
            :slot="selectedSlot"
            :day-of-week="dayOfWeek"
            :bot-id="botId"
            @cancel="() => selectedSlot = null"
            @saved="() => selectedSlot = null"
        />

        <v-sheet
            v-else
            border
            rounded
            hover
            class="mb-1 d-flex align-center px-3 py-2 cursor-pointer"
        >
            <div @click="() => selectedSlot = slot"
                 class="w-100">{{ slot.start_time }} - {{ slot.end_time }}
            </div>

            <v-icon icon="mdi-trash-can-outline"
                    class="mr-2"
                    color="error"
                    @click="() => onDelete(slot.id)"/>

            <v-icon icon="mdi-square-edit-outline"
                    @click="() => selectedSlot = slot"/>
        </v-sheet>
    </div>

    <bot-schedule-slot-edit
        v-if="selectedSlot && !selectedSlot?.id"
        :slot="selectedSlot"
        :day-of-week="dayOfWeek"
        :bot-id="botId"
        @cancel="() => selectedSlot = null"
        @saved="() => selectedSlot = null"
    />

    <v-btn
        v-show="!selectedSlot"
        text="добавить"
        rounded
        variant="outlined"
        @click="() => selectedSlot = {}"
        color="primary"
        size="small"
        class="mt-5"
    />
</template>
