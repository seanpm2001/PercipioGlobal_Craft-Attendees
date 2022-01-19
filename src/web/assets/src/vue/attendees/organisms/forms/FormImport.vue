<template>

    <form id="retour-import-form" method="post" accept-charset="UTF-8" enctype="multipart/form-data">
        <input type="hidden" name="CRAFT_CSRF_TOKEN" :value="csrf" />
        <input type="hidden" name="action" value="craft-attendees/csv/import">
        <input type="hidden" name="event" :value="event" />
        <input type="file" class="fixed top-full" name="file" id="attendee-csv-file" accept=".csv" onchange="this.form.submit();" />
        <label for="attendee-csv-file" class="bg-gray-300 text-gray-800 font-bold py-2 px-3 text-sm rounded-lg cursor-pointer inline-block">Import CSV File</label>
    </form>

</template>

<script lang="ts">
import {defineAsyncComponent, defineComponent, ref, watchEffect} from 'vue'
import { useModelWrapper } from '@/js/utils/modelWrapper'

export default defineComponent({

    props: {
        csrf: {
            type: String,
            required: true,
        },
        event: {
            type: String,
            required: true
        }
    },

})
</script>
