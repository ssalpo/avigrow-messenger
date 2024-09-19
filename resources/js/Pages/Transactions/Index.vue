<script setup>
import Navbar from "@/Components/Navbar.vue";
import {Head, router} from "@inertiajs/vue3";
import NewTransactionModal from "@/Components/NewTransactionModal.vue";

const props = defineProps(['transactions', 'totalDebits', 'totalCredits'])

</script>

<template>
    <Head title="Касса"/>

    <navbar/>

    <v-container>
        <h3 class="text-h5 mt-3 mb-4">Касса</h3>

        <new-transaction-modal />

        <v-row class="mt-7 mx-1 mb-5">
            <v-col class="text-center border rounded  text-primary">
                <b class="pb-2 d-block">{{totalDebits - totalCredits}}</b>
                <small>Остаток</small>
            </v-col>
            <v-col class="text-center border rounded mx-2 text-success">
                <b class="pb-2 d-block">{{totalDebits}}</b>
                <small>Доход</small>
            </v-col>
            <v-col class="text-center border rounded text-error">
                <b class="pb-2 d-block">{{totalCredits}}</b>
                <small>Расходы</small>
            </v-col>
        </v-row>

        <v-btn size="x-small"
               @click="() => router.visit(route('transactions.statistics'))"
               variant="text" color="primary" prepend-icon="mdi-arrow-right">Еще статистика</v-btn>

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
