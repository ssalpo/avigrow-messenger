<script setup>
let model = defineModel()

const durations = {
    5: "5с",
    15: "15с",
    30: "30с",
    60: "1м",
    180: "3м",
    300: "5м",
    600: "10м",
    1800: "30м"
}

let durationValues = Object.keys(durations).map(key => ({ key, value: durations[key] }));

</script>

<template>
    <v-select
        v-model="model"
        :items="[{key: '0', value: 'Без задержки'}, ...durationValues]"
        item-title="value"
        item-value="key"
    ></v-select>
</template>
