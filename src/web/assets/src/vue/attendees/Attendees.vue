<script lang="ts">

    // Async load the Vue 3 APIs we need from the Vue ESM
    import { defineComponent } from 'vue';
    import TabPaneAttendees from '@/vue/attendees/organisms/tab-panes/TabpaneAttendees.vue';

    export default defineComponent({
        components: {
            'tabpane-attendees': TabPaneAttendees
        }
    });

</script>
