<script setup>
defineProps(['answer']);

const statuses = {
    "moderation": "на модерации",
    "rejected": "отклонен"
}
</script>

<template>
    <v-sheet class="ml-10 mt-4 text-body-2 border pa-3 rounded">
        <v-chip v-if="answer.status !== 'published'" color="red">
            {{statuses[answer.status]}}
        </v-chip>

        <small v-if="answer.reject_reasons !== undefined" class="text-red text-small my-2 d-block">
            {{answer.reject_reasons.title}}
        </small>

        <div>{{ answer.text }}</div>
    </v-sheet>
</template>
