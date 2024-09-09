<script setup>

import {onMounted, reactive, ref} from "vue";

const emit = defineEmits(['selected', 'sendFastly']);

let fastTemplates = ref([]);
let form = reactive({
    id: null,
    title: '',
    content: ''
});
let errorMessage = ref("");
let isOpen = ref(false);
let isDialogOpen = ref(false);
let isBottomSheetOpen = ref(false);
let editFastTemplate = ref({});

onMounted(fetchList);

function fetchList() {
    axios
        .get('/fast-templates')
        .then((response) => fastTemplates.value = response.data)
}

const onSelect = (template) => {
    emit('selected', template);

    isBottomSheetOpen.value = false;
}

const onSaved = (e) => {
    if (!e?.id) {
        fastTemplates.value.unshift(e)
        return
    }

    editFastTemplate.value = {}

    let obj = fastTemplates.value.find(o => o.id === e.id);

    obj.content = e.content;
}

const deleteFastTemplate = (index, id) => {
    if(!confirm('Уверены что хотите удалить?')) {
        return
    }

    fastTemplates.value.splice(index, 1);

    axios.delete(`/api/fast-templates/${id}`)
}

function onEdit(selected) {
    form.id = selected.id
    form.title = selected.title
    form.content = selected.content
    isDialogOpen.value = true
}

function onCreate() {
    form.id = null;
    form.title = '';
    form.content = '';

    isDialogOpen.value = true
}

function onSave() {
    let url = '/fast-templates';

    if(form.id) {
        form['_method'] = 'PUT'
        url = `/fast-templates/${form.id}`
    }

    axios
        .post(url, form)
        .then(() => {
            isDialogOpen.value = false

            fetchList()
        })
        .catch((r) => {
            errorMessage.value = r.response.data.message
        })
}

function sendFastly(selected) {
    if(confirm('Уверены что хотите сразу отправить?')) {
        isDialogOpen.value = false
        isBottomSheetOpen.value = false
        emit('sendFastly', selected.content)
    }
}

</script>

<template>

    <v-bottom-sheet v-model="isBottomSheetOpen">
        <template v-slot:activator="{ props }">
            <button @click="isOpen = true" v-bind="props" :class="$attrs.class" class="left-btn" type="button">
                📝
            </button>
        </template>

        <v-sheet>
            <v-container>
                <v-row align="center">
                    <v-col class="text-h6" cols="9">Быстрые сообщения</v-col>
                    <v-col class="text-right">
                        <v-btn icon="mdi-plus" @click="onCreate" size="auto" color="success"></v-btn>
                    </v-col>
                </v-row>

                <v-list lines="two">
                    <template v-for="(fastTemplate, i) in fastTemplates"
                              :key="i">
                        <v-list-item
                        :title="fastTemplate.title"
                        @click="() => onSelect(fastTemplate)"
                    >
                        <template v-slot:title="{title}">
                            <div class="v-list-item-title">
                                {{ title }}

                                <v-menu>
                                    <template v-slot:activator="{ props }">
                                        <v-btn size="x-small" icon="mdi-dots-vertical" variant="text" v-bind="props"></v-btn>
                                    </template>

                                    <v-list density="compact">
                                        <v-list-item title="Отправить сразу" @click="sendFastly(fastTemplate)"></v-list-item>
                                        <v-list-item @click="() => onEdit(fastTemplate)" title="Редактировать"></v-list-item>
                                        <v-list-item @click="deleteFastTemplate(i, fastTemplate.id)" title="Удалить"></v-list-item>
                                    </v-list>
                                </v-menu>
                            </div>
                        </template>

                        <template v-slot:subtitle>
                            <div v-html="fastTemplate.content.replace(/\r?\n/g, '<br />')"></div>
                        </template>
                    </v-list-item>

                        <v-divider />
                    </template>
                </v-list>
            </v-container>

        </v-sheet>

    </v-bottom-sheet>

    <v-dialog
        transition="dialog-bottom-transition"
        v-model="isDialogOpen"
        max-width="500"
    >

        <template v-slot:default="{ isActive }">
            <v-form @submit.prevent="onSave">
                <v-card title="Редактирование">
                    <v-card-text>
                        <v-text-field v-model="form.title" density="comfortable" label="Название" variant="outlined"></v-text-field>
                        <v-textarea v-model="form.content" density="comfortable" label="Сообщение" variant="outlined"></v-textarea>

                        <v-alert
                            v-if="errorMessage"
                            :text="errorMessage"
                            type="error"
                        ></v-alert>
                    </v-card-text>

                    <v-divider></v-divider>

                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn
                            text="Закрыть"
                            variant="plain"
                            @click="isActive.value = false"
                        ></v-btn>

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

<style scoped>

</style>
