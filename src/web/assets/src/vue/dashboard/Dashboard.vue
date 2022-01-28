<script lang="ts">

    // Async load the Vue 3 APIs we need from the Vue ESM
    import { defineComponent } from 'vue'
    import HeaderDashboard from '@/vue/dashboard/molecules/headers/HeaderDashboard.vue'
    import StatsDashboard from '@/vue/dashboard/organisms/stats/StatsDashboard.vue'

    export default defineComponent({
        components: {
            'header-dashboard': HeaderDashboard,
            'stats-dashboard': StatsDashboard,
        },
    });

</script>
