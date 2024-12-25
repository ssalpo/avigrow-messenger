<script setup>
import Navbar from "@/Components/Menu/Navbar.vue";
import {Head, router} from "@inertiajs/vue3";
import PageTitle from "@/Components/PageTitle.vue";

defineProps(['products'])

function destroy(id) {
    if (!confirm('Вы уверены что хотите удалить?')) {
        return
    }

    router.delete(route('products.destroy', id), {
        preserveState: true,
        preserveScroll: true
    })
}
</script>

<template>
    <page-title
        :back-url="route('products.index')"
        text="Корзина"
    />

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
</template>

<style scoped>

</style>
