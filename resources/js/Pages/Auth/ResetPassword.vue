<script setup>
import AuthLayout from "@/Layouts/AuthLayout.vue";
import {Head, useForm, Link} from "@inertiajs/vue3";
import {ref} from "vue";
import logo from "../../../images/logo.svg";

defineOptions({layout: AuthLayout})

const visible = ref(false)
const step = ref(1)

const form = useForm({
    email: null,
})

const resetForm = useForm({
    email: null,
    reset_password_otp: null,
    password: null
});

const sendOtp = () => {
    form.post(route('send-reset-password-otp'), {
        onSuccess: () => {
            resetForm.email = form.email
            step.value = 2
        }
    })
}

const resetPassword = () => {
    resetForm.post(route('reset-password'))
}
</script>

<template>
    <Head title="Восстановление пароля"/>

    <div class="h-screen d-flex flex-column justify-center overflow-y-auto">
        <img :src="logo"
             style="width: 200px"
             class="py-5 mx-auto"
             alt="logo"/>
        <div class="px-5 px-sm-0 d-flex align-center full-width">
            <v-card
                class="mx-auto pa-8 pa-sm-10 pb-5 pb-sm-8"
                elevation="8"
                width="450"
                rounded="lg"
            >
                <v-btn variant="text"
                       v-if="step !== 1"
                       @click="() => step = 1"
                       color="primary"
                       class="mb-2"
                       prepend-icon="mdi-arrow-left"
                >
                    Назад
                </v-btn>

                <v-window v-model="step">
                    <v-window-item :value="1">
                        <div class="text-subtitle-1 text-medium-emphasis">Email</div>

                        <v-text-field
                            v-model="form.email"
                            :error-messages="form.errors.email"
                            density="compact"
                            placeholder="Введите ваш email"
                            prepend-inner-icon="mdi-email-outline"
                            variant="outlined"
                            :class="{'mb-3': form.errors.email !== undefined}"
                        ></v-text-field>

                        <v-btn
                            @click="sendOtp"
                            class="mb-5"
                            color="blue"
                            size="large"
                            variant="tonal"
                            block
                        >
                            Получить код
                        </v-btn>
                    </v-window-item>

                    <v-window-item :value="2">
                        <div class="text-subtitle-1 text-medium-emphasis">Код подтверждения</div>

                        <v-text-field
                            v-model="resetForm.reset_password_otp"
                            :error-messages="resetForm.errors.reset_password_otp"
                            density="compact"
                            placeholder="Введите код подтверждения"
                            persistent-hint
                            prepend-inner-icon="mdi-key-variant"
                            variant="outlined"
                            :class="{'mb-3': resetForm.errors.reset_password_otp !== undefined}"
                        ></v-text-field>

                        <div class="text-subtitle-1 text-medium-emphasis">Новый пароль</div>

                        <v-text-field
                            :error-messages="resetForm.errors.password"
                            :append-inner-icon="visible ? 'mdi-eye-off' : 'mdi-eye'"
                            :type="visible ? 'text' : 'password'"
                            density="compact"
                            placeholder="Введите новый пароль"
                            prepend-inner-icon="mdi-form-textbox-password"
                            variant="outlined"
                            v-model="resetForm.password"
                            class="mb-2"
                            @click:append-inner="visible = !visible"
                        ></v-text-field>

                        <v-btn
                            @click="resetPassword"
                            class="mb-5"
                            color="blue"
                            size="large"
                            variant="tonal"
                            block
                        >
                            Восстановить
                        </v-btn>
                    </v-window-item>
                </v-window>

                <v-card-text class="d-flex align-center justify-center">
                    <div class="me-5">Вспомнили пароль?</div>
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
        </div>
    </div>
</template>
