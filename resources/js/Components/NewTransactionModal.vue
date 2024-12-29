<script setup>
import { VNumberInput } from 'vuetify/labs/VNumberInput'
import {onMounted, ref} from "vue"
import {useForm} from "@inertiajs/vue3"

const props = defineProps(['errors', 'transaction'])

let dialog = ref(false);

const form = useForm({
    type: props.transaction?.type ?? null,
    amount: props.transaction?.amount ?? null,
    comment: props.transaction?.comment ?? null,
})

function send(dialog) {

    const onSuccess = () => {
        form.reset()
        dialog.value = false
    }

    if(props.transaction?.id) {
        form.patch(route('transactions.update', props.transaction.id), {
            onSuccess
        })
        return;
    }

    form.post(route('transactions.store'), {
        onSuccess
    })
}

function openDialog(type) {
    form.type = type
    dialog.value = true
}
</script>

<template>
    <v-dialog max-width="600" v-model="dialog" v-bind="$attrs">
        <template v-slot:activator="{ isActive, props }">
            <slot :props="props" :toggle-dialog="() => dialog = !dialog">
                <div class="text-center">
                    <v-btn color="success" class="mr-2" prepend-icon="mdi-plus" size="x-small" @click="() => openDialog( 1)">доход</v-btn>
                    <v-btn color="error" size="x-small" prepend-icon="mdi-minus" @click="() => openDialog(2)">расход</v-btn>
                </div>
            </slot>
        </template>

        <template v-slot:default="{ isActive }">
            <v-card
                prepend-icon="mdi-account"
                :title="form.type === 1 ? 'Доход' : 'Расход'"
            >
                <v-card-text>
                    <v-number-input
                        v-model="form.amount"
                        controlVariant="stacked"
                        label="Сумма"
                        :min="1"
                        :error-messages="errors?.amount"
                        class="mb-3"
                    />

                    <v-text-field
                        v-model="form.comment"
                        label="Комментарий"
                        :error-messages="errors?.comment"
                    />
                </v-card-text>

                <v-divider></v-divider>

                <v-card-actions>
                    <v-spacer></v-spacer>

                    <v-btn
                        text="Закрыть"
                        variant="plain"
                        @click="isActive.value = false"
                    ></v-btn>

                    <v-btn
                        color="blue-darken-1"
                        text="Сохранить"
                        variant="tonal"
                        @click="send(isActive)"
                    ></v-btn>
                </v-card-actions>
            </v-card>
        </template>
    </v-dialog>
</template>
