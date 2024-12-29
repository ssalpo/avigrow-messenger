<script setup>
import {computed, ref} from "vue";
import {useForm} from "@inertiajs/vue3";
import {difference, differenceBy, isEqual} from "lodash";

const props = defineProps({
    bot: {
        type: Object,
        required: true
    },
    accounts: {
        type: Array,
        required: true
    }
})

let form = useForm({accounts: props.bot.accounts.map(a => a.id)});

let selectedAllAccounts = computed(() => form.accounts.length === props.accounts.length)
let selectedSomeAccounts = computed(() => form.accounts.length > 0)

const onSelectAllAccounts = () => {
    form.accounts = selectedAllAccounts.value ? [] : props.accounts.map(a => a.id)
}

const onMenuUpdate = (state) => {
    if(state === false && isEqual(form.accounts, props.bot.accounts.map(a => a.id)) === false) {
        form.post(route('bots.attach-accounts', props.bot.id))
    }
}
</script>

<template>
    <v-expansion-panels class="mt-10" variant="inset">
        <v-expansion-panel class="mb-2" title="Прикрепленные аккаунты">
            <v-expansion-panel-text>
                <v-autocomplete
                    @update:menu="onMenuUpdate"
                    base-color="success"
                    hide-details
                    class="mb-5"
                    max-width="300"
                    v-model="form.accounts"
                    variant="outlined"
                    density="compact"
                    :items="accounts"
                    item-title="name"
                    item-value="id"
                    label="Выберите аккаунты"
                    multiple
                    single-line
                >
                    <template v-slot:selection="{ item, index }">
                        <span v-if="index === 0" class="text-caption align-self-center d-flex align-center">
                            <v-icon icon="mdi-link-edit" size="18" class="me-2" />
                            Прикрепленных аккаунтов: {{form.accounts.length}}
                        </span>
                    </template>

                    <template v-slot:prepend-item>
                        <v-list-item
                            title="Выбрать все"
                            @click="onSelectAllAccounts"
                        >
                            <template v-slot:prepend>
                                <v-checkbox-btn
                                    :indeterminate="selectedSomeAccounts && !selectedAllAccounts"
                                    :model-value="selectedAllAccounts"
                                ></v-checkbox-btn>
                            </template>
                        </v-list-item>

                        <v-divider class="mt-2"></v-divider>
                    </template>

                </v-autocomplete>

                <v-chip
                    prepend-icon="mdi-cast-connected"
                    class="me-3 mb-3"
                    color="blue-darken-1"
                    variant="outlined"
                    v-for="account in bot.accounts"
                >
                    {{ account.name }}
                </v-chip>
            </v-expansion-panel-text>
        </v-expansion-panel>

        <v-expansion-panel class="mb-2" title="Прикрепленные объявления">
            <v-expansion-panel-text>
                Some Text
            </v-expansion-panel-text>
        </v-expansion-panel>
    </v-expansion-panels>
</template>
