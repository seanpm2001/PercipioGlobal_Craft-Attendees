<template>
    <article class="bg-white p-4 rounded-xl mb-4">
        <p class="font-bold text-lg">Engagement Data Completion</p>
        <apexchart
            height="150"
            type="bar"
            :options="chartOptions"
            :series="series"
        ></apexchart>

        <span class="block w-full text-center py-2"><strong>26</strong> total training events, <strong>12</strong> (46,15%) have engagement data.</span>
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
                id: "engagement-levels",
                stacked: true,
                stackType: '100%'
            },
            plotOptions: {
                bar: {
                    horizontal: true,
                }
            },
            theme: {
                palette: 'palette3'
            },
            legend: {
                show: false
            },
            xaxis: {
                labels: {
                    show: false
                },
                axisBorder: {
                    show: false,
                },
                axisTicks: {
                    show: false
                }
            },
            yaxis: {
                show: false
            },
            colors:  ['#2b63eb', '#113795']
        })
        const series = ref([{
                name: 'Engaged (1 module)',
                data: [44]
            }, {
                name: 'Sustained (2 modules)',
                data: [53]
            }]
        )

        return { chartOptions, series }
    }
})
</script>
