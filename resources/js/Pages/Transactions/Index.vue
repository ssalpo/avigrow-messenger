<script setup>
import Navbar from "@/Components/Navbar.vue";
import {Head, router} from "@inertiajs/vue3";
import NewTransactionModal from "@/Components/NewTransactionModal.vue";

const props = defineProps(['transactions'])

</script>

<template>
    <Head title="Касса"/>

    <navbar/>

    <v-container>
        <h3 class="text-h5 mt-3 mb-4">Касса</h3>

        <new-transaction-modal />

        <v-list lines="one" class="mt-3">

            <template
                v-for="(subTransactions, date) in transactions"
            >
                <v-list-subheader :title="date" />

                <v-list-item
                    @click="() => router.visit(route('transactions.show', transaction.id))"
                    border
                    class="mb-2"
                    :class="{'text-red-accent-3': transaction.type === 2, 'text-green-darken-3': transaction.type === 1}"
                    v-for="transaction in subTransactions"
                    :key="transaction.id"
                    :title="transaction?.account?.name || (transaction.type === 1 ? 'Приход' : 'Расход')"
                    :subtitle="transaction.order_id ? `Приход по заказу #${transaction.order_id}` : transaction.comment"
                >
                    <template v-slot:append>
                        {{ transaction.amount }}
                    </template>
                </v-list-item>
            </template>
        </v-list>
    </v-container>
</template>
