<script setup>
import {useForm} from "@inertiajs/vue3";

const props = defineProps(['account'])

const form = useForm({
    is_enabled: props.account?.status?.is_enabled,
    always_online: props.account?.status?.always_online,
    available_from: props.account?.status?.available_from,
    available_to: props.account?.status?.available_to,
})

const send = () => {
    form.patch(route('accounts.update-account-status-settings', props.account.id))
}
</script>

<template>

    <v-form @submit.prevent="send">
        <div class="border px-5 py-1 mb-6 rounded">
            <v-switch label='Включить режим?'
                      hide-details
                      color="primary"
                      v-model="form.is_enabled"></v-switch>
        </div>

        <div class="text-subtitle-2 text-medium-emphasis mb-4"
             :class="{'opacity-50': form.always_online || !form.is_enabled}">
            Расписание нахождения в сети
        </div>

        <v-row :class="{'opacity-50': form.always_online || !form.is_enabled}">
            <v-col cols="6"
                   lg="4">
                <v-text-field
                    label="Начало"
                    autocomplete="off"
                    v-mask="'##:##'"
                    placeholder="ЧЧ:ММ"
                    variant="outlined"
                    persistent-hint
                    density="compact"
                    v-model="form.available_from"
                ></v-text-field>
            </v-col>

            <v-col cols="6"
                   lg="4">
                <v-text-field
                    label="Конец"
                    autocomplete="off"
                    v-mask="'##:##'"
                    placeholder="ЧЧ:ММ"
                    variant="outlined"
                    persistent-hint
                    density="compact"
                    v-model="form.available_to"
                ></v-text-field>
            </v-col>
        </v-row>

        <v-switch :class="{'opacity-50': !form.is_enabled}"
                  label="Круглосуточно находиться в сети?"
                  color="primary"
                  v-model="form.always_online"></v-switch>

        <v-btn
            class="mb-5"
            width="200"
            color="blue-darken-1"
            type="submit">
            Сохранить
        </v-btn>
    </v-form>

</template>
