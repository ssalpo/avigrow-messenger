<script setup>

import Navbar from "@/Components/Menu/Navbar.vue";
import {Head, router} from "@inertiajs/vue3";
import {onMounted, ref, watch} from "vue";
import {OnLongPress} from '@vueuse/components'
import AddCodeKeyModal from "@/Components/AddCodeKeyModal.vue";
import PageTitle from "@/Components/PageTitle.vue";

const props = defineProps(['tabs', 'keys', 'errors'])

const tab = ref(null)
const addDialog = ref(false)

onMounted(() => {
    if (Object.keys(props.errors).length !== 0) {
        addDialog.value = true
    }
})

function copyContent(text, item) {
    navigator.clipboard.writeText(text)

    router.post(route('code-keys.mark-as-receipt', item.id))
}

function destroy(id) {
    if (!confirm('Вы уверены что хотите удалить?')) {
        return
    }

    router.delete(route('code-keys.destroy', id), {
        preserveScroll: true,
        preserveState: true
    })
}
</script>

<template>
    <page-title
        :back-url="route('code-keys.index')"
        text="Ключи и аккаунты"
    >
        <template v-slot:append>
            <v-spacer />
            <v-menu>
                <template v-slot:activator="{ props }">
                    <v-icon icon="mdi-dots-vertical" v-bind="props"></v-icon>
                </template>

                <v-list density="compact">
                    <v-list-item
                        title="Добавить"
                        @click="() => addDialog = true"
                    />

                    <v-list-item
                        title="История"
                        @click="() => router.visit(route('code-keys.histories'))"
                    />
                </v-list>
            </v-menu>
        </template>
    </page-title>

    <v-tabs
        bg-color="blue-darken-1"
        v-model="tab"
    >
        <v-tab :value="value" :key="value" v-for="(label, value) in tabs">
            {{ label }}
            {{ keys[value] !== undefined ? `(${keys[value]?.length})` : '' }}
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
                                <v-menu>
                                    <template v-slot:activator="{ props }">
                                        <v-btn icon="mdi-dots-vertical" variant="text" v-bind="props"></v-btn>
                                    </template>

                                    <v-list density="compact">
                                        <v-list-item
                                            title="Удалить"
                                            @click="destroy(key.id)"
                                        />
                                    </v-list>
                                </v-menu>
                            </template>
                        </v-list-item>
                    </OnLongPress>
                </v-list>

            </template>

            <v-sheet v-else class="pa-5 text-center">Список пуст</v-sheet>
        </v-tabs-window-item>
    </v-tabs-window>

    <add-code-key-modal
        :errors="errors"
        :categories="Object.keys(tabs).map(k => ({value: parseInt(k), title: tabs[k]}))"
        v-model="addDialog"/>
</template>

<style scoped>

</style>
