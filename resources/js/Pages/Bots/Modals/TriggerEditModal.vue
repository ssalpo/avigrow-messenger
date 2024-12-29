<script setup>
import {router, useForm} from "@inertiajs/vue3";
import KeywordsInput from "@/Pages/Bots/Modals/KeywordsInput.vue";
import {watch} from "vue";
import DurationSelect from "@/Components/Form/DurationSelect.vue";

const model = defineModel()

const props = defineProps({
    botId: {
        type: Number,
        required: true
    },
    selected: {
        type: Object
    }
})

let form = useForm({
    id: null,
    keyword: null,
    keywords: [],
    response: null,
    delay: '0'
})

const send = () => {
    const options = {
        preserveState: true,
        preserveScroll: true,
        onSuccess: () => {
            model.value = false
            form.reset();
        }
    };

    if (form.id) {
        form.patch(route('bots.triggers.update', {bot: props.botId, trigger: form.id}), options)
        return
    }

    form.post(route('bots.triggers.store', props.botId), options)
}

const onDelete = () => {
    if(!confirm('Уверены что хотите удалить?')) return;

    router.delete(route('bots.triggers.destroy', {bot: props.botId, trigger: form.id}), {
        preserveState: true,
        preserveScroll: true,
        onSuccess: () => {
            model.value = false
            form.reset();
        }
    })
}

watch(() => props.selected, (selected) => {
    form = useForm({
        id: selected?.id,
        keyword: selected?.keyword,
        keywords: selected?.keywords || [],
        response: selected?.response,
        delay: selected?.delay?.toString() || '0'
    })
})

</script>

<template>
    <v-dialog
        v-model="model"
        transition="dialog-bottom-transition"
        fullscreen
        scrollable
    >
        <v-card>
            <v-toolbar height="50">
                <v-btn
                    size="small"
                    icon="mdi-close"
                    @click="model = false"
                ></v-btn>

                <v-toolbar-title class="text-subtitle-1">
                    {{form.id ? 'Редактирование' : 'Новый триггер'}}
                </v-toolbar-title>

                <v-toolbar-items class="pr-2">
                    <v-spacer />
                    <v-btn
                        type="submit"
                        color="error"
                        icon="mdi-trash-can-outline"
                        variant="text"
                        v-if="form.id"
                        @click="onDelete"
                    />
                </v-toolbar-items>
            </v-toolbar>

            <v-card-text class="pt-10">
                <keywords-input
                    label="Название"
                    v-model="form.keyword"
                    v-model:keywords="form.keywords"
                    :error-messages="form.errors.keyword || form.errors.keywords"
                />

                <v-textarea
                    variant="outlined"
                    label="Текст сообщения"
                    v-model="form.response"
                    :error-messages="form.errors.response"
                ></v-textarea>

                <duration-select
                    variant="outlined"
                    label="Задержка"
                    v-model="form.delay"
                />

                <div class="text-right">
                    <v-btn
                        @click="send"
                        prepend-icon="mdi-content-save-all-outline"
                        color="blue-darken-1"
                        text="Сохранить"
                    />
                </div>

            </v-card-text>
        </v-card>
    </v-dialog>
</template>
