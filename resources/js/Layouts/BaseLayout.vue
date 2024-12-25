<script setup>
import InternetStateChecker from "@/Components/InternetStateChecker.vue";
import StateLoader from "@/Components/StateLoader.vue";
import Navbar from "@/Components/Menu/Navbar.vue";
import {usePage} from "@inertiajs/vue3";

const page = usePage();

</script>

<template>
    <internet-state-checker/>

    <state-loader/>

    <v-app>
        <navbar/>

        <v-main>
            <v-container :class="page.props.containerClass">
                <slot />
            </v-container>
        </v-main>
    </v-app>
</template>
