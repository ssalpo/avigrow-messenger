<script setup>
import PageTitle from "@/Components/PageTitle.vue";
import {router, usePage} from "@inertiajs/vue3";
import {ref, watch} from "vue";
import EmployeeEditModal from "@/Pages/Employee/EmployeeEditModal.vue";

const props = defineProps(['employees'])
const selectedEmployee = ref(null)
const modal = ref(false)
const passwordModal = ref(false)


const onDelete = (id) => {
    if (!confirm('Вы уверены что хотите удалить?')) {
        return
    }

    router.delete(route('employees.destroy', id))
}

const onCopy = () => {
    passwordModal.value = false
}

watch(() => usePage().props.backData?.employeeAccountPassword, (value) => {
    if (value) {
        passwordModal.value = true
    }
})
</script>

<template>
    <page-title text="Операторы"/>

    <v-btn text="Добавить"
           density="comfortable"
           class="mb-5"
           color="primary"
           @click="() => modal = true"/>

    <v-card
        v-for="employee in employees"
        class="mb-3"
        hover
        :subtitle="employee.name"
        :title="employee.email"
    >
        <template v-slot:prepend>
            <v-icon icon="mdi-account-group"
                    class="mr-2"
                    size="30"
                    color="success"/>
        </template>

        <template v-slot:append>
            <v-icon icon="mdi-trash-can-outline"
                    @click="() => onDelete(employee.id)"
                    color="error"/>
        </template>
    </v-card>

    <employee-edit-modal
        v-model="modal"
        :selected="selectedEmployee"
    />

    <v-dialog
        v-model="passwordModal"
        width="auto"
    >
        <v-card
            max-width="400"
            prepend-icon="mdi-form-textbox-password"
            title="Новый пароль"
        >
            <v-card-text>
                Скопируйте пароль от аккаунта и передайте оператору.

                <div class="text-center">
                    <v-chip prepend-icon="mdi-key-variant"
                            class="mt-5">
                        {{ $page.props.backData?.employeeAccountPassword }}
                    </v-chip>
                </div>
            </v-card-text>
            <template v-slot:actions>
                <v-btn
                    class="ms-auto"
                    prepend-icon="mdi-close"
                    text="Закрыть"
                    @click="onCopy"
                ></v-btn>
            </template>
        </v-card>
    </v-dialog>
</template>
