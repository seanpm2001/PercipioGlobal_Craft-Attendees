<template>
    <stats-figures></stats-figures>
    <stats-levels></stats-levels>
    <stats-support></stats-support>
    <stats-completion></stats-completion>
</template>
<script lang="ts">

    // Async load the Vue 3 APIs we need from the Vue ESM
    import { defineComponent } from 'vue';
    import StatsCompletion from '@/vue/dashboard/molecules/stats/StatsCompletion.vue';
    import StatsFigures from '@/vue/dashboard/molecules/stats/StatsFigures.vue';
    import StatsLevels from '@/vue/dashboard/molecules/stats/StatsLevels.vue';
    import StatsSupport from '@/vue/dashboard/molecules/stats/StatsSupport.vue';
    import { storeToRefs } from 'pinia'
    import { useDashboardStore } from "@/store/dashboard"

    export default defineComponent({
        components: {
            'stats-completion': StatsCompletion,
            'stats-figures': StatsFigures,
            'stats-levels': StatsLevels,
            'stats-support': StatsSupport,
        },
        props: {
            site: {
                type: String,
                required: true
            },
            // ids: {
            //     type: String,
            //     required: true
            // }
        },
        setup(props){
            const store = useDashboardStore();

            store.site = props.site == '*' ? 2 : props.site

            store.fetchEvents(props.ids)
        }
    });

</script>
