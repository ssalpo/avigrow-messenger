<script setup>
import {useForm, usePage} from "@inertiajs/vue3";
import {ref} from "vue";

const model = defineModel()

const props = defineProps({
    accountId: {
        type: Number,
        required: true
    },
    review: {
        required: true
    }
})

const form = useForm({
    message: ''
})

const send = () => {
    const url = route('reviews.answer', {account: props.accountId, review: props.review.id});

    form.post(url, {
        preserveScroll: true, preserveState: true, onSuccess: () => {
            const response = usePage().props.backData.reviewAnswerResponse

            props.review.answer = {
                id: response.id,
                text: form.message,
                status: "moderation",
                createdAt: response.createdAt
            }

            model.value = false
            form.reset()
        }
    })
}

const aiGenerateStart = ref(false)

const aiGenerator = async () => {
    aiGenerateStart.value = true;

    const url = route('reviews.ai-answer-generator', {account: props.accountId});
    const response = await axios.post(url, {text: props.review?.text, context: props.review?.item.title})

    form.message = response.data
    aiGenerateStart.value = false;
}
</script>

<template>
    <v-dialog
        v-model="model"
        transition="dialog-bottom-transition"
        fullscreen
    >
        <v-card>
            <v-toolbar height="50">
                <v-btn
                    size="small"
                    icon="mdi-close"
                    @click="model = false"
                ></v-btn>

                <v-toolbar-title class="text-subtitle-1">
                    Ответить на отзыв
                </v-toolbar-title>
            </v-toolbar>

            <v-card-text class="pt-10">
                <div class="text-caption mb-7 d-flex">
                    <div class="font-weight-bold mr-2">Отзыв:</div>
                    <div>
                        {{ review.text }}
                    </div>
                </div>

                <v-textarea
                    variant="outlined"
                    v-model="form.message"
                    label="Текст ответа"
                    :error-messages="form.errors.message"
                    :hide-details="!form.errors.message"
                    class="mb-1"
                ></v-textarea>
                <div class="text-caption opacity-80">
                    <span v-if="!form.message.length">Не более 2000 символов</span>

                    <span v-else>{{ form.message.length }} из 2000 символов</span>
                </div>

                <div class="mt-5 d-flex">
                    <v-btn
                        :loading="aiGenerateStart"
                        @click="aiGenerator"
                        variant="outlined"
                        prepend-icon="mdi-robot-outline"
                        color="warning"
                        text="AI Ответ"
                        class="mr-2"
                    />

                    <v-spacer />

                    <v-btn
                        @click="send"
                        prepend-icon="mdi-content-save-all-outline"
                        color="blue-darken-1"
                        text="Отправить"
                    />
                </div>
            </v-card-text>
        </v-card>
    </v-dialog>
</template>
