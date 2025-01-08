<script setup>
import {useForm} from "@inertiajs/vue3";

const props = defineProps({
    account: {
        type: Object,
        required: true
    }
})

const form = useForm({
    telegram_chat_id: props?.account?.telegram_chat_id
})

const save = () => {
    form.post(route('accounts.save-settings', props.account.id))
}
</script>

<template>
    <v-form @submit.prevent="save" class="mt-3">
        <v-text-field
            variant="outlined"
            density="comfortable"
            :error-messages="form.errors.telegram_chat_id"
            v-model="form.telegram_chat_id"
            label="ID группы или канала в телеграм"
        ></v-text-field>

        <v-btn
            color="primary"
            class="mt-2"
            type="submit"
        >
            Сохранить
        </v-btn>
    </v-form>
</template>
