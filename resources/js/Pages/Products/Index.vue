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
    <page-title text="Продукты">
        <template v-slot:append>
            <v-menu>
                <template v-slot:activator="{ props }">
                    <v-spacer />
                    <v-icon icon="mdi-dots-vertical" v-bind="props" />
                </template>

                <v-list density="compact">
                    <v-list-item
                        prepend-icon="mdi-plus"
                        title="Добавить"
                        @click="() => router.visit(route('products.create'))"
                    />

                    <v-list-item
                        prepend-icon="mdi-trash-can-outline"
                        title="Корзина"
                        @click="() => router.visit(route('products.trash'))"
                    />
                </v-list>
            </v-menu>
        </template>
    </page-title>

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
                <v-menu>
                    <template v-slot:activator="{ props }">
                        <v-btn icon="mdi-dots-vertical" variant="text" v-bind="props"></v-btn>
                    </template>

                    <v-list density="compact">
                        <v-list-item
                            prepend-icon="mdi-pencil"
                            title="Редактировать"
                            @click="() => router.visit(route('products.edit', product.id))"
                        />
                        <v-list-item
                            prepend-icon="mdi-trash-can-outline"
                            title="Удалить"
                            @click="() => destroy(product.id)"
                        />
                    </v-list>
                </v-menu>
            </template>
        </v-list-item>
    </v-list>
</template>
