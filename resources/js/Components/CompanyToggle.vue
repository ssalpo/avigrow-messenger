<script setup>
import {router, usePage} from "@inertiajs/vue3";
import {ref} from "vue";

const page = usePage()

const companies = ref(page.props.navCompanies);
const selectedCompany = ref(page.props.selectedCompany)

const onChange = (value) => {
    router.post(route('companies.toggle', value))
}
</script>

<template>
    <v-select
        hide-details
        :rounded="false"
        @update:modelValue="onChange"
        v-model="selectedCompany"
        label="Текущая компания"
        :items="companies"
        item-title="name"
        item-value="id"
    ></v-select>
</template>
