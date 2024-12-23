<script setup>
import Navbar from "@/Components/Navbar.vue";
import {Head, Link} from "@inertiajs/vue3";
import BackBtn from "@/Components/BackBtn.vue";

defineProps(['account'])
</script>

<template>
    <Head :title="account.name + ` - Аккаунт`"/>

    <navbar/>

    <v-container>
        <v-sheet class="mb-5 d-flex align-center">
            <back-btn :url="route('accounts.index')"/>

            <v-sheet class="text-h6 text-md-h5 font-weight-bold text-truncate mr-5" max-width="500">
                {{ account.name }}
            </v-sheet>

            <v-spacer></v-spacer>

            <Link :href="route('accounts.edit', account.id)" class="mr-3">
                <v-btn color="primary" size="small" icon="mdi-pencil"/>
            </Link>
        </v-sheet>

    </v-container>
</template>

<style scoped>

</style>
