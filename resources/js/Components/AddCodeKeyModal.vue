<script setup>
import {reactive, ref} from "vue";
import {router, useForm} from '@inertiajs/vue3'

defineProps(['categories', 'errors'])

const contentType = ref(1)
const contentCode = ref('')

let contentAccount = reactive({
    login: '',
    password: ''
})

let form = useForm({
    content: '',
    comment: '',
    product_type: 1
})

function contentTypeFormatter() {
    if (contentType.value === 1) {
        form.content = contentCode.value
    }

    if (contentType.value === 2 && contentAccount.login && contentAccount.password) {
        form.content = `
Логин: ${contentAccount.login}
Пароль: ${contentAccount.password}
        `
    }
}

function send(dialog) {
    contentTypeFormatter();

    form.post(route('code-keys.store'), {
        preserveState: false,
        onSuccess: () => {
            form.reset();
            dialog.value = false

            contentCode.value = ''
            contentAccount = {
                login: '',
                password: ''
            }
        }
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

                    <v-text-field
                        v-show="contentType === 1"
                        label="Код"
                        class="mb-4"
                        v-model="contentCode"
                        :error-messages="errors?.content"
                    ></v-text-field>

                    <v-sheet v-show="contentType === 2">
                        <v-text-field
                            label="Логин"
                            :error-messages="errors?.content"
                            v-model="contentAccount.login"
                        ></v-text-field>

                        <v-text-field
                            label="Пароль"
                            :error-messages="errors?.content"
                            v-model="contentAccount.password"
                        ></v-text-field>
                    </v-sheet>

                    <v-text-field
                        label="Комментарий"
                        v-model="form.comment"
                    ></v-text-field>
                </v-card-text>

                <v-divider></v-divider>

                <v-card-actions>
                    <v-spacer></v-spacer>

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
