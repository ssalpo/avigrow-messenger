<script setup>
import {router, useForm} from "@inertiajs/vue3";
import {watch} from "vue";

const model = defineModel()

const props = defineProps({
    selected: {
        type: Object
    }
})

let form = useForm({
    id: null,
    name: null,
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
        form.patch(route('bots.update', form.id), options)
        return
    }

    form.post(route('bots.store'), options)
}

const onDelete = () => {
    if(!confirm('Уверены что хотите удалить?')) return;

    router.delete(route('bots.destroy', form.id), {
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
        name: selected?.name
    })
}, {immediate: true})

</script>

<template>
    <v-dialog
        v-model="model"
        transition="dialog-bottom-transition"
        fullscreen
    >
        <v-card>
            <v-toolbar height="50">
                <v-btn
                    size="small"
                    icon="mdi-close"
                    @click="model = false"
                ></v-btn>

                <v-toolbar-title class="text-subtitle-1">
                    {{form.id ? 'Редактирование' : 'Новый бот'}}
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
                    v-model="form.name"
                    label="Название"
                    :error-messages="form.errors.name"
                    class="mb-3"
                ></v-text-field>

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
