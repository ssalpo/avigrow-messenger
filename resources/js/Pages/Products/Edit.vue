<script setup>
import Navbar from "@/Components/Menu/Navbar.vue";
import {Head, router, useForm} from "@inertiajs/vue3";
import {VNumberInput} from 'vuetify/labs/VNumberInput'
import PageTitle from "@/Components/PageTitle.vue";

const props = defineProps(['product', 'errors'])

const form = useForm({
    id: props.product?.id,
    name: props.product?.name,
    price: props.product?.price
})

function send() {
    if (form.id) {
        form.patch(route('products.update', form.id))
        return
    }

    form.post(route('products.store'))
}
</script>

<template>
    <page-title
        :back-url="route('products.index')"
        :text="form.id ? `Редактирование продукта` : `Добавление продукта`"
    />

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
</template>
