<template>
    <article class="bg-white p-4 rounded-xl">
        <p class="font-bold text-lg">Follow on support</p>
        <apexchart
            type="bar"
            :options="chartOptions"
            :series="series"
        ></apexchart>
    </article>
</template>

<script lang="ts">
import { defineComponent, ref } from "vue"
import VueApexCharts from "vue3-apexcharts";

//https://www.npmjs.com/package/vue3-highcharts

export default defineComponent({
    components: {
        apexchart: VueApexCharts,
    },
    setup(){
        const chartOptions = ref({
            chart: {
                id: "follow-on-supprt",
            },
            plotOptions: {
                bar: {
                    horizontal: true
                }
            },
            theme: {
                palette: 'palette3'
            },
            legend: {
                position: 'top'
            },
            xaxis: {
                categories: ['South Korea', 'Canada', 'United Kingdom', 'Netherlands', 'Italy', 'France', 'Japan',
                    'United States', 'China', 'Germany'
                ],
            }
        })
        const series = ref([{
                data: [400, 430, 448, 470, 540, 580, 690, 1100, 1200, 1380]
            }]
        )

        return { chartOptions, series }
    }
})
</script>
