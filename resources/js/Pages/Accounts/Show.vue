<script setup>
import {Link} from "@inertiajs/vue3";
import PageTitle from "@/Components/PageTitle.vue";

defineProps(['account'])
</script>

<template>

    <page-title
        :text="account.name"
        :back-url="route('accounts.index')"
    >
        <template v-slot:append>
            <v-spacer></v-spacer>

            <Link :href="route('accounts.edit', account.id)" class="mr-3">
                <v-btn color="primary" size="small" icon="mdi-pencil"/>
            </Link>
        </template>

    </page-title>
</template>
