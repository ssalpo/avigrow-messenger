<script setup>
import {Link, router} from "@inertiajs/vue3";
import PageTitle from "@/Components/PageTitle.vue";
import TriggerEditModal from "@/Pages/Bots/Modals/TriggerEditModal.vue";
import {ref} from "vue";
import GreetingEditModal from "@/Pages/Bots/Modals/GreetingEditModal.vue";

defineProps(['bot'])
defineOptions({inheritAttrs: false})

const triggerModal = ref(false)
const selectedTrigger = ref(null)

const greetingModal = ref(false)
const selectedGreeting = ref(null)

const onSelectTrigger = (selected) => {
    selectedTrigger.value = selected
    triggerModal.value = true
}

const onSelectGreeting = (selected) => {
    selectedGreeting.value = selected
    greetingModal.value = true
}

const onCreate = () => {
    selectedTrigger.value = null
    triggerModal.value = true
}

const onCreateGreeting = () => {
    selectedGreeting.value = null
    greetingModal.value = true
}

</script>

<template>

    <page-title
        :text="bot.name"
        :back-url="route('bots.index')"
    >
        <template v-slot:append>
            <v-spacer></v-spacer>

            <Link :href="route('bots.edit', bot.id)" class="mr-3">
                <v-btn color="primary" size="small" icon="mdi-pencil"/>
            </Link>
        </template>
    </page-title>

    <v-card border class="mb-10">
        <v-card-title class="d-flex align-center">
            Приветствия
            <v-spacer />
            <v-icon size="small" icon="mdi-plus-circle-outline" @click="onCreateGreeting" />
        </v-card-title>
        <v-divider />
        <v-card-text>
            <v-sheet
                border
                rounded
                hover
                v-for="greeting in bot.greetings"
                class="mb-3 d-flex align-center px-5 py-3 cursor-pointer"
                @click="() => onSelectGreeting(greeting)"
            >
                <div class="w-100">{{ greeting.template }}</div>
                <v-icon icon="mdi-square-edit-outline" />
            </v-sheet>
        </v-card-text>
    </v-card>

    <v-card border>
        <v-card-title class="d-flex align-center">
            Триггеры
            <v-spacer />
            <v-icon size="small" icon="mdi-plus-circle-outline" @click="onCreate" />
        </v-card-title>
        <v-divider />
        <v-card-text>
            <v-sheet
                border
                rounded
                hover
                v-for="trigger in bot.triggers"
                class="mb-3 d-flex align-center px-5 py-3 cursor-pointer"
                @click="() => onSelectTrigger(trigger)"
            >
                <div class="w-100">{{ trigger.keywords.join(', ') }}</div>

                <v-icon icon="mdi-square-edit-outline" />
            </v-sheet>
        </v-card-text>
    </v-card>

    <trigger-edit-modal
        :bot-id="bot.id"
        :selected="selectedTrigger"
        v-model="triggerModal"
    />

    <greeting-edit-modal
        :bot-id="bot.id"
        :selected="selectedGreeting"
        v-model="greetingModal"
    />
</template>
