<script setup>
import PageTitle from "@/Components/PageTitle.vue";
import {ACCOUNT_STATUS_CONNECTED, ACCOUNT_STATUS_CONNECTION_ERROR} from "@/Constants/AccountConnectStatus.js";
import {route} from "ziggy-js";

const props = defineProps(['authConnectError', 'account']);

</script>

<template>
    <page-title text="Подключение аккаунта"/>

    <v-alert v-if="authConnectError"
             :text="authConnectError"
             type="error"></v-alert>

    <v-alert v-if="account?.connection_status === ACCOUNT_STATUS_CONNECTION_ERROR"
             text="Ошибка подключения. Обратитесь к администратору!"
             type="error"></v-alert>

    <v-alert v-if="account?.connection_status === ACCOUNT_STATUS_CONNECTED"
             text="Ошибка подключения. Обратитесь к администратору!"
             type="error"></v-alert>

    <v-alert v-if="account?.connection_status === ACCOUNT_STATUS_CONNECTED"
             text="Ваш аккаунт успешно подключен!"
             type="success">
    </v-alert>

    <v-alert
        v-if="account?.connection_status === ACCOUNT_STATUS_CONNECTED"
        type="success"
        variant="outlined"
    >
        Ваш аккаунт успешно подключен!
        <a :href="route('accounts.show', account.id)"
           class="text-decoration-none text-primary d-block mt-2 font-weight-medium align-center">
            Переключите активность, чтобы начать работу!
        </a>
    </v-alert>
</template>
