<script setup>
import {reactive, ref} from "vue";
import {router, useForm} from '@inertiajs/vue3'

defineProps(['categories'])

const contentType = ref(1)
const contentCode = ref('')

let contentAccount = reactive({
    login: '',
    password: ''
})

let form = useForm({
    content: null,
    product_type: 1
})

function contentTypeFormatter() {
    if (contentType.value === 1) {
        form.content = contentCode.value
    }

    if (contentType.value === 2) {
        form.content = `
Логин: ${contentAccount.login}
Пароль: ${contentAccount.password}
        `
    }
}

function send(dialog) {
    contentTypeFormatter();

    form.post(route('code-keys.store'), {
        onSuccess: () => {
            form.reset();
            dialog.value = false

            contentCode.value = ''
            contentAccount = {
                login: '',
                password: ''
            }
        },
    })
}
</script>

<template>
    <v-dialog max-width="600" v-bind="$attrs">
        <template v-slot:default="{ isActive }">
            <v-card
                prepend-icon="mdi-account"
                title="Новый ключ"
            >
                <v-card-text>
                    <v-radio-group v-model="contentType">
                        <v-radio label="Код" :value="1"></v-radio>
                        <v-radio label="Аккаунт" :value="2"></v-radio>
                    </v-radio-group>

                    <v-select
                        v-model="form.product_type"
                        label="Категория"
                        :items="categories"
                    ></v-select>

                    <template v-if="contentType === 1">
                        <v-text-field
                            label="Код"
                            v-model="contentCode"
                            required
                        ></v-text-field>
                    </template>

                    <template v-if="contentType === 2">
                        <v-text-field
                            label="Логин"
                            v-model="contentAccount.login"
                            required
                        ></v-text-field>

                        <v-text-field
                            label="Пароль"
                            v-model="contentAccount.password"
                            required
                        ></v-text-field>
                    </template>
                </v-card-text>

                <v-divider></v-divider>

                <v-card-actions>
                    <v-spacer></v-spacer>

                    <v-btn
                        text="Clear"
                        variant="plain"
                        @click="() => form.reset()"
                    ></v-btn>

                    <v-btn
                        :disabled="form.processing"
                        text="Закрыть"
                        variant="plain"
                        @click="isActive.value = false"
                    ></v-btn>

                    <v-btn
                        :disabled="form.processing"
                        color="primary"
                        text="Сохранить"
                        variant="tonal"
                        @click="send(isActive)"
                    ></v-btn>
                </v-card-actions>
            </v-card>
        </template>
    </v-dialog>
</template>

<style scoped>

</style>
