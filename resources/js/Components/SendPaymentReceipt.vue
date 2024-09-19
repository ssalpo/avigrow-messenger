<script setup>
import {onMounted, ref} from "vue";
import {useForm} from "@inertiajs/vue3";

const props = defineProps(['imageUrl'])

const products = ref([])
const form = useForm({
    url: props.imageUrl,
    caption: null
})

onMounted(() => {
    console.log(props.imageUrl);

    axios.get(route('autocomplete.products'))
        .then((response) => {
            products.value = response.data
        })
})

function send(dialog) {
    dialog.value = false

    form.post(route('send-payment-receipt'), {
        preserveState: false
    })
}
</script>

<template>
    <v-menu
        :close-on-content-click="false"
    >
        <template v-slot:activator="{ props }">
            <slot :props="props">
                <v-btn v-bind="props">Отправить в отчет</v-btn>
            </slot>
        </template>

        <template v-slot:default="{ isActive }">
            <v-card min-width="300">
                <v-card-text>
                    <v-select
                        variant="outlined"
                        v-model="form.caption"
                        :items="products"
                        item-title="name"
                        item-value="name"
                        label="Выберите продукт"
                        class="mb-3"
                        @update:modelValue="send(isActive)"
                    />
                </v-card-text>
            </v-card>
        </template>
    </v-menu>
</template>
