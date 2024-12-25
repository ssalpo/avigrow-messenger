<script setup>
import {useForm} from "@inertiajs/vue3";
import PageTitle from "@/Components/PageTitle.vue";

const props = defineProps(['account', 'errors'])

const form = useForm({
    id: props.account?.id,
    name: props.account?.name,
    external_client_id: props.account?.external_client_id,
    external_client_secret: props.account?.external_client_secret
})

function send() {
    if (form.id) {
        form.patch(route('accounts.update', form.id))
        return
    }

    form.post(route('accounts.store'))
}
</script>

<template>
    <page-title
        :text="form.id ? `Редактирование аккаунта` : `Новый аккаунт`"
        :back-url="form.id ? route('accounts.show', form.id) : route('accounts.index')"
    />

    <v-sheet class="mx-auto" max-width="600">
        <v-form @submit.prevent="send">
            <v-text-field
                label="Название"
                variant="outlined"
                hint="Придумайте название"
                persistent-hint
                class="mb-4"
                density="compact"
                v-model="form.name"
                :error-messages="errors?.name"
            ></v-text-field>

            <v-text-field
                label="Client ID"
                variant="outlined"
                hint="Скопируйте из личного кабинета Авито"
                persistent-hint
                class="mb-4"
                density="compact"
                v-model="form.external_client_id"
                :error-messages="errors?.external_client_id"
            ></v-text-field>

            <v-text-field
                label="Client Secret"
                variant="outlined"
                hint="Скопируйте из личного кабинета Авито"
                persistent-hint
                class="mb-4"
                density="compact"
                v-model="form.external_client_secret"
                :error-messages="errors?.external_client_secret"
            ></v-text-field>

            <v-sheet class="text-right">
                <v-btn class="mt-2" width="200" color="primary" type="submit">
                    {{ form.id ? `Изменить` : `Добавить` }}
                </v-btn>
            </v-sheet>
        </v-form>
    </v-sheet>
</template>
