<template>
    <article class="bg-white p-4 rounded-xl mb-4">
        <p class="font-bold text-lg">Engagement Data Completion</p>

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
        ]"><strong>{{ totals['events'] }}</strong> total training events, <strong>{{ allAttendees }}</strong> have attendees and ({{ average }}%) are verified.</span>

        <span v-if="loading" class="block relative flex items-center justify-center w-full" style="height:150px">
            <svg class="animate-spin ml-2 mt-1 h-8 w-8 text-gray-400 inline-block -mt-px" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" style="margin-bottom: 0!important;">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        </span>
    </article>
</template>

<script lang="ts">
    import {defineComponent, ref, watchEffect} from "vue"
    import VueApexCharts from "vue3-apexcharts";
    import {useTrainingsStore} from "../../../../store/trainings";
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
                    name: 'Attendees (unverified)',
                    data: [0]
                }, {
                    name: 'Attendees (verified)',
                    data: [0]
                }]
            )

            const store = useTrainingsStore()
            const { loading, totals, attendees, unverifiedAttendees } = storeToRefs(store)
            const allAttendees = ref(0)
            const totalAttendees = ref(0)
            const totalAttendeesAreApproved = ref(0)
            const totalAttendeesAreDisapproved = ref(0)
            const totalAttendeesAreDispproved = ref(0)
            const average = ref(0)

            watchEffect(() => {

                if(attendees.value){

                    let completion = []
                    for(const key in attendees.value){
                        completion[''+attendees.value[key].eventId] = (completion[''+attendees.value[key].eventId] ?? 0) + 1
                    }

                    let completionApproved = []
                    for(const key in unverifiedAttendees.value){
                        completionApproved[''+unverifiedAttendees.value[key].eventId] = (completionApproved[''+unverifiedAttendees.value[key].eventId] ?? 0) + 1
                    }

                    totalAttendeesAreApproved.value = Object.keys(completion).length

                    let unapprovedCount = 0
                    for(const key in completionApproved){
                        if(Object.keys(completion).indexOf(key) < 0){
                            unapprovedCount++
                        }
                    }

                    allAttendees.value = Object.keys(completion).length + unapprovedCount
                    totalAttendeesAreApproved.value = attendees.value.length
                    totalAttendeesAreDispproved.value = unverifiedAttendees.value.length

                    average.value = ((totalAttendeesAreApproved.value/(totalAttendeesAreApproved.value + totalAttendeesAreDispproved.value))*100).toFixed(2).replace(/[.,]00$/, "")

                    series.value[0]['data'][0] = totalAttendeesAreDispproved.value
                    series.value[1]['data'][0] = totalAttendeesAreApproved.value

                }
            })

            return { loading, chartOptions, series, totals, average, allAttendees, totalAttendeesAreApproved, totalAttendeesAreDispproved, attendees }
        }
    })
</script>
