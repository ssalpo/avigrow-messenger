<script setup>
import {ref, watch} from "vue";
import {chain, cloneDeep, debounce, find, orderBy} from "lodash";

const emits = defineEmits(['itemSelected', 'edit'])

const props = defineProps({
    items: {
        type: Array,
        required: true
    },
    searchText: {
        type: String,
        default: ""
    },
    startManage: {
        type: Boolean,
        default: false
    },
    activeAccount: {
        type: Object,
        required: true
    }
})

let tab = ref('all');
let allFastTemplates = ref([]);
let filteredFastTemplates = ref([]);
let filteredAllFastTemplates = ref([]);

function sendFastly(selected) {
    if (confirm('Уверены что хотите сразу отправить?')) {
        editDialog.value = false
        isBottomSheetOpen.value = false
        emit('sendFastly', selected.content)
    }
}

// Функция для поиска и подсветки
function searchAndHighlight(searchText) {
    const input = searchText.toLowerCase();

    const replaceHighlights = (item) => {
        item.content = item.content.replace(
            new RegExp(`(${input})`, "gi"),
            "<span class='highlight-fm'>$1</span>"
        )

        return item
    }

    const filterIncludes = (e) => e.content.toLowerCase().includes(input)

    // Если отправлен пустой инпут
    if (!input) {
        filteredAllFastTemplates.value = cloneDeep(orderBy(allFastTemplates.value, 'number_of_uses', 'desc'));
        filteredFastTemplates.value = cloneDeep(props.items);
        return
    }

    // фильтрирует элементы общего списка
    filteredAllFastTemplates.value = cloneDeep(allFastTemplates.value)
        .filter(filterIncludes)
        .map(replaceHighlights);

    // фильтрирует элементы внутри каждого таба
    filteredFastTemplates.value = cloneDeep(props.items)
        .map((e) => {
            e.fast_templates = e.fast_templates
                .filter(filterIncludes)
                .map(replaceHighlights)

            return e;
        });
}

const onItemsChanged = (items) => {
    filteredFastTemplates.value = items

    allFastTemplates.value = chain(items)
        .map('fast_templates') // Берем только значения fastMessages
        .compact() // Убираем undefined или null
        .flatten() // Разворачиваем вложенные массивы
        .value(); // Получаем результат

    filteredAllFastTemplates.value = orderBy(allFastTemplates.value, 'number_of_uses', 'desc')
}

const onSelect = (id) => {
    emits('itemSelected', find(allFastTemplates.value, {id}));

    axios.post(route('fast-templates.increment-uses', {account: props.activeAccount.id, id}))
}

const onEdit = (id) => {
    emits('edit', find(allFastTemplates.value, {id}));
}

const swipe = (direction) => {
    let index = tab.value;

    if (direction === 'l' && index === 'all') {
        tab.value = 0
    }

    if (direction === 'r' && index <= 0) {
        tab.value = 'all'
    }

    if (direction === 'l' && filteredFastTemplates.value[index + 1] !== undefined) {
        tab.value = index + 1
    }

    if (direction === 'r' && filteredFastTemplates.value[index - 1] !== undefined) {
        tab.value = index - 1
    }
}

// Watchers
watch(() => props.items, onItemsChanged, {deep: true, immediate: true})

watch(
    () => props.searchText,
    debounce((text) => searchAndHighlight(text), 500)
)
</script>

<template>
    <v-tabs v-model="tab"
            height="35"
            class="mt-4 mb-2">
        <v-tab min-width="auto"
               class="px-3 text-capitalize"
               value="all">Все
        </v-tab>
        <v-tab
            v-for="(tag, i) in props.items"
            :value="i"
            min-width="auto"
            class="px-3  text-capitalize">{{ tag.name }}
        </v-tab>
    </v-tabs>

    <v-tabs-window
        v-model="tab"
        v-touch="{left: () => swipe('l'), right: () => swipe('r')}"
    >
        <v-tabs-window-item value="all">
            <v-sheet
                v-for="(fastTemplate, i) in filteredAllFastTemplates"
                :key="i"
            >
                <v-sheet class="py-3 ps-3 text-body-2 d-flex">

                    <div style="word-break: break-word; width: 100%">
                        <div class="font-weight-bold text-capitalize"
                             style="color: #292929; font-size: 13px">
                            {{ fastTemplate.tag }}
                        </div>
                        <div
                            @click="() => onSelect(fastTemplate.id)"
                            v-html="fastTemplate.content.replace(/\r?\n/g, '<br />')"></div>
                    </div>
                    <v-spacer v-if="startManage"/>
                    <div v-if="startManage"
                         class="ml-4">
                        <v-icon @click="() => onEdit(fastTemplate.id)"
                                icon="mdi-playlist-edit"
                                color="#808080"></v-icon>
                    </div>
                </v-sheet>
                <v-divider/>
            </v-sheet>
        </v-tabs-window-item>

        <v-tabs-window-item
            v-for="(tag, i) in filteredFastTemplates"
            :key="tag.id"
            :value="i">

            <v-sheet
                v-for="(fastTemplate, i) in tag.fast_templates"
                :key="i"
            >
                <v-sheet class="py-3 ps-3 text-body-2 d-flex">
                    <v-sheet style="word-break: break-word; width: 100%"
                             @click="() => onSelect(fastTemplate.id)"
                             v-html="fastTemplate.content.replace(/\r?\n/g, '<br />')"/>

                    <v-spacer v-show="startManage"/>
                    <v-sheet v-show="startManage"
                             class="ml-4">
                        <v-icon @click="() => onEdit(fastTemplate.id)"
                                icon="mdi-playlist-edit"
                                color="#808080"></v-icon>
                    </v-sheet>
                </v-sheet>
                <v-divider/>
            </v-sheet>

        </v-tabs-window-item>
    </v-tabs-window>
</template>
