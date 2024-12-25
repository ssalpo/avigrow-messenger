<script setup>
import Navbar from "@/Components/Navbar.vue";
import {Head, Link} from "@inertiajs/vue3";

defineProps(['accounts'])
</script>

<template>
    <Head title="Аккаунты"/>

    <navbar/>

    <v-container>
        <v-sheet class="d-flex align-center mb-5">
            <v-sheet class="text-h6 text-md-h5 font-weight-bold">Аккаунты</v-sheet>
            <v-spacer></v-spacer>
            <Link :href="route('accounts.create')" class="mr-3">
                <v-btn color="success" size="small" icon="mdi-plus-circle-outline"/>
            </Link>
        </v-sheet>


        <v-sheet class="mx-auto" max-width="500">
            <Link
                v-for="account in accounts"
                :href="route('accounts.show', account.id)"
                class="d-block mb-5 text-decoration-none"
            >
                <v-card
                    hover
                    append-icon="mdi-open-in-new"
                    :subtitle="`Название на Авито: ${account.avito_name}`"
                    :title="account.name"
                ></v-card>
            </Link>
        </v-sheet>
    </v-container>
</template>
