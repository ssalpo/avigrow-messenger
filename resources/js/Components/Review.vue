<script setup>

import ReviewAnswer from "@/Components/ReviewAnswer.vue";

const props = defineProps(['review'])

const stageTypes = {
    "done": "Сделка состоялась",
    "fell_through": "Сделка сорвалсь",
    "not_agree": "Не договорились",
    "not_communicate": "Не общались",
}
</script>

<template>

    <v-sheet>
        <v-list>
            <v-list-item
                class="px-0"
                :title="review.sender.name"
                :subtitle="review.createdAt"
            >
                <template v-slot:title="{title}">
                    <b>{{ title }}</b>
                </template>
            </v-list-item>
        </v-list>

        <v-sheet class="d-flex flex-row align-center">
            <v-rating
                hover
                color="yellow-darken-3"
                :length="5"
                readonly
                half-increments
                :size="25"
                :model-value="review.score"
            />

            <span class="text-disabled text-body-2">
                {{stageTypes[review.stage]}}
            </span>
        </v-sheet>

        <v-sheet class="mb-3 text-disabled text-body-2">
            {{ review.item.title }}
        </v-sheet>

        <v-sheet class="text-body-2">
            {{ review.text }}
        </v-sheet>

        <ReviewAnswer v-if="review.answer" :answer="review.answer"/>
    </v-sheet>

</template>
