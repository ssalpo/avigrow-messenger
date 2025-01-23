<script setup>
import {useForm} from "@inertiajs/vue3";
import PageTitle from "@/Components/PageTitle.vue";
import {ACCOUNT_TYPE_FREE, ACCOUNT_TYPE_PRO} from "@/Constants/AccountType.js";

const props = defineProps(['account', 'errors'])

const form = useForm({
    id: props.account?.id,
    name: props.account?.name,
    type: props.account?.type ?? 1,
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

    <v-sheet class="mx-auto"
             max-width="600">
        <v-form @submit.prevent="send">
            <v-radio-group label="Тип аккаунта"
                           v-model="form.type">

                <v-radio label="Обычный"
                         :value="0"></v-radio>

                <v-radio label="Pro"
                         :value="1"></v-radio>
            </v-radio-group>


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

            <div v-if="form.type === 1">
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
            </div>

            <v-alert v-else
                     v-if="!form?.id || (form.id && form.type === ACCOUNT_TYPE_FREE && account.type === ACCOUNT_TYPE_PRO)"
                     class="mb-5 text-body-2"
                     type="success"
                     variant="outlined">
                После нажатия кнопки "{{ form.id ? "Изменить" : "Добавить" }}" вы будете перенаправлены на платформу
                Авито для предоставления необходимого доступа.
            </v-alert>

            <v-sheet class="text-right">
                <v-btn class="mt-2"
                       width="200"
                       color="blue-darken-1"
                       type="submit">
                    {{ form.id ? `Изменить` : `Добавить` }}
                </v-btn>
            </v-sheet>
        </v-form>
    </v-sheet>
</template>
