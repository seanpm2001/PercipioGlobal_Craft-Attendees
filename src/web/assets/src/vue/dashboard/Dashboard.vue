<script lang="ts">

    // Async load the Vue 3 APIs we need from the Vue ESM
    import { defineComponent } from 'vue';
    import StatsFigures from '@/vue/dashboard/organisms/stats/StatsFigures.vue';

    export default defineComponent({
        components: {
            'stats-figures': StatsFigures,
        }
    });

</script>
