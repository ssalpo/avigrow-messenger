<script setup>

import Navbar from "@/Components/Menu/Navbar.vue";
import {Head, router} from "@inertiajs/vue3";
import {ref} from "vue";
import {OnLongPress} from '@vueuse/components'
import AddCodeKeyModal from "@/Components/AddCodeKeyModal.vue";
import PageTitle from "@/Components/PageTitle.vue";

defineProps(['tabs', 'keys'])

const tab = ref(null)

function copyContent(text) {
    navigator.clipboard.writeText(text)
}

function restore(id) {
    router.post(route('code-keys.restore', id))
}
</script>

<template>
    <page-title
        :back-url="route('code-keys.index')"
        text="История использования"
    />

    <v-tabs
        bg-color="blue-darken-1"
        v-model="tab"
    >
        <v-tab :value="value" :key="value" v-for="(label, value) in tabs">
            {{ label }}
            {{keys[value] !== undefined ? `(${keys[value]?.length})` : ''}}
        </v-tab>
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
                            :subtitle="[key.comment, key.created_at_formatted].filter(x => x).join(' | ')"
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
</template>
