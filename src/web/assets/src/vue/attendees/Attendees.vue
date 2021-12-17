<script lang="ts">

// Async load the Vue 3 APIs we need from the Vue ESM
import { defineComponent } from 'vue';
import FormAttendee from '@/vue/attendees/organisms/forms/FormAttendee.vue';

export default defineComponent({
    components: {
        'form-attendee': FormAttendee
    }
});

</script>
