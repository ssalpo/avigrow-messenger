<script setup>
import {useForm} from "@inertiajs/vue3";
import {watch} from "vue";

const model = defineModel()

const props = defineProps({
    selected: {
        type: Object
    }
})

let form = useForm({
    name: null,
    email: null,
    alreadyHasAccount: true
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

    form.post(route('employees.sync'), options)
}

watch(() => props.selected, (selected) => {
    form.name = selected?.name
    form.email = selected?.email
}, {immediate: true})

watch(() => model.value, () => {
    form.clearErrors()
})

</script>

<template>
    <v-dialog v-model="model"
              max-width="400"
              scrollable
    >
        <v-card>
            <v-toolbar height="50">
                <v-toolbar-title class="text-subtitle-1">
                    Привязка оператора
                </v-toolbar-title>

                <v-btn
                    size="small"
                    icon="mdi-close"
                    @click="model = false"
                ></v-btn>
            </v-toolbar>

            <v-card-text class="pt-5">
                <v-switch
                    color="primary"
                    v-model="form.alreadyHasAccount"
                    label="Оператор имеет аккаунт?"/>

                <v-expand-transition>
                    <v-text-field
                        v-if="!form.alreadyHasAccount"
                        density="compact"
                        variant="outlined"
                        v-model="form.name"
                        label="Имя оператора"
                        :error-messages="form.errors.name"
                        class="mb-3"
                    ></v-text-field>
                </v-expand-transition>

                <v-text-field
                    density="compact"
                    variant="outlined"
                    v-model="form.email"
                    label="Email"
                    :error-messages="form.errors.email"
                    class="mb-3"
                ></v-text-field>

            </v-card-text>

            <v-card-actions>
                <v-btn
                    @click="send"
                    prepend-icon="mdi-content-save-all-outline"
                    color="blue-darken-1"
                    variant="flat"
                    text="Сохранить"
                />
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>
