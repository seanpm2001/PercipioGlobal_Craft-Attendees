<script lang="ts">

    // Async load the Vue 3 APIs we need from the Vue ESM
    import { defineComponent } from 'vue';
    import StatusEngagement from '@/vue/trainings/molecules/statusses/StatusEngagement.vue';

    export default defineComponent({
        components: {
            'status-engagement': StatusEngagement
        }
    });

</script>
