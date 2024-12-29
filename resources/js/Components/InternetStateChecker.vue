<script setup>
import {onMounted, ref} from "vue";

let dialog = ref(false);

onMounted(() => {
    window.addEventListener('online', () => dialog.value = false);
    window.addEventListener('offline', () => dialog.value = true);
});
</script>

<template>
    <v-dialog
        v-model="dialog"
        persistent
    >
        <v-list
            class="py-2"
            color="blue-darken-1"
            elevation="12"
            rounded="lg"
        >
            <v-list-item
                prepend-icon="$vuetify-outline"
                title="Интернет отключен"
                subtitle="Окно автоматически закроется после подключения к интернету!"
            >
                <template v-slot:prepend>
                    <div class="pe-4">
                        <v-icon color="blue-darken-1" size="x-large">mdi-connection</v-icon>
                    </div>
                </template>

                <template v-slot:subtitle="{subtitle}">
                    <div style="-webkit-line-clamp: 0;">{{subtitle}}</div>
                </template>
            </v-list-item>
        </v-list>
    </v-dialog>
</template>
