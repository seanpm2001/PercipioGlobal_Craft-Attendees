<template>
    <!-- https://dribbble.com/shots/15774046-Nexudus-Corporate-VC-Dashboards -->
    <div class="grid grid-cols-2 xl:grid-cols-4 gap-4">

        <stat :value="12" :average="4" type="event" description="trainings per month" />
        <stat :value="278" :average="23" type="attendees" description="attendees per training" />
        <stat :value="109" :average="9" type="schools" description="schools per training" />
        <stat :value="19" :average="9" type="priority schools" description="priority schools per training" />

    </div>
</template>

<script lang="ts">
    import {defineComponent} from "vue"
    import Stat from '@/vue/dashboard/atoms/stats/Stat.vue'

    export default defineComponent({
        components:
            {
            'stat': Stat
        }
    })
</script>
