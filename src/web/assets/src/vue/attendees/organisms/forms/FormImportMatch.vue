<template>

    <form ref="form" method="post" accept-charset="UTF-8">
        <input type="hidden" name="CRAFT_CSRF_TOKEN" :value="csrf" />
        <input type="hidden" name="action" value="craft-attendees/csv/import-csv-columns">
        <input type="hidden" name="filename" :value="filepath" />
        <input type="hidden" name="file" :value="filename" />
        <input type="hidden" name="event" :value="event" />
        <input type="hidden" name="site" :value="site" />
        <template v-for="(column, i) in columns">
            <input
                type="hidden"
                :name="`columns[${i}]`"
                :value="column.value"
            />
        </template>
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
            type: Number,
            required: true
        },
        site: {
            type: String,
            required: true
        },
        filepath: {
            type: String,
            required: true
        },
        filename: {
            type: String,
            required: true,
        },
        columns: {
            type: Object,
            required: true
        },
        submitForm: {
            type: Boolean
        }
    },
    setup(props){

        const form = ref(null)

        watchEffect(() => {
            if(props.submitForm && form.value){
                form.value.submit()
            }
        })

        return { form }
    }

})
</script>
