<template>

    <div class="grid grid-cols-9 bg-gray-100 py-2 items-center">
        <div class="p-3 text-left text-xs font-semibold text-gray-600 uppercase flex flex-nowrap items-center">
            Status
        </div>
        <div class="p-3 text-left text-xs font-semibold text-gray-600 uppercase flex flex-nowrap items-center">
            Errors
        </div>
        <div class="p-3 text-left text-xs font-semibold text-gray-600 uppercase flex flex-nowrap items-center">
            Success
        </div>
        <div class="p-3 text-left text-xs font-semibold text-gray-600 uppercase">
            Total lines
        </div>
        <div class="col-span-2 p-3 text-left text-xs font-semibold text-gray-600 uppercase">
            File
        </div>
        <div class="col-span-2 p-3 text-left text-xs font-semibold text-gray-600 uppercase">
            Date
        </div>
        <div>&nbsp;</div>
    </div>

    <div class="rounded-xl bg-white overflow-hidden min-h-16">
        <div v-if="Object.keys(groupedLogs).length > 0" class="w-ful">
            <article
                v-for="(log, file, i) in groupedLogs"
                :class="[
                    Object.keys(groupedLogs).length-1 !== i ? 'border-b border-gray-100 border-solid' : ''
                ]"
            >
                <list-item-log :log="log" :filename="log[0].filename ?? file"/>
            </article>
        </div>
        <svg v-if="Object.keys(groupedLogs).length === 0 && loading" class="animate-spin ml-4 mt-5 mb-3 h-6 w-6 text-gray-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>

        <p class="p-6" v-if="Object.keys(groupedLogs).length === 0 && !loading">There are currently no logs for this event</p>
    </div>

</template>

<script lang="ts">
    import {defineComponent, ref, watchEffect, onUnmounted} from 'vue'
    import { useLogsStore } from '@/store/logs'
    import { storeToRefs } from 'pinia'
    import ListItemLog from '@/vue/attendees/molecules/list-items/ListItemLog.vue';
    import ListItemAttendee from "*.vue";

    export default defineComponent({
        components: {
            'list-item-log': ListItemLog,
        },
        props: {
            event: {
                type: String,
                required: true
            },
        },
        setup(props){
            const store = useLogsStore()
            const { logs, loading } = storeToRefs(store)
            const limit = ref(100)
            const offset = ref(0)
            const groupedLogs = ref({})
            const interval = ref(null);

            store.fetchLogs(props.event, limit.value, offset.value)

            watchEffect(() => {

                if(logs.value){
                    groupedLogs.value = logs.value.reduce((r, a) => {
                        r[a.filepath] = [...r[a.filepath] || [], a];
                        return r;
                    }, {});
                }
            })

            onUnmounted(() => {
                clearInterval(interval.value)
            })

            interval.value = setInterval(() => {
                store.fetchLogs(props.event, limit.value, offset.value)
            }, 2000)

            return { groupedLogs, loading }
        }
    })
</script>
