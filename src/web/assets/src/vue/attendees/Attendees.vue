<script lang="ts">

    // Async load the Vue 3 APIs we need from the Vue ESM
    import { defineComponent } from 'vue';
    import TabPaneAttendees from '@/vue/attendees/organisms/tab-panes/TabpaneAttendees.vue';
    import ListCsv from '@/vue/attendees/organisms/lists/ListCsv.vue';

    export default defineComponent({
        components: {
            'list-csv': ListCsv,
            'tabpane-attendees': TabPaneAttendees
        }
    });

</script>
