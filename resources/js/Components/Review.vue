<script setup>

import ReviewAnswer from "@/Components/ReviewAnswer.vue";

const props = defineProps({
    accountId: {
        type: Number,
        required: true
    },
    review: {
        type: Object,
        required: true
    }
})

const emits = defineEmits(['selected'])

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

        <div class="d-flex flex-row align-center">
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
                {{ stageTypes[review.stage] }}
            </span>
        </div>

        <div class="mb-3 text-disabled text-body-2">
            {{ review.item.title }}
        </div>

        <div class="text-body-2 d-block mb-2">
            {{ review.text }}
        </div>

        <div v-if="review.images !== undefined">
            <v-row>
                <v-col cols="3" sm="2" md="1" lg="1" v-for="image in review.images">
                    <a :href="image.sizes[1]['url']" target="_blank">
                        <v-img
                            :lazy-src="image.sizes[0]['url']"
                            rounded
                            :max-width="90"
                            :max-height="120"
                            cover
                            class="bg-grey-lighten-2"
                            :src="image.sizes[0]['url']"
                        >
                            <template v-slot:placeholder>
                                <v-row
                                    align="center"
                                    class="fill-height ma-0"
                                    justify="center"
                                >
                                    <v-progress-circular
                                        color="grey-lighten-5"
                                        indeterminate
                                    ></v-progress-circular>
                                </v-row>
                            </template>
                        </v-img>
                    </a>
                </v-col>
            </v-row>
        </div>

        <ReviewAnswer v-if="review.answer"
                      @deleted="() => review.answer = null"
                      :account-id="accountId"
                      :review="review"/>

        <v-btn variant="outlined"
               v-if="review.canAnswer"
               @click="() => emits('selected', review)"
               class="mt-3"
               density="compact"
               color="primary"
               text="Написать ответ"/>
    </v-sheet>

</template>
