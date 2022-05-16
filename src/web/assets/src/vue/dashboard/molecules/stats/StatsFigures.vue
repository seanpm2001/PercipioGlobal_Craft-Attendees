<template>
    <!-- https://dribbble.com/shots/15774046-Nexudus-Corporate-VC-Dashboards -->
    <div class="grid grid-cols-2 xl:grid-cols-4 gap-4">

        <stat v-if="events && !loading" :value="parseInt(totals['events'] ?? events.length)" :average="parseFloat(averageEvents())" type="events" description="trainings per month" />
        <stat-empty v-else />

        <stat v-if="attendees && !loading" :value="parseInt(totals['attendees'] ?? attendees.length)" :average="parseFloat(averageAttendees())" type="attendees" description="attendees per training" />
        <stat-empty v-else />

        <stat v-if="attendees && !loading" :value="parseInt(totals['schools'] ?? 0)" :average="parseFloat(averageSchools())" type="schools" description="schools per training" />
        <stat-empty v-else />

        <stat v-if="attendees && !loading" :value="priority" :average="parseFloat(averagePrioritySchools())" type="priority schools" description="priority schools per training" />
        <stat-empty v-else />

    </div>
</template>

<script lang="ts">
    import {defineComponent, ref, watchEffect} from "vue"
    import Stat from '@/vue/dashboard/atoms/stats/Stat.vue'
    import StatEmpty from '@/vue/dashboard/atoms/stats/StatEmpty.vue'
    import { useDashboardStore } from '@/store/dashboard'
    import { storeToRefs } from 'pinia'

    export default defineComponent({
        components: {
            'stat': Stat,
            'stat-empty': StatEmpty
        },
        setup(){
            const store = useDashboardStore();
            const { loading, events, attendees, period, totals } = storeToRefs(store)
            const priority = ref(0)

            const averagePrioritySchools = () => {
                const AVprioritySchools = parseFloat(priority.value / totals.value['events'])
                return isNaN(AVprioritySchools) ? 0 : parseFloat(AVprioritySchools).toFixed(2).replace(/[.,]00$/, "");
            }

            const averageEvents = () => {
                const AVevents = Math.ceil(events.value.length/period.value)
                return isNaN(events) ? 0 : events.toFixed(2).replace(/[.,]00$/, "");
            }

            const averageAttendees = () => {
                const AVattendees = totals.value['attendees']/totals.value['events']
                return isNaN(AVattendees) ? 0 : parseFloat(AVattendees).toFixed(2).replace(/[.,]00$/, "");
            }

            const averageSchools = () => {
                const AVschools = totals.value['schools']/totals.value['events']
                return isNaN(AVschools) ? 0 : parseFloat(AVschools).toFixed(2).replace(/[.,]00$/, "");
            }


            watchEffect(() => {
                if(attendees.value){
                    const schools = attendees.value.filter((e, i) => attendees.value.findIndex(a => a['orgName'] === e['orgName']) === i)
                    priority.value = schools.filter(a => a.priority == 1).length
                }
            })


            return { loading, events, attendees, totals, priority, averageEvents, averageAttendees, averageSchools, averagePrioritySchools }
        }
    })
</script>
