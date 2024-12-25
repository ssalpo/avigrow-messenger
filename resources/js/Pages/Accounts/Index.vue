<script setup>
import {Link} from "@inertiajs/vue3";
import PageTitle from "@/Components/PageTitle.vue";

defineProps(['accounts'])
</script>

<template>
    <page-title text="Аккаунты">
        <template v-slot:append>
            <v-spacer></v-spacer>
            <Link :href="route('accounts.create')" class="mr-3">
                <v-btn color="success" size="small" icon="mdi-plus-circle-outline"/>
            </Link>
        </template>
    </page-title>

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
</template>
