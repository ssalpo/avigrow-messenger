<script setup>

import Navbar from "@/Components/Navbar.vue";
import {Head, router, usePage} from "@inertiajs/vue3";

defineProps(['orders'])

const page = usePage()

function cancel(id) {
    router.post(route('orders.cancel', {account: page.props.activeAccount.id, order: id}), {},{
        preserveState: true,
        preserveScroll: true
    })
}
</script>

<template>
    <Head title="Заказы"/>

    <navbar/>

    <v-container>
        <v-card
            :color="order.is_cancel ? 'red-lighten-4' : ''"
            class="mb-3"
            v-for="order in orders"
            :key="order.id"
            :title="`Заказ #${order.id}`"
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

            <v-divider v-if="!order.is_cancel" />
            <v-card-actions v-if="!order.is_cancel">
                <v-spacer/>
                <v-btn size="x-small" color="red" text="Отменить" @click="() => cancel(order.id)"></v-btn>
            </v-card-actions>
        </v-card>
    </v-container>
</template>
