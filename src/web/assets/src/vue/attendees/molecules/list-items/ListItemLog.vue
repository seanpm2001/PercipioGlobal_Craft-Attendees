<template>
    <div
        tabindex="0"
        :class="[
            'w-full border-l-2 border-solid',
            expanded ? 'bg-blue-100 bg-opacity-10 border-red-300' : 'border-white',
        ]"
    >
        <div class="grid grid-cols-8 py-2 items-center w-full items-center">
            <div class="col-span-2 p-3 font-bold text-red-600 cursor-pointer" @click="toggle">
                ⚠ {{ log.length }} error(s) occured
            </div>
            <div class="col-span-3 p-3 cursor-pointer" @click="toggle">
                {{ filename }}
            </div>
            <div class="col-span-2 p-3 cursor-pointer" @click="toggle">
                Last occured: {{ log[0].dateCreated }}
            </div>
            <div class="p-3 text-blue-800 cursor-pointer" @click="toggle">
                {{ expanded ? 'Hide' : 'Show' }} errors
            </div>
        </div>

        <div class="px-10 py-6 relative" v-if="expanded">

            <div class="flex items-center relative mb-4">
                <h3 class="text-lg inline-block flex-grow">{{ filename }} error(s) details</h3>
                <span :class="[
                    'text-xs flex items-center mt-2 transition ease-in-out',
                    loading ? 'opacity-100' : 'opacity-0'
                ]">
                    <svg class="animate-spin h-4 w-4 text-gray-600 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span>Pulling new logs</span>
                </span>
            </div>

            <div class="w-full grid grid-cols-9 border-b border-gray-200 border-solid pb-2 mb-2">
                <div class="py-3 text-left text-xs font-semibold text-gray-600 uppercase flex flex-nowrap items-center">
                    CSV Line
                </div>
                <div class="col-span-2 py-3 text-left text-xs font-semibold text-gray-600 uppercase flex flex-nowrap items-center">
                    Attendee
                </div>
                <div class="col-span-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase flex flex-nowrap items-center">
                    Error
                </div>
                <div class="col-span-2 py-3 text-left text-xs font-semibold text-gray-600 uppercase flex flex-nowrap items-center">
                    Date Created
                </div>
            </div>

            <template v-for="error in logs">
                <div class="w-full grid grid-cols-9 py-2 border-b border-solid border-gray-100" v-for="errorLine in JSON.parse(error.message)">
                    <div>{{ error.line }}</div>
                    <div class="col-span-2">{{ error.attendee }}</div>
                    <div class="col-span-4">{{ errorLine[0] }}</div>
                    <div class="col-span-2">{{ error.dateCreated }}</div>
                </div>
            </template>
        </div>
    </div>
</template>

<script>

    import { defineComponent, ref, watchEffect } from 'vue'
    import { useLogsStore } from '@/store/logs'
    import { storeToRefs } from 'pinia'

    export default defineComponent({
        props: {
            log: {
                type: Object,
                required: true
            },
            filename: {
                type: String,
                required: true
            }
        },
        setup(props){
            const store = useLogsStore()
            const { loading } = storeToRefs(store)
            const expanded = ref(false)
            const logs = ref({})

            const toggle = () => {
                expanded.value = !expanded.value
            }

            watchEffect(() => {
                if(props.log){
                    logs.value = props.log.sort((a,b) => (a.line < b.line) ? 1 : ((b.line > a.line) ? -1 : 0))
                }
            })

            return { logs, expanded, loading, toggle }
        }
    })

</script>
