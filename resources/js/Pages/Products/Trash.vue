<script setup>
import Navbar from "@/Components/Navbar.vue";
import {Head, router} from "@inertiajs/vue3";

defineProps(['products'])

function destroy(id) {
    if(!confirm('Вы уверены что хотите удалить?')) {
        return
    }

    router.delete(route('products.destroy', id), {
        preserveState: true,
        preserveScroll: true
    })
}
</script>

<template>
    <Head title="Корзина" />

    <navbar />

    <v-container>
        <v-row class="d-flex align-center mt-3 mb-5">
            <v-col class="text-center" cols="2">
                <v-btn icon="mdi-arrow-left" @click="router.visit(route('products.index'))" size="small" color="primary" variant="text"></v-btn>
            </v-col>

            <v-col cols="10">
                <h3 class="text-h6">Корзина</h3>
            </v-col>
        </v-row>

        <v-list lines="one">
            <v-list-item
                border
                class="mb-2"
                v-for="product in products"
                :key="product.id"
                :title="product.name"
                :subtitle="`Цена: ${product.price}`"
            >
                <template v-slot:append>
                    <v-btn
                        icon="mdi-delete-restore"
                        color="success"
                        variant="text"
                        @click="() => router.post(route('products.restore', product.id), {preserveScroll: true, preserveState: true})"
                    ></v-btn>
                </template>
            </v-list-item>
        </v-list>
    </v-container>
</template>

<style scoped>

</style>
