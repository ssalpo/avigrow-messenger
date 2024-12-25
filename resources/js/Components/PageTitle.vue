<script setup>

import {Head, router} from "@inertiajs/vue3";

const props = defineProps({
    text: {
        type: String,
        required: true
    },
    backUrl: {
        type: String
    },
    withoutHead: {
        type: Boolean,
        default: false
    }
})
</script>

<template>
    <Head v-if="!props.withoutHead" :title="props.text" />

    <v-sheet class="d-flex align-center mb-5">
        <slot name="prepend">
            <v-icon
                v-if="backUrl"
                icon="mdi-arrow-left"
                size="small"
                @click="router.visit(props.backUrl)"
                class="mr-2"
            />
        </slot>

        <slot name="title">
            <h3 class="text-h6">{{text}}</h3>
        </slot>

        <slot name="append"></slot>
    </v-sheet>
</template>
