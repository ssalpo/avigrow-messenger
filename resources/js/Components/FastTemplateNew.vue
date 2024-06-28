<script setup>
import {ref, watch} from "vue";

const props = defineProps({
    fastTemplate: {
        type: Object
    }
})

const emit = defineEmits(['saved', 'editing']);

const template = ref('')
const isBusy = ref(false)
const isEdit = ref(false)

const save = () => {
    if (template.value.trim() === '') {
        isEdit.value = false;
        return;
    }

    isBusy.value = true;

    let content = {content: template.value};


    let url = '/api/fast-templates';

    if (props.fastTemplate?.id) {
        content['_method'] = 'PUT'
        url = `/api/fast-templates/${props.fastTemplate.id}`
    }

    axios
        .post(url, content)
        .then(() => {
            template.value = ''
            isEdit.value = false
        })
        .finally(() => isBusy.value = false)

    emit('saved', {id: props.fastTemplate?.id, content: template.value})
}

watch(() => props.fastTemplate, () => {
    isEdit.value = true

    template.value = props.fastTemplate.content
})

</script>

<template>
    <a class="fast-messages-new-btn" v-if="!isEdit" @click.prevent="isEdit = true">+ добавить</a>

    <div class="fast-messages-new" v-else>
        <textarea placeholder="Введите шаблон" @keyup.meta.enter="save" v-model="template" />
        <button @click="save">🖊️</button>
    </div>
</template>
