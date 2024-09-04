<script setup>

import {onMounted, ref} from "vue";
import FastTemplateNew from "@/Components/FastTemplateNew.vue";

const emit = defineEmits(['selected']);

let fastTemplates = ref([]);
let isOpen = ref(false);
let editFastTemplate = ref({});

onMounted(() => {
    axios
        .get('/api/fast-templates')
        .then((response) => fastTemplates.value = response.data)
});

const onSelect = (template) => {
    emit('selected', template);

    isOpen.value = false;
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
    fastTemplates.value.splice(index, 1);

    axios.delete(`/api/fast-templates/${id}`)
}

</script>

<template>
    <button @click="isOpen = true" :class="$attrs.class" class="left-btn" type="button">📝</button>

    <Teleport to="body">
        <div class="fast-messages" v-show="isOpen">
            <div class="fast-messages__left">
                <div class="fast-messages__close" @click="isOpen = false">❌</div>
            </div>

            <div class="fast-messages__templates">
                <fast-template-new :fast-template="editFastTemplate" @saved="onSaved"/>

                <div class="fast-messages-template" v-if="!editFastTemplate?.id" v-for="(fastTemplate, index) in fastTemplates">
                    <div class="fast-messages-template__content" @click="onSelect(fastTemplate)" v-html="fastTemplate.content.replace(/\r?\n/g, '<br />')">
                    </div>
                    <div>
                        <div class="fast-messages-template__edit" @click="editFastTemplate = fastTemplate">🖊</div>
                        <div class="fast-messages-template__edit" @click="deleteFastTemplate(index, fastTemplate.id)">🗑️</div>
                    </div>
                </div>
            </div>
        </div>
    </Teleport>
</template>

<style scoped>

</style>
