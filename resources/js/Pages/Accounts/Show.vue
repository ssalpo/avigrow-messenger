<script setup>
import {Link, router} from "@inertiajs/vue3";
import PageTitle from "@/Components/PageTitle.vue";
import BotShowSchedules from "@/Pages/Bots/Show/BotShowSchedules.vue";
import {ref} from "vue";
import EditSettings from "@/Pages/Accounts/EditSettings.vue";

const props = defineProps(['account'])

const panel = ref([])

const toggleActivity = () => {
    router.post(route('accounts.toggle-activity', props.account.id))
}
</script>

<template>
    <page-title
        :text="account.name"
        :back-url="route('accounts.index')"
    >
        <template v-slot:append>
            <v-spacer></v-spacer>

            <v-switch
                @click="toggleActivity"
                inset
                hide-details
                color="primary"
                class="mr-6"
                :model-value="account.is_active"
            ></v-switch>
        </template>
    </page-title>

    <v-btn text="Редактировать"
           @click="() => router.visit(route('accounts.edit', account.id))"
           variant="outlined"
           color="primary"
           prepend-icon="mdi-pencil"
           density="comfortable"/>

    <v-expansion-panels v-model="panel"
                        class="mt-7">
        <v-expansion-panel class="mb-2"
                           title="Настройки">
            <v-expansion-panel-text>
                <edit-settings :account="account"/>
            </v-expansion-panel-text>
        </v-expansion-panel>
    </v-expansion-panels>
</template>
