<script lang="ts">

    // Async load the Vue 3 APIs we need from the Vue ESM
    import { defineComponent } from 'vue';
    import StatsFigures from '@/vue/dashboard/organisms/stats/StatsFigures.vue';
    import StatsLevels from '@/vue/dashboard/organisms/stats/StatsLevels.vue';
    import StatsSupport from '@/vue/dashboard/organisms/stats/StatsSupport.vue';

    export default defineComponent({
        components: {
            'stats-figures': StatsFigures,
            'stats-levels': StatsLevels,
            'stats-support': StatsSupport,
        }
    });

</script>
