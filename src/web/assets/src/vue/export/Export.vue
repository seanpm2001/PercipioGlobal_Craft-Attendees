<script lang="ts">

    // Async load the Vue 3 APIs we need from the Vue ESM
    import { defineComponent } from 'vue'
    import FormExport from '@/vue/export/organisms/forms/FormExport.vue'

    export default defineComponent({
        components: {
            'form-export': FormExport,
        },
    });

</script>
