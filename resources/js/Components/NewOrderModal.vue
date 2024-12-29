<script setup>
import { VNumberInput } from 'vuetify/labs/VNumberInput'
import {onMounted, ref} from "vue"
import {useForm} from "@inertiajs/vue3"

const props = defineProps(['errors', 'chatId', 'accountId'])

const products = ref([])
const form = useForm({
    product_id: null,
    price: null,
    comment: null,
    chat_id: props.chatId
})

onMounted(() => {
    axios.get(route('autocomplete.products'))
        .then(response => {
            products.value = response.data
        })
})

function send(dialog) {
    form.post(route('orders.store', {account: props.accountId}), {
        preserveState: true,
        onSuccess: () => {
            form.reset()
            dialog.value = false
        }
    })
}
</script>

<template>
    <v-dialog max-width="600" v-bind="$attrs">
        <template v-slot:activator="{ props }">
            <slot :props="props">
                <button v-bind="props" class="left-btn message-icon" type="button">🛒</button>
            </slot>
        </template>

        <template v-slot:default="{ isActive }">
            <v-card
                prepend-icon="mdi-account"
                title="Новый заказ"
            >
                <v-card-text>
                    <v-select
                        v-model="form.product_id"
                        :items="products"
                        item-title="name"
                        item-value="id"
                        label="Выберите продукт"
                        :error-messages="errors?.product_id"
                        class="mb-3"
                    />

                    <v-number-input
                        v-model="form.price"
                        controlVariant="stacked"
                        label="Сумма"
                        :min="1"
                        :error-messages="errors?.price"
                        class="mb-3"
                    />

                    <v-text-field
                        v-model="form.comment"
                        label="Комментарий"
                        :error-messages="errors?.comment"
                    />
                </v-card-text>

                <v-divider></v-divider>

                <v-card-actions>
                    <v-spacer></v-spacer>

                    <v-btn
                        text="Закрыть"
                        variant="plain"
                        @click="isActive.value = false"
                    ></v-btn>

                    <v-btn
                        color="blue-darken-1"
                        text="Сохранить"
                        variant="tonal"
                        @click="send(isActive)"
                    ></v-btn>
                </v-card-actions>
            </v-card>
        </template>
    </v-dialog>
</template>
