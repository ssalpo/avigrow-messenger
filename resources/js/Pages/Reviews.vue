<script setup>
import Review from "@/Components/Review.vue";
import {ref, watch} from "vue";
import PageTitle from "@/Components/PageTitle.vue";
import SendAnswerModal from "@/Pages/Reviews/SendAnswerModal.vue";

const props = defineProps(['reviews', 'lastPage', 'accountId']);

let reviews = ref(props.reviews);
const currentPage = ref(1);
const isBusy = ref(false);

const selectedReview = ref(null)
const answerModal = ref(false)

function loadMore({done}) {
    if (isBusy.value === true) {
        return;
    }

    if (currentPage.value >= props.lastPage) {
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

const onSelect = (review) => {
    selectedReview.value = review

    answerModal.value = true
}

</script>

<template>
    <page-title text="Отзывы"/>

    <send-answer-modal
        :account-id="accountId"
        v-model="answerModal"
        :review="selectedReview"/>

    <v-infinite-scroll mode="manual"
                       :items="reviews"
                       :onLoad="loadMore">
        <template v-for="(review, index) in reviews"
                  :key="index">
            <Review @selected="onSelect"
                    :account-id="accountId"
                    class="mb-5"
                    :review="review"/>
            <v-divider/>
        </template>

        <template v-slot:load-more="{props}">
            <div class="text-center mt-3 mb-5">
                <v-btn variant="outlined"
                       size="x-small"
                       color="blue-darken-1"
                       v-bind="props">
                    Показать еще
                </v-btn>
            </div>
        </template>
    </v-infinite-scroll>
</template>
