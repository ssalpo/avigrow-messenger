<script setup>
import { router } from '@inertiajs/vue3'

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

</script>

<template>
    <ul class="head-tab-menu">
        <li v-for="item in items"
            :class="{active: item.id === activeAccountId}"
            @click="changeAccount(item.id)">{{item.name}}</li>
    </ul>
</template>
