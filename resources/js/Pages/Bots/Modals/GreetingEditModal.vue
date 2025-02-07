<script setup>
import {router, useForm} from "@inertiajs/vue3";
import {ref, watch} from "vue";
import DurationSelect from "@/Components/Form/DurationSelect.vue";
import MacrosMenu from "@/Components/Bots/MacrosMenu.vue";

const model = defineModel()

const templateElement = ref(null)

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
    schedule_from: null,
    schedule_to: null,
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
        schedule_from: selected?.schedule_from,
        schedule_to: selected?.schedule_to,
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
                <macros-menu :element="templateElement"
                             v-model="form.template"
                             activator="#macros-activator"/>

                <div class="position-relative">
                    <v-textarea
                        ref="templateElement"
                        variant="outlined"
                        label="Текст шаблона"
                        v-model="form.template"
                        :class="{'mb-3': form.errors.template}"
                        :error-messages="form.errors.template"
                    ></v-textarea>

                    <v-icon icon="mdi-format-list-checks"
                            color="primary"
                            class="position-absolute top-0 right-0 mr-5 mt-3 cursor-pointer"
                            id="macros-activator">
                    </v-icon>
                </div>

                <div class="text-subtitle-2 mb-4">
                    Часы работы
                    <small class="d-block text-medium-emphasis">(по таймзоне прикрепленного аккаунта)</small>
                </div>

                <v-row class="mb-1">
                    <v-col cols="6"
                           lg="4">
                        <v-text-field
                            :error="form.errors.template"
                            label="Начало"
                            autocomplete="off"
                            v-mask="'##:##'"
                            placeholder="ЧЧ:ММ"
                            variant="outlined"
                            persistent-hint
                            density="compact"
                            v-model="form.schedule_from"
                        ></v-text-field>
                    </v-col>

                    <v-col cols="6"
                           lg="4">
                        <v-text-field
                            :error="form.errors.schedule_to"
                            label="Конец"
                            autocomplete="off"
                            v-mask="'##:##'"
                            placeholder="ЧЧ:ММ"
                            variant="outlined"
                            persistent-hint
                            density="compact"
                            v-model="form.schedule_to"
                        ></v-text-field>
                    </v-col>
                </v-row>

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
