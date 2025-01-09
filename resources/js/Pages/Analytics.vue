<script setup>
import PageTitle from "@/Components/PageTitle.vue";
import {computed, ref, watch} from "vue";
import VueApexCharts from "vue3-apexcharts"
import {useForm} from "@inertiajs/vue3";
import VueDatePicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css'

const props = defineProps(['accountList', 'analytics', 'filters'])

const autocompleteAccounts = computed(() => {
    return Object.keys(props.accountList).map(key => ({key, value: props.accountList[key]}))
})

const generateMonthDates = (month, year) => {
    const months = ['янв.', 'фев.', 'мар.', 'апр.', 'май.', 'июн.', 'июл.', 'авг.', 'сен.', 'окт.', 'ноя.', 'дек.'];
    const monthIndex = month - 1; // В JavaScript месяцы начинаются с 0
    const daysInMonth = new Date(year, month, 0).getDate(); // Получаем количество дней в месяце

    const dates = [];
    for (let day = 1; day <= daysInMonth; day++) {
        dates.push(`${day} ${months[monthIndex]} ${year}`);
    }
    return dates;
};

const generateChartData = (month, year, items) => {
    const daysInMonth = new Date(year, month, 0).getDate(); // Получаем количество дней в месяце

    const chartData = [];

    for (let day = 1; day <= daysInMonth; day++) {
        chartData.push(items[day] || 0);
    }

    return chartData;
};

const monthCategories = generateMonthDates(1, 2024);


const chartOptions = ref({
    chart: {
        type: 'area',
        toolbar: {
            show: true
        },
        zoom: {
            enabled: true,
            type: 'x',
            resetIcon: {
                offsetX: -10,
                offsetY: 0,
                fillColor: '#fff',
                strokeColor: '#37474F'
            },
            selection: {
                background: '#90CAF9',
                border: '#0D47A1'
            }
        }
    },
    dataLabels: {
        enabled: false
    },
    legend: {
        show: true,
        horizontalAlign: 'left'
    },
    stroke: {
        curve: 'smooth'
    },
    xaxis: {
        categories: monthCategories,
    },
    tooltip: {
        y: {
            formatter: function (val) {
                return `${val} Новых диалогов`
            }
        },
    },
    yaxis: {
        tickAmount: 8, // Количество делений на оси Y
        labels: {
            formatter: function (value) {
                return value.toFixed(); // Оставляем только целые числа
            }
        }
    },
})

const series = ref([])

const filter = useForm({
    accounts: [],
    date: {
        month: new Date().getMonth(),
        year: new Date().getFullYear()
    }
})

watch(() => props.analytics, (analytics) => {

    for (let [accountId, items] of Object.entries(analytics)) {
        series.value.push({
            name: props.accountList[accountId],
            data: generateChartData(1, 2024, items)
        })
    }

}, {immediate: true})

watch(() => props.filters, () => {
    filter.accounts = props.filters?.accounts || []
    filter.date = {
        month: props.filters?.date?.month ?? new Date().getMonth(),
        year: props.filters?.date?.year ?? new Date().getFullYear()
    }
}, {immediate: true})

const onFilter = (status) => {
    if ((status === false || typeof status === 'undefined') && filter.isDirty) {
        filter.get(route('analytics'))
    }
}

</script>

<template>
    <page-title text="Аналитика"/>

    <v-row class="mb-4">
        <v-col cols="12" sm="6">
            <v-autocomplete
                hide-details
                v-model="filter.accounts"
                variant="outlined"
                density="compact"
                label="Выбранные аккаунты"
                item-title="value"
                item-value="key"
                multiple
                :items="autocompleteAccounts"
                @update:menu="onFilter"
            ></v-autocomplete>
        </v-col>
        <v-col cols="12" sm="6">
            <VueDatePicker
                @closed="onFilter"
                format="MM-yyyy"
                v-model="filter.date"
                locale="ru"
                month-picker
                auto-apply/>
        </v-col>
    </v-row>

    <vue-apex-charts type="area"
                     height="350"
                     :options="chartOptions"
                     :series="series"/>
</template>
