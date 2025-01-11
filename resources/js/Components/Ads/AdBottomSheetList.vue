<script setup>
import {onMounted, ref, watch} from "vue";
import {debounce} from "lodash";

const model = defineModel()

const emits = defineEmits(['selected'])

const props = defineProps({
    accountId: {
        type: Number,
        required: true
    }
})

const searchText = ref()
const ads = ref([])
const filteredAds = ref([])

const loadAds = () => {
    axios.get(route('ads.index', props.accountId))
        .then((response) => {
            ads.value = response.data
            filteredAds.value = response.data
        })
}

const search = (text) => {
    let input = text.toLowerCase().trim()

    filteredAds.value = !input
        ? ads.value
        : ads.value.filter((e) => {
            return e.title.toLowerCase().includes(input) ||
                e.price.toString().includes(input) ||
                e.external_id.toString().includes(input)
        })
}

const onSelected = (ad) => {
    emits('selected', ad.url)

    model.value = false
}

onMounted(() => {
    if(!ads.value.length) {
        loadAds()
    }
})

watch(
    () => searchText.value,
    debounce((text) => search(text), 500)
)

watch(() => model.value, (status) => {
    if(status === false) {
        searchText.value = ''
    }
})
</script>

<template>
    <v-bottom-sheet v-model="model"
                    style="height: 100vh"
                    @close="model = false">
        <v-sheet style="height: 100vh">
            <v-container>
                <v-sheet class="d-flex align-center justify-between mb-5">
                    <v-sheet class="d-flex w-100 pe-4">
                        <v-icon
                            @click="model = false"
                            icon="mdi-arrow-left"
                            size="20"
                            class="mr-2"
                        />

                        <input
                            v-model="searchText"
                            type="text"
                            placeholder="Поиск по названию, цене и ID"
                            style="font-size: 15px; outline: none; width: 100%"/>
                    </v-sheet>
                </v-sheet>

                <v-sheet v-for="ad in filteredAds" @click="() => onSelected(ad)">
                    <div class="py-2 px-1 text-body-2"
                         style="word-break: break-word;">
                        <div class="font-weight-bold">
                            {{ ad.title }}
                        </div>

                        <div class="d-flex text-caption mt-1">
                            <div class="mr-5 opacity-90">Цена: {{ ad.price }} руб</div>
                            <div class="opacity-60 font-weight-thin">ID: {{ ad.external_id }}</div>
                        </div>
                    </div>
                    <v-divider/>
                </v-sheet>

            </v-container>
        </v-sheet>
    </v-bottom-sheet>
</template>

<style scoped>

</style>
