<script setup>
import {Head, useForm, Link, usePage} from '@inertiajs/vue3';
import AuthLayout from "@/Layouts/AuthLayout.vue";
import {ref} from "vue";

defineOptions({layout: AuthLayout})

const visible = ref(false)

const form = useForm({
    email: null,
    password: null
})

function auth() {
    form.post(route('login'))
}
</script>

<template>
    <Head title="Авторизация"/>

    <div class="mt-15 px-5 px-sm-0">
        <v-card
            class="mx-auto pa-5 pa-sm-12 pb-8"
            elevation="8"
            max-width="448"
            rounded="lg"
        >
            <div class="text-subtitle-1 text-medium-emphasis">Email</div>

            <v-text-field
                v-model="form.email"
                :error-messages="form.errors.email"
                density="compact"
                placeholder="Введите email"
                prepend-inner-icon="mdi-email-outline"
                variant="outlined"
            ></v-text-field>

            <div class="text-subtitle-1 text-medium-emphasis d-flex align-center justify-space-between">
                Пароль

                <a
                    class="text-caption text-decoration-none text-blue"
                    href="#"
                    rel="noopener noreferrer"
                    target="_blank"
                >
                    Забыли пароль?
                </a>
            </div>

            <v-text-field
                :error-messages="form.errors.password"
                :append-inner-icon="visible ? 'mdi-eye-off' : 'mdi-eye'"
                :type="visible ? 'text' : 'password'"
                density="compact"
                placeholder="Введите пароль"
                prepend-inner-icon="mdi-lock-outline"
                variant="outlined"
                v-model="form.password"
                class="mb-2"
                @click:append-inner="visible = !visible"
            ></v-text-field>

            <v-btn
                type="submit"
                class="mb-8"
                color="blue"
                size="large"
                variant="tonal"
                block
                @click="auth"
            >
                Войти
            </v-btn>

            <v-card-text class="text-center">
                <Link
                    class="text-blue text-decoration-none"
                    :href="route('signup')"
                    rel="noopener noreferrer"
                    target="_blank"
                >
                    Регистрация
                    <v-icon icon="mdi-chevron-right"></v-icon>
                </Link>
            </v-card-text>
        </v-card>
    </div>

</template>
