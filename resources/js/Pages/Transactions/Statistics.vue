<script setup>
import Navbar from "@/Components/Navbar.vue";
import {Head} from "@inertiajs/vue3";

defineProps([
    'dayDebitSum',
    'weekDebitSum',
    'monthDebitSum',
    'dayCreditSum',
    'weekCreditSum',
    'monthCreditSum'
])
</script>

<template>
    <Head title="Статистика"/>

    <navbar />

    <v-container>

        <v-card class="mb-5" variant="outlined" color="success">
            <v-card-title>
                Доходы
            </v-card-title>

            <v-divider />

            <v-card-text>
                <v-row class="mt-5 mx-1 mb-5">
                    <v-col class="text-center border rounded">
                        <b class="pb-2 d-block">{{dayDebitSum}}</b>
                        <small>Сегодня</small>
                    </v-col>
                    <v-col class="text-center border rounded mx-2">
                        <b class="pb-2 d-block">{{weekDebitSum}}</b>
                        <small>Неделя</small>
                    </v-col>
                    <v-col class="text-center border rounded">
                        <b class="pb-2 d-block">{{monthDebitSum}}</b>
                        <small>Месяц</small>
                    </v-col>
                </v-row>
            </v-card-text>
        </v-card>

        <v-card variant="outlined" color="error">
            <v-card-title>
                Расходы
            </v-card-title>

            <v-divider />

            <v-card-text>
                <v-row class="mt-5 mx-1 mb-5">
                    <v-col class="text-center border rounded">
                        <b class="pb-2 d-block">{{dayCreditSum}}</b>
                        <small>Сегодня</small>
                    </v-col>
                    <v-col class="text-center border rounded mx-2">
                        <b class="pb-2 d-block">{{weekCreditSum}}</b>
                        <small>Неделя</small>
                    </v-col>
                    <v-col class="text-center border rounded">
                        <b class="pb-2 d-block">{{monthCreditSum}}</b>
                        <small>Месяц</small>
                    </v-col>
                </v-row>
            </v-card-text>
        </v-card>

    </v-container>
</template>
