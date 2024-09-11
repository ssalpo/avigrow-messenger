<script setup>
import {ref} from "vue";
import {OnLongPress} from "@vueuse/components";

const props = defineProps(['tabs', 'keys'])

let isBottomSheetOpen = ref(false);
const tab = ref(null)
let baseKeys = ref({...props.keys})

function copyContent(text, item, tabValue) {
    navigator.clipboard.writeText(text)

    axios.post(route('code-keys.mark-as-receipt', {code_key: item.id, empty: 1}))
        .then(() => {
            let items = baseKeys.value[parseInt(tabValue)];

            baseKeys.value[parseInt(tabValue)].splice(items.indexOf(item), 1);
        })
}

</script>

<template>
    <v-bottom-sheet v-model="isBottomSheetOpen">
        <template v-slot:activator="{ props }">
            <button v-bind="props" :class="$attrs.class" class="left-btn message-icon" type="button">
                🔑
            </button>
        </template>

        <v-sheet>
            <v-container>
                <v-tabs
                    bg-color="primary"
                    v-model="tab"
                >
                    <v-tab :value="value" :key="value" v-for="(label, value) in tabs">
                        {{ label }}
                        {{baseKeys[value] !== undefined ? `(${baseKeys[value]?.length})` : ''}}
                    </v-tab>
                </v-tabs>

                <v-tabs-window v-model="tab">
                    <v-tabs-window-item
                        v-for="(label, value) in tabs"
                        :value="value"
                        :key="value"
                    >
                        <template v-if="baseKeys[value] !== undefined && baseKeys[value].length">

                            <v-list lines="one">
                                <OnLongPress
                                    @trigger="copyContent(key.content, key, value)"
                                    v-for="key in baseKeys[value]"
                                    :key="key.id"
                                >
                                    <v-list-item
                                        v-ripple
                                        class="my-3 mx-2"
                                        rounded
                                        elevation="2"
                                        :title="key.content"
                                        :subtitle="[key.comment, key.created_at_formatted].filter(x => x).join(' | ')"
                                    />
                                </OnLongPress>
                            </v-list>

                        </template>

                        <v-sheet v-else class="pa-5 text-center">Список пуст</v-sheet>
                    </v-tabs-window-item>
                </v-tabs-window>
            </v-container>
        </v-sheet>
    </v-bottom-sheet>
</template>

<style scoped>

</style>
