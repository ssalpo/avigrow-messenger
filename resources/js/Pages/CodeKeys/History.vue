<script setup>

import Navbar from "@/Components/Navbar.vue";
import {Head, router} from "@inertiajs/vue3";
import {ref} from "vue";
import {OnLongPress} from '@vueuse/components'
import AddCodeKeyModal from "@/Components/AddCodeKeyModal.vue";

defineProps(['tabs', 'keys'])

const tab = ref(null)

function copyContent(text, item) {
    navigator.clipboard.writeText(text)
}

function restore(id) {
    router.post(route('code-keys.restore', id))
}
</script>

<template>
    <Head title="История использования"/>

    <navbar/>

    <v-container>
        <v-row class="d-flex align-center mb-2">
            <v-col class="text-center" cols="2">
                <v-btn icon="mdi-arrow-left" size="small" color="primary" variant="text"></v-btn>
            </v-col>

            <v-col cols="10">
                <h3 class="text-h6">История использования</h3>
            </v-col>
        </v-row>

        <v-tabs
            bg-color="primary"
            v-model="tab"
        >
            <v-tab :value="value" :key="value" v-for="(label, value) in tabs">{{ label }}</v-tab>
        </v-tabs>

        <v-tabs-window v-model="tab">
            <v-tabs-window-item
                v-for="(label, value) in tabs"
                :value="value"
                :key="value"
            >
                <template v-if="keys[value] !== undefined">

                    <v-list lines="one">
                        <OnLongPress
                            @trigger="copyContent(key.content, key)"
                            v-for="key in keys[value]"
                            :key="key.id"
                        >
                            <v-list-item
                                v-ripple
                                class="my-3 mx-2"
                                rounded
                                elevation="2"
                                :title="key.content"
                            >
                                <template v-slot:append>
                                    <v-btn icon="mdi-backup-restore" variant="text" @click="() => restore(key.id)"></v-btn>
                                </template>
                            </v-list-item>
                        </OnLongPress>
                    </v-list>

                </template>

                <v-sheet v-else class="pa-5 text-center">Список пуст</v-sheet>
            </v-tabs-window-item>
        </v-tabs-window>
    </v-container>
</template>
