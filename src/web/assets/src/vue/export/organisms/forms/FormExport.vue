<template>
    <form
        ref="form"
        method="post"
        action=""
        accept-charset="UTF-8"
        @submit="validateForm"
        novalidate
    >
        <div class="flex items-center mb-8">
            <h2>Export data from the trainings</h2>
            <div class="flex-grow text-right">
                <button class="bg-emerald-300 text-emerald-800 font-bold py-2 px-3 text-sm rounded-lg cursor-pointer inline-block mt-5">Export data</button>
            </div>
        </div>

        <input type="hidden" name="action" value="craft-attendees/csv/export">
        <input type="hidden" name="CRAFT_CSRF_TOKEN" :value="csrf">
        <input type="hidden" name="site" :value="site">
        <!--input type="hidden" name="start" :value="start">
        <input type="hidden" name="end" :value="end"-->

        <div class="grid grid-cols-5">
            <label class="pr-4">
                <span class="text-xs font-bold text-gray-400 block mb-1">Select a school</span>
                <select name="school" class="block h-10 px-1 rounded-md bg-gray-100 w-full border-none">
                    <option default value="all">All schools</option>
                    <option default value="prior">Prior schools</option>
                </select>
            </label>
            <label class="pr-4">
                <span class="text-xs font-bold text-gray-400 block mb-1">Select an export type</span>
                <select name="type" class="block h-10 px-1 rounded-md bg-gray-100 w-full border-none">
                    <option default value="attendee">Attendee Data</option>
                    <option default value="event">Event ID reference table</option>
                    <option default value="subscriptions">Mailing list subscribers</option>
                    <option default value="school-attendee">School data per attendee</option>
                    <option default value="school-unique">School data unique</option>
                </select>
            </label>
            <label class="col-span-2 pr-4">
                <span class="text-xs font-bold text-gray-400 block mb-1">Time period</span>
                <div class="flex w-full">
                    <div class="inline-block flex-grow">
                        <div class="flex">
                            <span class="text-xs text-gray-400 inline-block">From</span>
                            <input type="date" v-model="startDate" name="start" :max="yesterday" :class="[
                                'px-2 flex-grow py-2 text-sm text-gray-600 appearance-none box-border bg-gray-100 rounded-lg border-solid',
                                errors?.startDate ? 'border-red-300' : 'border-gray-100'
                            ]"/>
                        </div>
                        <span v-if="errors?.startDate" class="text-xs p-2 inline-block font-bold text-red-300">{{errors?.startDate}}</span>
                    </div>
                    <div class="inline-block flex-grow">
                        <div class="flex">
                            <span class="text-xs text-gray-400 inline-block">To</span>
                            <input type="date" v-model="endDate" name="end" :max="today" :class="[
                                'flex-grow px-2 py-2 text-sm text-gray-600 appearance-none box-border bg-gray-100 rounded-lg border-solid',
                                errors?.endDate ? 'border-red-300' : 'border-gray-100'
                            ]"/>
                        </div>
                        <span v-if="errors?.endDate" class="text-xs p-2 inline-block font-bold text-red-300">{{errors?.endDate}}</span>
                    </div>
                </div>
                <p class="mt-0 text-xs">Predefined selections:
                    <span class="text-xs text-blue-600 cursor-pointer" @click.prevent="setPredefined('3m')">Last&nbsp;3&nbsp;months</span>,
                    <span class="text-xs text-blue-600 cursor-pointer" @click.prevent="setPredefined('6m')">Last&nbsp;6&nbsp;months</span>,
                    <span class="text-xs text-blue-600 cursor-pointer" @click.prevent="setPredefined('12m')">Last&nbsp;year</span>,
                    <span class="text-xs text-blue-600 cursor-pointer" @click.prevent="setPredefined('cay')">Current&nbsp;academic&nbsp;year</span> or
                    <span class="text-xs text-blue-600 cursor-pointer" @click.prevent="setPredefined('lay')">Last&nbsp;academic&nbsp;year</span>
                </p>
            </label>
            <label>
                <span class="text-xs font-bold text-gray-400 block mb-1">Tag</span>
                <!--input v-model="tag" name="tag" :class="[
                    'px-2 w-full py-2 text-sm text-gray-600 appearance-none box-border bg-gray-100 rounded-lg border-solid',
                    errors?.startDate ? 'border-red-300' : 'border-gray-100'
                ]"/-->
                <input-tags />
            </label>
        </div>
    </form>
</template>

<script lang="ts">
    import {defineComponent, ref } from 'vue'
    import InputTag from "@/vue/export/atoms/inputs/InputTag.vue";

    export default defineComponent({
        components: {
            'input-tags': InputTag
        },
        props: {
            csrf: {
                type: String,
                required: true
            },
            site: {
                type: String,
                required: true
            }
        },
        setup(){
            const start = ref(null)
            const end = ref(null)
            const startDate = ref('2017-07-04')
            const endDate = ref(
                new Date().getFullYear()+'-'+
                (('0' + ( new Date().getMonth()+1)).slice(-2) )+'-'+
                (('0' + new Date().getDate()).slice(-2))
            )
            const today = ref(  new Date().getFullYear()+'-'+
                (('0' + ( new Date().getMonth()+1)).slice(-2) )+'-'+
                (('0' + new Date().getDate()).slice(-2))
            )
            const yesterday = ref(  new Date().getFullYear()+'-'+
                (('0' + ( new Date().getMonth()+1)).slice(-2) )+'-'+
                (('0' + (new Date().getDate()-1)).slice(-2))
            )
            const tag = ref('')
            const errors = ref({})

            const d = new Date()
            d.setMonth(d.getMonth() - 3)
            startDate.value = d.getFullYear()+'-'+
                (('0' + ( d.getMonth()+1)).slice(-2) )+'-'+
                (('0' + d.getDate()).slice(-2))

            const validateForm = (evt) => {
                errors.value = {}

                if(endDate.value > today.value){
                    errors.value['endDate'] = "This date can't be in the future"
                }

                if(startDate.value > yesterday.value){
                    errors.value['startDate'] = "This date can't be after yesterday"
                }

                if(Object.keys(errors.value).length > 0){
                    evt.preventDefault();
                }
            }

            const setPredefined = (period) => {
                const s = new Date()
                const e = new Date()
                const currentYear = s.getFullYear()

                switch(period){
                    case '6m':
                        endDate.value = today.value

                        e.setMonth(e.getMonth() - 6)

                        startDate.value = e.getFullYear()+'-'+
                            (('0' + ( e.getMonth()+1)).slice(-2) )+'-'+
                            (('0' + e.getDate()).slice(-2))

                        break;
                    case '12m':
                        endDate.value = today.value

                        e.setMonth(e.getMonth() - 12)

                        startDate.value = e.getFullYear()+'-'+
                            (('0' + ( e.getMonth()+1)).slice(-2) )+'-'+
                            (('0' + e.getDate()).slice(-2))

                        break;
                    case 'cay':
                        startDate.value = (currentYear-1)+'-09-01'
                        endDate.value = currentYear+'-07-31'

                        break;
                    case 'lay':
                        startDate.value = (currentYear-2)+'-09-01'
                        endDate.value = (currentYear-1)+'-07-31'

                        break;
                    default:
                        endDate.value = today.value

                        e.setMonth(e.getMonth() - 3)

                        startDate.value = e.getFullYear()+'-'+
                        (('0' + ( e.getMonth()+1)).slice(-2) )+'-'+
                        (('0' + e.getDate()).slice(-2))
                }
            }

            return {
                startDate,
                endDate,
                today,
                yesterday,
                tag,
                errors,
                validateForm,
                setPredefined
            }
        }
    })
</script>
