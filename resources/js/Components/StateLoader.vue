<script setup>
import {onMounted, ref} from "vue";
import { router } from '@inertiajs/vue3'

const isLoading = ref(0);

onMounted(() => {
    router.on('start', () => isLoading.value = true)

    router.on('finish', () => isLoading.value = false)
})

</script>

<template>
    <div class="state-loader" v-if="isLoading">
        <div class="loader"></div>
    </div>
</template>
