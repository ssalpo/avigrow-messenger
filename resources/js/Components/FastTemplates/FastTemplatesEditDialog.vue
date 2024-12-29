<script setup>
import {ref, watch} from "vue";
import {includes} from "lodash";
import {useForm} from "@inertiajs/vue3";

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

let form = useForm({
    id: null,
    content: '',
    tag: ''
});

function onSave() {
    const options = {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            form.reset()
            model.value = false
            emits(form.id ? 'updated' : 'created', form)
        }
    }

    if (form.id) {
        form.patch(route('fast-templates.update', form.id), options)
        return
    }

    form.post(route('fast-templates.store'), options)
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
    form.reset()

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
                            :error-messages="form.errors.tag"
                            class="mb-3"
                        ></v-autocomplete>

                        <v-textarea
                            v-model="form.content"
                            :error-messages="form.errors.content"
                            density="comfortable"
                            label="Сообщение"
                            variant="outlined"
                        ></v-textarea>
                    </v-card-text>

                    <v-divider></v-divider>

                    <v-card-actions>
                        <v-icon
                            v-if="form.id"
                            @click="onDelete"
                            icon="mdi-delete-outline" class="ml-3" color="red"/>

                        <v-spacer></v-spacer>

                        <v-btn
                            :disabled="form.processing"
                            type="submit"
                            color="blue-darken-1"
                            text="Сохранить"
                            variant="tonal"
                        ></v-btn>
                    </v-card-actions>
                </v-card>
            </v-form>
        </template>
    </v-dialog>
</template>
