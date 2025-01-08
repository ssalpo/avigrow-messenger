<script setup>
import PageTitle from "@/Components/PageTitle.vue";
import {ref, watch} from "vue";
import VueApexCharts from "vue3-apexcharts"

const props = defineProps(['accounts', 'analytics'])


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
        },
        min: 0
    },
})

const series = ref([])

watch(() => props.analytics, (analytics) => {

    for (let [accountId, items] of Object.entries(analytics)) {
        series.value.push({
            name: props.accounts[accountId],
            data: generateChartData(1, 2024, items)
        })
    }

}, {immediate: true})
</script>

<template>
    <page-title text="Аналитика"/>

    <vue-apex-charts type="area"
                     height="350"
                     :options="chartOptions"
                     :series="series"/>
</template>
