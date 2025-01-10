<script setup>
import {onMounted, ref} from "vue";
import FastTemplatesEditDialog from "@/Components/FastTemplates/FastTemplatesEditDialog.vue";
import FastTemplatesList from "@/Components/FastTemplates/FastTemplatesList.vue";

const model = defineModel();

defineOptions({
    inheritAttrs: false
})

const emit = defineEmits(['selected', 'sendFastly']);
const props = defineProps({
    activeAccount: {
        type: Object,
        required: true
    }
})

const fastTemplates = ref([])

let selected = ref({});
let startManage = ref(false);
let editDialog = ref(false);

onMounted(fetchList);

function fetchList() {
    axios
        .get(route('fast-templates.index', props.activeAccount.id))
        .then((response) => {
            fastTemplates.value = response.data
        })
}

const onSelect = (item) => {
    emit('selected', item.content);

    model.value = false;
}

const onDeleted = (id) => {
    fetchList();

    editDialog.value = false
}

function onEdit(item) {
    selected.value = item

    editDialog.value = true
}

function onSaved() {
    selected.value = {}

    fetchList();
}

const searchText = ref("")
</script>

<template>
    <v-bottom-sheet v-model="model" style="height: 100vh" @close="model = false">
        <v-sheet style="height: 100vh">
            <v-container>
                <v-sheet class="d-flex align-center justify-between">
                    <v-sheet class="d-flex w-100 pe-4">
                        <v-icon
                            @click="model = false"
                            icon="mdi-arrow-left"
                            size="20"
                            class="mr-2"
                        />

                        <input
                            v-model="searchText"
                            type="text" placeholder="Поиск" style="font-size: 15px; outline: none; width: 100%"/>
                    </v-sheet>
                    <v-sheet class="d-flex align-center">
                        <v-chip v-if="startManage" prepend-icon="mdi-plus-circle" size="x-small" color="success" @click="() => editDialog = true" class="mr-5">
                            Добавить
                        </v-chip>

                        <v-icon icon="mdi-square-edit-outline" :color="startManage ? 'red' : undefined" @click="() => startManage = !startManage"></v-icon>
                    </v-sheet>
                </v-sheet>

                <fast-templates-list
                    :activeAccount="activeAccount"
                    :startManage="startManage"
                    @itemSelected="onSelect"
                    @edit="onEdit"
                    :items="fastTemplates"
                    :search-text="searchText"
                />
            </v-container>
        </v-sheet>
    </v-bottom-sheet>

    <fast-templates-edit-dialog
        :active-account="activeAccount"
        v-model="editDialog"
        :selected="selected"
        @deleted="onDeleted"
        @created="onSaved"
        @updated="onSaved"
    />
</template>
