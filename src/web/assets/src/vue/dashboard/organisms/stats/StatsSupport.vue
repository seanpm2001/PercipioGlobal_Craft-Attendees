<template>
    <article class="bg-white p-4 rounded-xl">
        <p class="font-bold text-lg">Follow on support</p>
        <apexchart
            height="400"
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
                id: "follow-on-support",
            },
            plotOptions: {
                bar: {
                    horizontal: true,
                    height: '10%'
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
            },
            yaxis: {
                labels: {
                    style: {
                        fontSize: '14px',
                        fontFamily: 'system-ui,BlinkMacSystemFont,-apple-system,"Segoe UI","Roboto","Oxygen","Ubuntu","Cantarell","Fira Sans","Droid Sans","Helvetica Neue",sans-serif'
                    }
                }
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
