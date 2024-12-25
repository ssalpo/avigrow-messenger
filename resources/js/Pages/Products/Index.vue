<script setup>
import Navbar from "@/Components/Menu/Navbar.vue";
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
    <Head title="Продукты" />

    <v-container>
        <v-row class="d-flex align-center mt-3 mb-2">
            <v-col cols="9">
                <h3 class="text-h5">Продукты</h3>
            </v-col>
            <v-col class="text-right">
                <v-menu>
                    <template v-slot:activator="{ props }">
                        <v-btn icon="mdi-dots-vertical" size="md" variant="text" v-bind="props"></v-btn>
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
    </v-container>
</template>

<style scoped>

</style>
