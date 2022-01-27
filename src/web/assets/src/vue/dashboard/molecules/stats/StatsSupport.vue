<template>
    <article class="bg-white p-4 rounded-xl mb-4">
        <p class="font-bold text-lg">Follow on support</p>
        <apexchart
            v-if="!loading"
            height="800px"
            type="bar"
            :options="chartOptions"
            :series="series"
        ></apexchart>

        <span :class="[
            'block w-full text-center py-2',
            loading ? 'opacity-0' : ''
        ]">Showing what types of follow on support have been offered.</span>

        <span v-if="loading" class="block relative flex items-center justify-center w-full" style="height:800px">
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
    import {useDashboardStore} from "@/store/dashboard";
    import {storeToRefs} from "pinia";

    //https://www.npmjs.com/package/vue3-highcharts

    export default defineComponent({
        components: {
            apexchart: VueApexCharts,
        },
        setup(props){
            // const support = ref(JSON.parse(props.data))
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
                    categories: [],
                },
                yaxis: {
                    labels: {
                        style: {
                            fontSize: '14px',
                            fontFamily: 'system-ui,BlinkMacSystemFont,-apple-system,"Segoe UI","Roboto","Oxygen","Ubuntu","Cantarell","Fira Sans","Droid Sans","Helvetica Neue",sans-serif'
                        }
                    }
                },
                colors:  ['#2b63eb', '#113795', '#07205b']
            })
            const series = ref([
                {
                    name: 'Follow on support',
                    data: []
                }
            ])

            const store = useDashboardStore()
            const { loading, followOnSupport, followOnSupportOptions } = storeToRefs(store)

            watchEffect(() => {

                if(followOnSupport.value){
                    const support = followOnSupport.value.reduce(function (r, a) {
                        r[a.optionId] = r[a.optionId] || [];
                        r[a.optionId].push(a);
                        return r;
                    }, Object.create(null));

                    let options = []

                    for(const value in support){
                        options.push(followOnSupportOptions.value.find(op => op.id == value)?.name);
                    }

                    chartOptions.value.xaxis.categories = options
                    console.log("test",chartOptions.value.xaxis.categories,options)

                    series.value[0].data = []
                    Object.values(support).forEach((entry,i) => {
                        series.value[0].data.push(entry.length)
                    })

                }
            })

            return { chartOptions, series, loading }
        }
    })
</script>
