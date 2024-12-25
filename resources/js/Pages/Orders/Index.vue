<script setup>

import Navbar from "@/Components/Menu/Navbar.vue";
import {Head, router, usePage} from "@inertiajs/vue3";

defineProps(['orders', 'totalSum', 'totalSumCanceled'])

const page = usePage()

function cancel(id) {
    router.post(route('orders.cancel', {account: page.props.activeAccount.id, order: id}), {}, {
        preserveState: true,
        preserveScroll: true
    })
}
</script>

<template>
    <Head title="Заказы"/>

    <v-row class="mt-2 mx-1 mb-7">
        <v-col class="text-center border rounded mx-2 text-success">
            <v-row class="d-flex align-center">
                <v-col>
                    <b class="pb-1 d-block">{{ totalSum }}</b>
                    <small>Купленных</small>
                </v-col>
                <v-col>
                    <v-icon icon="mdi-cart-check"/>
                </v-col>
            </v-row>
        </v-col>
        <v-col class="text-center border rounded text-error">
            <v-row class="d-flex align-center">
                <v-col>
                    <b class="pb-1 d-block">{{ totalSumCanceled }}</b>
                    <small>Отмененных</small>
                </v-col>
                <v-col>
                    <v-icon icon="mdi-cart-remove"/>
                </v-col>
            </v-row>
        </v-col>
    </v-row>

    <h3 class="text-h5 mt-3 mb-4">Заказы</h3>

    <v-card
        :color="order.is_cancel ? 'red-lighten-4' : ''"
        class="mb-3 mt-5"
        v-for="order in orders"
        :prepend-icon="!order.is_cancel ? `mdi-cart-check` : `mdi-cart-remove`"
        :key="order.id"
        :title="`#${order.id}`"
        :subtitle="order.created_at_formatted"
    >
        <v-card-text>
            <div class="d-flex ga-2">
                <v-chip variant="outlined" color="red" size="small" prepend-icon="mdi-cash">
                    {{ order.price }} руб.
                </v-chip>

                <v-chip variant="outlined" color="green" size="small" prepend-icon="mdi-cart-check">
                    {{ order.product.name }}
                </v-chip>
            </div>
        </v-card-text>

        <v-divider v-if="!order.is_cancel"/>
        <v-card-actions v-if="!order.is_cancel">
            <v-spacer/>
            <v-btn size="x-small" color="red" text="Отменить" @click="() => cancel(order.id)"></v-btn>
        </v-card-actions>
    </v-card>

</template>
