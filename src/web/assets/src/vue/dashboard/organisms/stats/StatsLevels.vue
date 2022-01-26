<template>
    <article class="bg-white p-4 rounded-xl mb-4">
        <p class="font-bold text-lg">Engagement level</p>
        <apexchart
            height="150"
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
                    position: 'top'
                },
                yaxis: {
                    show: false
                }
            })
            const series = ref([{
                    name: 'Engaged (1 module)',
                    data: [44]
                }, {
                    name: 'Sustained (2 modules)',
                    data: [53]
                }, {
                    name: 'Embedded (3+ modules)',
                    data: [3]
                }]
            )

            return { chartOptions, series }
        }
    })
</script>
