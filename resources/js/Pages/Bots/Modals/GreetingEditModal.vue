<script setup>
import {router, useForm} from "@inertiajs/vue3";
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
    template: null,
    delay: '0',
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
        form.patch(route('bots.greetings.update', {bot: props.botId, greeting: form.id}), options)
        return
    }

    form.post(route('bots.greetings.store', props.botId), options)
}

const onDelete = () => {
    if(!confirm('Уверены что хотите удалить?')) return;

    router.delete(route('bots.greetings.destroy', {bot: props.botId, greeting: form.id}), {
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
        template: selected?.template,
        delay: selected?.delay?.toString() || '0',
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
                    {{form.id ? 'Редактирование' : 'Новое приветствие'}}
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
                <v-textarea
                    variant="outlined"
                    label="Текст шаблона"
                    v-model="form.template"
                    :error-messages="form.errors.template"
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
                        color="primary"
                        text="Сохранить"
                    />
                </div>

            </v-card-text>
        </v-card>
    </v-dialog>
</template>
