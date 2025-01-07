<script setup>
import {Link} from "@inertiajs/vue3";
import PageTitle from "@/Components/PageTitle.vue";
import BotShowSchedules from "@/Pages/Bots/Show/BotShowSchedules.vue";
import {ref} from "vue";
import EditSettings from "@/Pages/Accounts/EditSettings.vue";

defineProps(['account'])

const panel = ref([])
</script>

<template>
    <page-title
        :text="account.name"
        :back-url="route('accounts.index')"
    >
        <template v-slot:append>
            <v-spacer></v-spacer>

            <Link :href="route('accounts.edit', account.id)" class="mr-3">
                <v-btn color="blue-darken-1" size="small" icon="mdi-pencil"/>
            </Link>
        </template>
    </page-title>

    <v-expansion-panels v-model="panel" class="mt-10">
        <v-expansion-panel class="mb-2" title="Настройки">
            <v-expansion-panel-text>
                <edit-settings :account="account" />
            </v-expansion-panel-text>
        </v-expansion-panel>
    </v-expansion-panels>
</template>
