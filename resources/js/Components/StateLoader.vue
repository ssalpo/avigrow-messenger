<script setup>
import {onMounted, ref} from "vue";
import { router } from '@inertiajs/vue3'

const isLoading = ref(false);

onMounted(() => {
    router.on('start', () => isLoading.value = true)

    router.on('finish', () => isLoading.value = false)
})

</script>

<template>
    <div class="text-center">
        <v-overlay
            persistent
            v-model="isLoading"
            scroll-strategy="block"
            class="align-center justify-center"
        >
            <v-progress-circular
                color="blue-darken-1"
                size="64"
                indeterminate
            ></v-progress-circular>
        </v-overlay>
    </div>
</template>
