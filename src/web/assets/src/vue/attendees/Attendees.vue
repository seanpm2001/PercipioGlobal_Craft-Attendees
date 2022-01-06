<script lang="ts">

    // Async load the Vue 3 APIs we need from the Vue ESM
    import { defineComponent } from 'vue';
    import ButtonAdd from '@/vue/attendees/atoms/buttons/ButtonAdd.vue';
    import ListAttendees from '@/vue/attendees/organisms/lists/ListAttendees.vue';
    import PopupAddAttendee from "@/vue/attendees/organisms/popups/PopupAddAttendee.vue";

    export default defineComponent({
        components: {
            'button-add': ButtonAdd,
            'popup-add-attendee': PopupAddAttendee,
            'list-attendees': ListAttendees
        }
    });

</script>
