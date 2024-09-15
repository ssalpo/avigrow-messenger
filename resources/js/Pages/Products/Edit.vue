<script setup>
import Navbar from "@/Components/Navbar.vue";
import {Head, router, useForm} from "@inertiajs/vue3";
import { VNumberInput } from 'vuetify/labs/VNumberInput'

const props = defineProps(['product', 'errors'])

const form = useForm({
    id: props.product?.id,
    name: props.product?.name,
    price: props.product?.price
})

function send() {
    if(form.id) {
        form.patch(route('products.update', form.id))
        return
    }

    form.post(route('products.store'))
}
</script>

<template>
    <Head :title="form.id ? `Редактирование продукта` : `Добавление продукта`" />

    <navbar />

    <v-container>
        <v-row class="d-flex align-center mb-1">
            <v-col class="text-center" cols="2">
                <v-btn icon="mdi-arrow-left" @click="router.visit(route('products.index'))" size="small" color="primary" variant="text"></v-btn>
            </v-col>

            <v-col cols="10">
                <h3 class="text-h6">{{form.id ? `Редактирование продукта` : `Добавление продукта`}}</h3>
            </v-col>
        </v-row>

        <v-form @submit.prevent="send">
            <v-text-field
                v-model="form.name"
                label="Название"
                :error-messages="errors?.name"
                class="mb-3"
            ></v-text-field>

            <v-number-input
                v-model="form.price"
                :error-messages="errors?.price"
                controlVariant="stacked"
                label="Сумма"
                :min="1"
                class="mb-3"
            />

            <v-btn class="mt-2" type="submit" block>Сохранить</v-btn>
        </v-form>
    </v-container>

</template>
