<script setup>
import AuthLayout from "@/Layouts/AuthLayout.vue";
import {Head, useForm, Link} from "@inertiajs/vue3";
import {ref} from "vue";

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

    <form @submit.prevent="send" class="px-5 px-sm-0 h-screen d-flex align-center full-width">
            <v-card
                class="mx-auto pa-8 pb-5 pa-sm-12 pb-sm-12"
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
                    class="mb-8"
                    color="blue"
                    size="large"
                    variant="tonal"
                    block
                >
                    Зарегистрироваться
                </v-btn>

                <v-card-text class="text-center">
                    <Link
                        class="text-blue text-decoration-none"
                        :href="route('login')"
                        rel="noopener noreferrer"
                        target="_blank"
                    >
                        Авторизироваться
                        <v-icon icon="mdi-chevron-right"></v-icon>
                    </Link>
                </v-card-text>
            </v-card>
        </form>
</template>
