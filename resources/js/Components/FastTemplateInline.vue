<script setup>
import {onMounted, ref, watch} from "vue";
import {debounce} from "lodash";

const model = defineModel()

const emits = defineEmits(['selected'])

const props = defineProps({
    accountId: {
        type: Number,
        required: true
    },
    isExternalSelect: {
        type: Boolean
    }
})

const menu = ref(false)
const fastTemplates = ref([])
const filteredFastTemplates = ref([])

const scrollToEnd = () => {
    let autocompleteList = document.querySelector('.autocomplete-list');
    autocompleteList.scrollTop = autocompleteList.scrollHeight;
}

const search = (text) => {
    let input = text.toLowerCase().trim()

    if (input) {
        filteredFastTemplates.value = fastTemplates.value.filter(
            (e) => e.content.toLowerCase().includes(input)
        )

        let total = filteredFastTemplates.value.length

        menu.value = total > 0

        if(total > 0) {
            scrollToEnd()
        }

        return
    }

    menu.value = false;
}

onMounted(() => {
    axios.get(route('fast-templates.all', props.accountId))
        .then((r) => {
            fastTemplates.value = r.data
            filteredFastTemplates.value = r.data
        })
})

watch(
    () => model.value,
    debounce((text) => search(text), 200)
)

</script>

<template>
    <div class="autocomplete-container">
        <div
            v-show="menu && !isExternalSelect"
            class="autocomplete-list">
            <div
                @click="() => emits('selected', fastTemplate.content)"
                v-for="(fastTemplate, i) in filteredFastTemplates"
                :key="i"
            >
                <div class="py-3 ps-3 text-body-2 d-flex">
                    <div style="word-break: break-word;"
                         v-html="fastTemplate.content.replace(/\r?\n/g, '<br />')"/>

                </div>
                <v-divider/>
            </div>
        </div>
        <slot/>
    </div>
</template>

<style scoped>
.autocomplete-container {
    position: relative;
    width: 100%;
}

.autocomplete-list {
    position: absolute;
    bottom: 100%;
    left: 0;
    right: 0;
    background: #fff;
    border-top: 1px solid #ccc;
    max-height: 300px;
    overflow-y: auto;
    z-index: 1000;
    list-style: none;
    width: 100%;
    padding: 0;
    margin: 0;
}
</style>
