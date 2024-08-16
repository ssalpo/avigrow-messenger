<script setup>
import { router } from '@inertiajs/vue3'
import {onMounted} from "vue";

const props = defineProps({
    activeAccountId: {
        type: Number
    },
    items: {
        type: Array,
    }
});

const changeAccount = (id) => {
    router.visit(route('account.chats', id));
}

onMounted(() => {
    document.getElementsByClassName("active-menu-element")[0]?.scrollIntoView();
})

</script>

<template>
    <ul class="head-tab-menu">
        <li v-for="item in items"
            :class="{'active active-menu-element': item.id === activeAccountId}"
            @click="changeAccount(item.id)">{{item.name}}</li>
    </ul>
</template>
