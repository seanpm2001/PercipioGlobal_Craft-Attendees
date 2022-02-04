<template>
    <!-- https://dribbble.com/shots/15774046-Nexudus-Corporate-VC-Dashboards -->
    <div class="grid grid-cols-2 xl:grid-cols-4 gap-4">

        <stat v-if="events && !loading" :value="events.length" :average="averageEvents()" type="events" description="trainings per month" />
        <stat-empty v-else />

        <stat v-if="attendees && !loading" :value="attendees.length" :average="averageAttendees()" type="attendees" description="attendees per training" />
        <stat-empty v-else />

        <stat v-if="attendees && !loading" :value="schools()" :average="averageSchools()" type="schools" description="schools per training" />
        <stat-empty v-else />

        <stat v-if="attendees && !loading" :value="priority()" :average="averagePrioritySchools()" type="priority schools" description="priority schools per training" />
        <stat-empty v-else />

    </div>
</template>

<script lang="ts">
    import {defineComponent} from "vue"
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
            const { loading, events, attendees, period } = storeToRefs(store)

            const schools = () => {
                const attendeeSchools = attendees.value.reduce(function (r, a) {
                    r[a.orgName] = r[a.orgName] || [];
                    r[a.orgName].push(a);
                    return r;
                }, Object.create(null));

                return Object.keys(attendeeSchools).length
            }

            const priority = () => {
                const prioritySchools = attendees.value.filter(attendee => attendee.priority === 1)

                return Object.keys(prioritySchools).length
            }

            const averagePrioritySchools = () => {
                const prioritySchools = attendees.value.filter(attendee => attendee.priority === 1)

                return Object.keys(prioritySchools).length / events.value.length
            }

            const averageEvents = () => {
                return Math.ceil(events.value.length/period.value)
            }

            const averageAttendees = () => {
                 const attendeeEvents = attendees.value.reduce(function (r, a) {
                    r[a.eventId] = r[a.eventId] || [];
                    r[a.eventId].push(a);
                    return r;
                }, Object.create(null));

                let average = Object.values(attendeeEvents).map(s => s.length)
                average = Math.ceil(average.reduce( ( p, c ) => p + c, 0 ) / average.length)

                return isNaN(average) ? 0 : average
            }

            const averageSchools = () => {
                const attendeeSchools = attendees.value.reduce(function (r, a) {
                    r[a.eventId] = r[a.eventId] || [];
                    r[a.eventId].push(a);
                    return r;
                }, Object.create(null));

                let uniqueAverage = 0
                for(const key in attendeeSchools){

                    const uniqueSchool = attendeeSchools[key].reduce(function (r, a) {
                        r[a.orgName] = r[a.orgName] || [];
                        r[a.orgName].push(a);
                        return r;
                    }, Object.create(null));

                    uniqueAverage += Object.keys(uniqueSchool).length
                }

                const average = Math.ceil(uniqueAverage / Object.keys(attendeeSchools)?.length)

                return isNaN(average) ? 0 : average
            }

            return { loading, events, attendees, schools, priority, averageEvents, averageAttendees, averageSchools, averagePrioritySchools }
        }
    })
</script>
