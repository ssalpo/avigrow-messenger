<script setup>
import {onMounted, ref} from "vue";

let isOnline = ref(true);

onMounted(() => {
    window.addEventListener('online', () => isOnline.value = true);
    window.addEventListener('offline', () => isOnline.value = false);
});
</script>

<template>
    <div class="internet-status-checker" v-if="!isOnline">
        <span>Интернет отключен</span>
        <small>Окно автоматически закроется после подключения к интернету!</small>
    </div>
</template>
