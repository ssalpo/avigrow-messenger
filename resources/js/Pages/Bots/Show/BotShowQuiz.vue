<script setup>
import {ref} from "vue";
import QuizEditModal from "@/Pages/Bots/Modals/QuizEditModal.vue";
import GreetingEditModal from "@/Pages/Bots/Modals/GreetingEditModal.vue";

defineProps({
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

</script>

<template>
    <v-card border>
        <v-card-title class="d-flex align-center">
            Список вопросов
            <v-spacer />
            <v-icon size="small" icon="mdi-plus-circle-outline" @click="() => onSelectTrigger({})" />
        </v-card-title>
        <v-divider />
        <v-card-text>
            <v-sheet
                border
                rounded
                hover
                v-for="quiz in bot.quizzes"
                class="mb-3 d-flex align-center px-5 py-3 cursor-pointer"
                @click="() => onSelectTrigger(quiz)"
            >
                <div class="w-100">{{ quiz.name }}</div>

                <v-icon icon="mdi-square-edit-outline" />
            </v-sheet>
        </v-card-text>
    </v-card>

    <QuizEditModal
        :bot-id="bot.id"
        :selected="selectedQuiz"
        v-model="quizModal"
    />
</template>
