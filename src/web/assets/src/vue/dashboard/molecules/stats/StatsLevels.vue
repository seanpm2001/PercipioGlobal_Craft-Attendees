<template>
    <article class="bg-white p-4 rounded-xl mb-4">
        <p class="font-bold text-lg">Engagement level</p>
        <apexchart
            v-if="!loading && attendees?.length > 0"
            height="150"
            type="bar"
            :options="chartOptions"
            :series="series"
        ></apexchart>

        <p v-if="!loading && attendees?.length === 0" class="text-xl w-full inline-block text-center text-gray-400">There is no data to preview</p>

        <span :class="[
            'block w-full text-center py-2',
            loading || attendees?.length === 0 ? 'opacity-0' : ''
        ]">Showing the split of engagement levels across all events in this time period</span>

        <span v-if="loading" class="block relative flex items-center justify-center w-full" style="height:150px">
            <svg class="animate-spin ml-2 mt-1 h-8 w-8 text-gray-400 inline-block -mt-px" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" style="margin-bottom: 0!important;">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        </span>
    </article>
</template>

<script lang="ts">
    import { defineComponent, ref, watchEffect } from "vue"
    import VueApexCharts from "vue3-apexcharts";
    import {useDashboardStore} from "@/store/dashboard";
    import {storeToRefs} from "pinia";

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
                colors:  ['#2b63eb', '#113795', '#07205b']
            })
            const series = ref([{
                    name: 'Engaged (1 module)',
                    data: [0]
                }, {
                    name: 'Sustained (2 modules)',
                    data: [0]
                }, {
                    name: 'Embedded (3+ modules)',
                    data: [0]
                }]
            )

            const store = useDashboardStore()
            const { loading, attendees } = storeToRefs(store)

            watchEffect(() => {

                if(attendees.value){
                    const attendeeDays = attendees.value.reduce(function (r, a) {
                        r[a.days] = r[a.days] || [];
                        r[a.days].push(a);
                        return r;
                    }, Object.create(null));
                    for(const key in Object.keys(attendeeDays)){
                        const index = key < 3 ? key : 2
                        series.value[index]['data'][0] = parseInt(series.value[index]['data'][0]) + Object.values(attendeeDays)[key].length
                    }
                }

                // if(attendees.value){
                //
                //     let levels = []
                //
                //     for(const key in attendees.value){
                //         for(const l in attendees.value[key]){
                //             levels[''+attendees.value[key][l].days] = (levels[''+attendees.value[key][l].days] ?? 0) + 1
                //         }
                //     }
                //
                //     for(const key in Object.keys(levels)){
                //         const index = parseInt(Object.keys(levels)[key]) < 3 ? parseInt(Object.keys(levels)[key]) : 2
                //
                //         series.value[index]['data'][0] = parseInt(series.value[index]['data'][0]) + parseInt(levels[Object.keys(levels)[key]])
                //     }
                // }
            })


            return { loading, chartOptions, series, attendees }
        }
    })
</script>
