<script setup>
import {router} from "@inertiajs/vue3";

const props = defineProps({
    accountId: {
        type: Number,
        required: true
    },
    review: {
        type: Object,
        required: true
    }
});

const emits = defineEmits(['deleted'])

const statuses = {
    "moderation": "на модерации",
    "rejected": "отклонен"
}

const onDelete = () => {
    if(!confirm('Вы уверены что хотите удалить?')) {
        return
    }

    let url  = route('reviews.answer.destroy', {account: props.accountId, review: props.review.id, answer: props.review.answer.id})

    router.delete(url, {
        preserveState: true,
        preserveScroll: true,
        onSuccess: () => {
            props.review.answer = null
            props.review.canAnswer = true
        }
    })
}
</script>

<template>
    <div class="ml-10 mt-4 text-body-2 border pa-3 rounded">
        <v-chip v-if="review.answer.status !== 'published'"
                color="red"
                class="mb-3">
            {{ statuses[review.answer.status] }}
        </v-chip>

        <small v-if="review.answer.reject_reasons !== undefined"
               class="text-red text-small my-2 d-block">
            {{ review.answer.reject_reasons.title }}
        </small>

        <div class="d-flex">
            <div class="mr-3">{{ review.answer.text }}</div>
            <v-spacer/>
            <v-icon icon="mdi-trash-can-outline"
                    @click="onDelete"
                    color="error"/>
        </div>
    </div>
</template>
