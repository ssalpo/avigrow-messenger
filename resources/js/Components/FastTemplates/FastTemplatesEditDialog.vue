<script setup>
import {reactive, ref, watch} from "vue";
import {includes} from "lodash";

const model = defineModel();

const props = defineProps({
    selected: {
        type: Object,
        required: true
    }
})

const emits = defineEmits(['created', 'updated', 'deleted'])

let searchTagIndex = null
let tags = ref([]);
let errorMessage = ref("");

let form = reactive({
    id: null,
    content: '',
    tag: ''
});

function onSave() {
    let url = '/fast-templates';

    if (form.id) {
        form['_method'] = 'PUT'
        url = `/fast-templates/${form.id}`
    }

    axios
        .post(url, form)
        .then(() => {
            model.value = false
            emits(form.id ? 'updated' : 'created', form)
        })
        .catch((r) => errorMessage.value = r.response.data.message)
}

const onDelete = () => {
    if (!confirm('Уверены что хотите удалить?')) {
        return
    }

    axios.delete(`/fast-templates/${form.id}`).then(() => {
        emits('deleted', form.id)
    })
}

function onTagSearchUpdate(value) {
    if (!searchTagIndex) {
        searchTagIndex = tags.value.length
    }

    if (value && includes(tags.value, value)) {
        tags.value.splice(searchTagIndex, 1);
    }

    if (!value || includes(tags.value, value)) return

    if (tags.value[searchTagIndex] !== undefined) {
        tags.value[searchTagIndex] = value
    } else {
        tags.value.push(value)
    }
}

// Watchers
watch(() => props.selected, (selected) => {
    form.id = selected.id
    form.tag = selected.tag
    form.content = selected.content
})

watch(() => model.value, (state) => {
    errorMessage.value = ""

    if (state === true) {
        axios.get(route('fm-tags.index')).then((response) => {
            searchTagIndex = null
            tags.value = response.data;
        })
    }
})

</script>

<template>
    <v-dialog
        transition="dialog-bottom-transition"
        v-model="model"
        max-width="500"
    >
        <template v-slot:default="{ isActive }">
            <v-form @submit.prevent="onSave">
                <v-card title="Редактирование">
                    <v-card-text>
                        <v-autocomplete
                            v-model="form.tag"
                            variant="outlined"
                            density="comfortable"
                            label="Ярлык"
                            @update:search="onTagSearchUpdate"
                            clearable
                            :items="tags"
                        ></v-autocomplete>

                        <v-textarea v-model="form.content" density="comfortable" label="Сообщение"
                                    variant="outlined"></v-textarea>

                        <v-alert
                            v-if="errorMessage"
                            :text="errorMessage"
                            type="error"
                        />
                    </v-card-text>

                    <v-divider></v-divider>

                    <v-card-actions>
                        <v-icon
                            v-if="form.id"
                            @click="onDelete"
                            icon="mdi-delete-outline" class="ml-3" color="red" />

                        <v-spacer></v-spacer>

                        <v-btn
                            type="submit"
                            color="primary"
                            text="Сохранить"
                            variant="tonal"
                        ></v-btn>
                    </v-card-actions>
                </v-card>
            </v-form>
        </template>
    </v-dialog>
</template>
