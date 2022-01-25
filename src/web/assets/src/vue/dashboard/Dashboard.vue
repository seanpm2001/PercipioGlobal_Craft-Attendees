<script lang="ts">

    // Async load the Vue 3 APIs we need from the Vue ESM
    import { defineComponent } from 'vue';
    import StatsFigures from '@/vue/dashboard/organisms/stats/StatsFigures.vue';
    import StatsLevels from '@/vue/dashboard/organisms/stats/StatsLevels.vue';

    export default defineComponent({
        components: {
            'stats-figures': StatsFigures,
            'stats-levels': StatsLevels,
        }
    });

</script>
