<script setup>

import Navbar from "@/Components/Menu/Navbar.vue";
import {Head} from "@inertiajs/vue3";
import Review from "@/Components/Review.vue";
import {onMounted, ref} from "vue";

const props = defineProps(['reviews', 'lastPage', 'accountId']);

let reviews = ref(props.reviews);
const currentPage = ref(1);
const isBusy = ref(false);

function loadMore({ done }) {
    if (isBusy.value === true) {
        return;
    }

    if(currentPage.value >= props.lastPage) {
        done('empty')
        return
    }

    isBusy.value = true;

    currentPage.value++;

    axios.get(`/accounts/${props.accountId}/reviews?page=${currentPage.value}`)
        .then((response) => {
            reviews.value = reviews.value.concat(response.data)

            done('ok')
        })
        .finally(() => isBusy.value = false)
}

</script>

<template>
    <Head title="Отзывы" />

    <v-container>
        <h3 class="text-h5 mt-3 mb-5">Отзывы</h3>

        <v-infinite-scroll mode="manual" :items="reviews" :onLoad="loadMore">
            <template v-for="(review, index) in reviews" :key="index">
                <Review class="mb-5" :review="review" />
                <v-divider />
            </template>

            <template v-slot:load-more="{props}">
                <div class="text-center mt-3 mb-5">
                    <v-btn variant="outlined" size="x-small" color="primary" v-bind="props">
                        Показать еще
                    </v-btn>
                </div>
            </template>
        </v-infinite-scroll>
    </v-container>

</template>
