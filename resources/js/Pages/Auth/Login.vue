<script setup>
import {Head, useForm} from '@inertiajs/vue3';

const props = defineProps(['users', 'errors'])

const form = useForm({
    email: null,
    password: null
})

function auth() {
    form.post(route('login'))
}
</script>

<template>
    <Head title="Авторизация"/>

    <form @submit.prevent="auth">
        <div>
            <select style="width: 188px" v-model="form.email">
                <option :value="null">Выберите пользователя</option>
                <option v-for="user in users" :value="user.email">{{user.name}}</option>
            </select>
        </div>

        <div>
            <input type="password" v-model="form.password" placeholder="Пароль" style="width: 180px">
        </div>

        <div class="error-message" v-if="errors.email || errors.password">
            {{errors.email || errors.password}}
        </div>

        <div>
            <button type="submit">Войти</button>
        </div>
    </form>


</template>

<style scoped>
form {
    text-align: center;
    width: 180px;
    margin: 50px auto;
}

div {
    margin: 10px 0;
}

.error-message {
    color: red;
    font-size: 12px;
}
</style>
