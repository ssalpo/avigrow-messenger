<script setup>
import {computed, ref} from "vue";
import QuizEditModal from "@/Pages/Bots/Modals/QuizEditModal.vue";
import draggable from 'vuedraggable'

const props = defineProps({
    bot: {
        type: Object,
        required: true
    }
})

const quizModal = ref(false)
const selectedQuiz = ref({})

const onSelectTrigger = (selected) => {
    selectedQuiz.value = selected
    quizModal.value = true
}

const dragOptions = computed(() => {
    return {
        animation: 200,
        group: "description",
        disabled: false,
        ghostClass: "ghost"
    }
})

const onSortEnd = () => {
    axios.post(route('bots.quizzes.resort', props.bot.id), {
        quizzes: props.bot.quizzes.map(e => e.id)
    })
}
</script>

<template>
    <v-card border>
        <v-card-title class="d-flex align-center">
            Список вопросов
            <v-spacer/>
            <v-icon size="small"
                    icon="mdi-plus-circle-outline"
                    @click="() => onSelectTrigger({})"/>
        </v-card-title>
        <v-divider/>
        <v-card-text>
            <draggable
                @end="onSortEnd"
                v-model="bot.quizzes"
                handle=".quiz-sort-handler"
                item-key="id"
                v-bind="dragOptions"
            >
                <template #item="{element}">
                    <v-sheet
                        border
                        rounded
                        hover
                        class="mb-3 d-flex align-center px-5 py-3 cursor-pointer"
                    >
                        <v-icon icon="mdi-sort"
                                class="mr-3 opacity-60 quiz-sort-handler"/>

                        <div class="w-100"
                             @click="() => onSelectTrigger(element)">{{ element.name }}
                        </div>

                        <v-icon icon="mdi-square-edit-outline"
                                @click="() => onSelectTrigger(element)"/>
                    </v-sheet>
                </template>
            </draggable>

        </v-card-text>
    </v-card>

    <QuizEditModal
        :bot-id="bot.id"
        :selected="selectedQuiz"
        v-model="quizModal"
    />
</template>

<style scoped>
.ghost {
    opacity: 0.5;
    background: #ea8d8d;
}
</style>
