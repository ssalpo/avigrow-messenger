<script setup>
import {useForm} from "@inertiajs/vue3";
import {onMounted, ref, watch} from "vue";

const emits = defineEmits(['cancel', 'saved'])

const focusInput = ref(null);

const props = defineProps({
    botId: {
        type: Number,
        required: true,
    },
    dayOfWeek: {
        type: String,
        required: true
    },
    slot: {
        type: Object,
        required: true
    }
})

let form = useForm({
    id: null,
    start_time: null,
    end_time: null,
});

const onSave = () => {
    const options = {
        onSuccess: () => {
            emits('saved')
        },
        preserveState: true,
        preserveScroll: true
    }

    if(!form.start_time && !form.end_time) {
        return
    }

    if(form.id) {
        form.patch(
            route('bots.schedules.slots.update', {bot: props.botId, dayOfWeek: props.dayOfWeek, slot: form.id}),
            options
        )
        return
    }

    form.post(
        route('bots.schedules.slots.store', {bot: props.botId, dayOfWeek: props.dayOfWeek}),
        options
    )
}

onMounted(() => {
    focusInput.value.focus();
})

watch(() => props.slot, (slot) => {
    form = useForm({
        id: slot?.id,
        start_time: slot?.start_time?.slice(0, -3),
        end_time: slot?.end_time?.slice(0, -3),
    })
}, {immediate: true})
</script>

<template>
    <v-sheet
        border
        rounded
        hover
        class="mb-1 d-flex align-center px-3 py-2 cursor-pointer editor"
    >
        <div class="w-100">
            <input type="text" @keyup.enter="onSave" ref="focusInput" autocomplete="off" v-mask="'##:##'" v-model="form.start_time" placeholder="ЧЧ:ММ" class="editor-input"/>
            -
            <input type="text" @keyup.enter="onSave" autocomplete="off" v-mask="'##:##'" v-model="form.end_time"  placeholder="ЧЧ:ММ" class="editor-input"/>
        </div>

        <v-icon
            v-if="!form.processing"
            icon="mdi-content-save-all-outline"
            color="primary"
            @click="onSave"
        />

        <v-progress-circular v-else indeterminate size="21" width="2"></v-progress-circular>
    </v-sheet>

    <div class="text-caption mb-1 d-flex align-center">
        <v-btn
            @click="() => emits('cancel')"
            height="20"
            size="x-small"
            variant="text"
            text="закрыть" prepend-icon="mdi-close-box-multiple-outline" />

        <v-spacer />
        <span class="text-hint">{{form.errors.start_time || form.errors.end_time || 'не сохранено'}}</span>
    </div>
</template>

<style scoped>
.editor-input {
    width: 52px;
    text-align: center;
    outline-color: #949494;
    border: 2px solid #ccc;
    border-radius: 5px;
}

.editor {
    border: 1px solid #ea8d8d;
}

.text-hint {
    color: #e85d5d;
}
</style>
