<script setup>
import {router, useForm} from "@inertiajs/vue3";
import KeywordsInput from "@/Pages/Bots/Modals/KeywordsInput.vue";
import {watch} from "vue";
import {
    QUIZ_ANSWER_TYPE_ARBITRARY,
    QUIZ_ANSWER_TYPE_OPTIONS,
    QUIZ_ANSWER_TYPES
} from "@/Constants/BotQuizAnswerTypes.js";

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

const answerTypes = Object.keys(QUIZ_ANSWER_TYPES).map(key => ({ key, value: QUIZ_ANSWER_TYPES[key] }))

let form = useForm({
    id: null,
    name: null,
    content: null,
    answer_type: `${QUIZ_ANSWER_TYPE_ARBITRARY}`,
    option_keyword: null,
    options: [],
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
        console.log(form)
        form.patch(route('bots.quizzes.update', {bot: props.botId, quiz: form.id}), options)
        return
    }

    form.post(route('bots.quizzes.store', props.botId), options)
}

const onDelete = () => {
    if(!confirm('Уверены что хотите удалить?')) return;

    router.delete(route('bots.quizzes.destroy', {bot: props.botId, quiz: form.id}), {
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
        name: selected?.name,
        content: selected?.content,
        answer_type: selected?.answer_type?.toString() || `${QUIZ_ANSWER_TYPE_ARBITRARY}`,
        option_keyword: null,
        options: selected?.options || [],
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
                    {{form.id ? 'Редактирование' : 'Новый квиз'}}
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
                <v-text-field
                    variant="outlined"
                    label="Название (только для Вас)"
                    v-model="form.name"
                    :error-messages="form.errors.name"
                />

                <v-textarea
                    variant="outlined"
                    label="Текст сообщения"
                    v-model="form.content"
                    :error-messages="form.errors.content"
                ></v-textarea>

                <v-select
                    label="Тип ответа"
                    :items="answerTypes"
                    item-title="value"
                    item-value="key"
                    variant="outlined"
                    v-model="form.answer_type"
                ></v-select>

                <keywords-input
                    v-if="form.answer_type == QUIZ_ANSWER_TYPE_OPTIONS"
                    label="Введите варианты ответов"
                    v-model="form.option_keyword"
                    v-model:keywords="form.options"
                    :error-messages="form.option_keyword || form.errors.options"
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
