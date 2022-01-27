<template>
    <!-- https://dribbble.com/shots/15774046-Nexudus-Corporate-VC-Dashboards -->
    <div class="grid grid-cols-2 xl:grid-cols-4 gap-4">

        <stat v-if="events && !loading" :value="events.length" :average="averageEvents()" type="event" description="trainings per month" />
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
    import {useDashboardStore} from "@/store/dashboard";
    import {storeToRefs} from "pinia";

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
                const attendeePrioritySchools = attendees.value.reduce(function (r, a) {
                    r[a.priority] = r[a.priority] || [];
                    r[a.priority].push(a);
                    return r;
                }, Object.create(null));

                return isNaN(Object.keys(attendeePrioritySchools).length) ? Object.keys(attendeePrioritySchools).length : 0
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

                 return Object.keys(attendeeEvents).length
            }

            const averageSchools = () => {
                const attendeeSchools = attendees.value.reduce(function (r, a) {
                    r[a.orgName] = r[a.orgName] || [];
                    r[a.orgName].push(a);
                    return r;
                }, Object.create(null));

                let average = Object.values(attendeeSchools).map(s => s.length)
                average = Math.ceil(average.reduce( ( p, c ) => p + c, 0 ) / average.length)

                return isNaN(average) ? 0 : average
            }

            const averagePrioritySchools = () => {
                let attendeeSchools = attendees.value.filter(at => at.priority === 1)

                attendeeSchools = attendeeSchools.reduce(function (r, a) {
                    r[a.eventId] = r[a.eventId] || [];
                    r[a.eventId].push(a);
                    return r;
                }, Object.create(null));

                let average = Object.values(attendeeSchools).map(s => s.length)
                average = Math.ceil(average.reduce( ( p, c ) => p + c, 0 ) / average.length)

                return isNaN(average) ? 0 : average

            }

            return { loading, events, attendees, schools, priority, averageEvents, averageAttendees, averageSchools, averagePrioritySchools }
        }
    })
</script>
