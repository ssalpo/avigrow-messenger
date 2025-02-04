<script setup>
import AuthLayout from "@/Layouts/AuthLayout.vue";
import {Head, useForm, Link} from "@inertiajs/vue3";
import {ref} from "vue";
import logo from "../../../images/logo.svg";

defineOptions({layout: AuthLayout})

const visible = ref(false)

const form = useForm({
    company: null,
    name: null,
    email: null,
    password: null
})

const send = () => {
    form.post(route('register'))
}
</script>

<template>
    <Head title="Регистрация"/>

    <div class="h-screen d-flex flex-column justify-center overflow-y-auto">
        <img :src="logo" style="width: 200px" class="py-5 mx-auto" alt="logo" />
        <form @submit.prevent="send" class="px-5 px-sm-0 d-flex align-center full-width">
            <v-card
                class="mx-auto pa-8 pa-sm-10 pb-5 pb-sm-8"
                elevation="8"
                width="450"
                rounded="lg"
            >
                <div class="text-subtitle-1 text-medium-emphasis">Название компании</div>

                <v-text-field
                    density="compact"
                    v-model="form.company"
                    :error-messages="form.errors.company"
                    placeholder="Введите название компании"
                    prepend-inner-icon="mdi-domain"
                    variant="outlined"
                ></v-text-field>

                <div class="text-subtitle-1 text-medium-emphasis">Имя</div>

                <v-text-field
                    density="compact"
                    v-model="form.name"
                    :error-messages="form.errors.name"
                    placeholder="Введите имя"
                    prepend-inner-icon="mdi-domain"
                    variant="outlined"
                ></v-text-field>

                <div class="text-subtitle-1 text-medium-emphasis">Email</div>

                <v-text-field
                    v-model="form.email"
                    :error-messages="form.errors.email"
                    density="compact"
                    placeholder="Введите ваш email"
                    prepend-inner-icon="mdi-email-outline"
                    variant="outlined"
                ></v-text-field>

                <div class="text-subtitle-1 text-medium-emphasis d-flex align-center justify-space-between">
                    Пароль
                </div>

                <v-text-field
                    v-model="form.password"
                    :error-messages="form.errors.password"
                    :append-inner-icon="visible ? 'mdi-eye-off' : 'mdi-eye'"
                    :type="visible ? 'text' : 'password'"
                    density="compact"
                    placeholder="Введите пароль"
                    prepend-inner-icon="mdi-lock-outline"
                    variant="outlined"
                    :class="{'mb-5': form.errors.password !== undefined}"
                    @click:append-inner="visible = !visible"
                ></v-text-field>

                <v-btn
                    type="submit"
                    class="mb-5"
                    color="blue"
                    size="large"
                    variant="tonal"
                    block
                >
                    Зарегистрироваться
                </v-btn>

                <v-card-text class="d-flex align-center justify-center">
                    <div class="me-5">У вас уже есть аккаунт?</div>
                    <Link
                        class="text-blue text-decoration-none"
                        :href="route('login')"
                        rel="noopener noreferrer"
                        target="_blank"
                    >
                        Войти
                        <v-icon icon="mdi-chevron-right"></v-icon>
                    </Link>
                </v-card-text>
            </v-card>
        </form>
    </div>
</template>
