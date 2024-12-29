<script setup>
import {VTreeview} from "vuetify/labs/components";
import {onMounted, ref} from "vue";
import {isEqual} from "lodash";
import {useForm} from "@inertiajs/vue3";

const props = defineProps({
    bot: {
        type: Object,
        required: true
    }
})

let itemsAlreadyLoad = ref(false);
const items = ref([])

const form = useForm({
    ads: []
})

const loadItems = () => {
    itemsAlreadyLoad.value = false

    axios
        .get(route('bots.connected-add-treeview', props.bot.id))
        .then((res) => {
            form.ads = res.data.selected
            items.value = res.data.treeView

            itemsAlreadyLoad.value = true
        })
};

onMounted(() => {
    loadItems()
})

const saveChanges = () => {
    form.post(route('bots.attach-ads', props.bot.id), {
        preserveScroll: true,
        preserveState: true
    })
}
</script>

<template>

    <v-btn
        color="blue-darken-1"
        @click="saveChanges"
        :disabled="form.processing"
        text="Сохранить изменения"
        class="mb-3 mt-5"
    />

    <div class="mt-5" v-if="!itemsAlreadyLoad">
        <v-progress-circular
            color="primary"
            indeterminate
            :size="35"
            :width="5"
        ></v-progress-circular>
    </div>

    <v-treeview
        v-model:selected="form.ads"
        open-on-click
        item-value="id"
        select-strategy="classic"
        selectable
        :items="items">
        <template v-slot:prepend="{ item, isOpen }">
            <v-icon v-if="item.children !== undefined">
                mdi-account-arrow-right-outline
            </v-icon>
            <v-icon v-else>
                mdi-receipt-text
            </v-icon>
        </template>
    </v-treeview>
</template>
