<script setup>
const model = defineModel({required: true})
const keywords = defineModel('keywords', {required: true, default: [], type: Array})

const addKeyword = () => {
    if(!model.value.trim()) return;

    keywords.value.push(model.value)

    model.value = ''
}

const removeKeyword = (i) => {
    keywords.value.splice(i, 1)
}
</script>

<template>
    <v-text-field
        append-inner-icon="mdi-plus-box-outline"
        @click:append-inner="addKeyword"
        variant="outlined"
        label="Название"
        class="mb-1"
        v-model="model"
        @keyup.enter="addKeyword"
        v-bind="$attrs"
    />

    <v-sheet border rounded class="pa-3 mb-8" v-if="keywords.length">
        <v-chip
            v-for="(keyword, index) in keywords"
            color="teal"
            density="compact"
            class="ma-1"
            variant="outlined"
            :text="keyword"
            closable
            @click:close="() => removeKeyword(index)"
        />
    </v-sheet>
</template>
