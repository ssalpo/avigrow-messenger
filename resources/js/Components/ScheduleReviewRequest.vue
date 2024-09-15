<script setup>
import {onMounted, ref} from "vue";

    let dialog = ref(false)
    let isBusy = ref(false)
    let hideBtn = ref(false)

    const props = defineProps({
        chatId: {
            type: String,
            required: true
        },
        accountId: {
            type: Number,
            required: true
        }
    });

    function add() {
        isBusy.value = true;

        axios.post(route('schedule-reviews.store', {account: props.accountId}), {chat_id: props.chatId, account_id: props.accountId})
            .then(() => {
                dialog.value = false
                hideBtn.value = true
            })
            .catch(() => alert('Ошибка добавления, обратитесь к администратору!'))
            .finally(() => isBusy.value = false)
    }

</script>

<template>
    <v-dialog
        v-model="dialog"
        max-width="400"
        persistent
    >
        <template v-slot:activator="{ props }">
            <slot :props="props">
                <button v-bind="props" v-show="!hideBtn" class="left-btn message-icon" type="button">⏰</button>
            </slot>
        </template>

        <v-card
            prepend-icon="mdi-update"
            text="Система автоматически запросит отзыв через определенное время."
            title="Запрос отзыва"
        >
            <template v-slot:actions>
                <v-spacer></v-spacer>

                <v-btn @click="dialog = false" :disabled="isBusy">
                    Нет
                </v-btn>

                <v-btn @click="add" color="success" :disabled="isBusy">
                    Да
                </v-btn>
            </template>
        </v-card>
    </v-dialog>
</template>

<style scoped>

</style>
