<script setup>
import {router} from "@inertiajs/vue3";
import NewTransactionModal from "@/Components/NewTransactionModal.vue";
import PageTitle from "@/Components/PageTitle.vue";

defineProps(['transaction', 'errors'])
</script>

<template>
    <page-title
        :back-url="route('transactions.index')"
        text="Детали транзакции"
    />

    <v-card>
        <v-card-title
            :class="{'text-red-accent-3': transaction.type === 2, 'text-green-darken-3': transaction.type === 1}">
            {{ transaction.type === 1 ? 'Приход' : 'Расход' }} #{{ transaction.id }}
        </v-card-title>
        <v-card-text>
            <v-chip class="mb-3" variant="outlined" color="red" size="small" prepend-icon="mdi-cash">
                {{ transaction.amount }} руб.
            </v-chip>

            <div v-if="transaction.comment">
                <b>Комментарий:</b> {{ transaction.comment }}
            </div>
        </v-card-text>

        <v-card-actions v-if="!transaction.order_id">

            <new-transaction-modal :transaction="transaction" :errors="errors">
                <template v-slot:default="{toggleDialog}">
                    <v-btn icon="mdi-pencil" @click="toggleDialog" color="blue-darken-1"/>
                </template>
            </new-transaction-modal>

            <v-spacer/>
            <v-btn icon="mdi-trash-can-outline" @click="router.delete(route('transactions.destroy', transaction.id))"
                   color="red"/>
        </v-card-actions>
    </v-card>
</template>
