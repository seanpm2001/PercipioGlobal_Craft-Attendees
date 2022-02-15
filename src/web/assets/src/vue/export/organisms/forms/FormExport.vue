<template>
    <form
        ref="form"
        method="post"
        action=""
        accept-charset="UTF-8"
        class="mt-8"
    >
        <input type="hidden" name="action" value="craft-attendees/csv/export">
        <input type="hidden" name="CRAFT_CSRF_TOKEN" :value="csrf">
        <input type="hidden" name="site" :value="site">
        <!--input type="hidden" name="start" :value="start">
        <input type="hidden" name="end" :value="end"-->

        <div class="grid grid-cols-5">
            <label class="pr-2">
                <span class="text-xs font-bold text-gray-400 block mb-1">Select a school</span>
                <select name="school" class="block h-10 px-1 rounded-md bg-gray-100 w-full border-none">
                    <option default value="all">All schools</option>
                    <option default value="prior">Prior schools</option>
                </select>
            </label>
            <label class="pr-2">
                <span class="text-xs font-bold text-gray-400 block mb-1">Select an export type</span>
                <select name="type" class="block h-10 px-1 rounded-md bg-gray-100 w-full border-none">
                    <option default value="attendee">Attendee Data</option>
                    <option default value="event">Event ID reference table</option>
                    <option default value="subscriptions">Mailing list subscribers</option>
                    <option default value="school-attendee">School data per attendee</option>
                    <option default value="school-unique">School data unique</option>
                </select>
            </label>
            <label class="col-span-2">
                <span class="text-xs font-bold text-gray-400 block mb-1">Time period</span>
                <div class="flex w-full">
                    <input type="date" v-model="startDate" name="start" class="inline-flex flex-grow px-2 py-2 text-sm text-gray-600 appearance-none box-border bg-gray-100 rounded-lg border-solid border-gray-100"/>
                    <input type="date" v-model="endDate" name="end" class="inline-flex flex-grow px-2 py-2 text-sm text-gray-600 appearance-none box-border bg-gray-100 rounded-lg border-solid border-gray-100"/>
                </div>
            </label>
            <div class="pt-.5 text-right">
                <button class="bg-emerald-300 text-emerald-800 font-bold py-2 px-3 text-sm rounded-lg cursor-pointer inline-block mt-5">Export data</button>
            </div>
        </div>
    </form>
</template>

<script lang="ts">
    import {defineComponent, ref } from 'vue'

    export default defineComponent({
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

            const d = new Date()
            d.setMonth(d.getMonth() - 3)
            startDate.value = d.getFullYear()+'-'+
                (('0' + ( d.getMonth()+1)).slice(-2) )+'-'+
                (('0' + d.getDate()).slice(-2))

            console.log(endDate)

            return {
                startDate,
                endDate
            }
        }
    })
</script>
