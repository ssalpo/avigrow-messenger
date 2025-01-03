<script setup>
const model = defineModel();

const emits = defineEmits(['selected'])

const props = defineProps(['element'])

const macrosList = [
    {
        key: '{name}',
        description: 'имя написавшего'
    },
    {
        key: '{adTitle}',
        description: 'название объявления'
    },
    {
        key: '{price}',
        description: 'цена в объявлении'
    },
    {
        key: '{location}',
        description: 'местоположение'
    },
    {
        key: '{welcome}',
        description: 'приветствие по времени'
    },
]

const onSelect = (macros) => {
    emits('selected', macros)

    let textArea = props.element;

    if(textArea) {
        // Получение текущей позиции курсора
        const cursorPosition = textArea.selectionStart;

        // Текущий текст в textarea
        const text = textArea.value;

        // Обновление текста в textarea
        model.value = text.slice(0, cursorPosition) + macros.key + text.slice(cursorPosition);

        // Переместить курсор после вставленного макроса
        textArea.selectionStart = textArea.selectionEnd = cursorPosition + macros.key.length;

        // textArea.focus();
    }
}

</script>

<template>
    <v-menu>
        <v-card>
            <v-card-text>
                <div class="macros-item"
                     @click="() => onSelect(macros)"
                     v-for="macros in macrosList">
                    <span class="macros-key">{{ macros.key }} -</span> {{ macros.description }}
                </div>
            </v-card-text>
        </v-card>
    </v-menu>
</template>

<style scoped>
.macros-item {
    margin-bottom: 4px;
    cursor: pointer;
    font-size: 14px;
}

.macros-item:hover {
    opacity: 0.8;
    color: red;
}

.macros-key {
    color: rgb(62, 99, 226);
}
</style>
