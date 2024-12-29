<script setup>
import {useForm} from "@inertiajs/vue3";
import PageTitle from "@/Components/PageTitle.vue";

const props = defineProps(['bot', 'errors'])

const form = useForm({
    id: props.bot?.id,
    name: props.bot?.name
})

function send() {
    if (form.id) {
        form.patch(route('bots.update', form.id))
        return
    }

    form.post(route('bots.store'))
}
</script>

<template>
    <page-title
        :back-url="route('bots.index')"
        :text="form.id ? `Редактирование бота` : `Добавление бота`"
    />

    <v-form @submit.prevent="send">
        <v-text-field
            v-model="form.name"
            label="Название"
            :error-messages="errors?.name"
            class="mb-3"
        ></v-text-field>

        <v-sheet class="d-flex align-center">
            <v-btn v-if="form.id" size="small" color="error" icon="mdi-delete-outline"></v-btn>

            <v-spacer />

            <v-btn color="blue-darken-1" class="mt-2" type="submit">Сохранить</v-btn>
        </v-sheet>

    </v-form>
</template>
