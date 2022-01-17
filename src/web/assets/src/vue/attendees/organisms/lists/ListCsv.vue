<template>

    <section class="bg-gray-100 p-6 pt-10 -ml-6 relative mt-10">

        <div class="w-full flex field">
            <div class="heading flex-grow">
                <label class="text-xl inline-block w-full mb-2">Map fields into the correct data</label>
                <span>Choose the fields to import into the attendees from the CSV file by dragging them<br/>in the appropriate order. Click on the ✕ to delete an unused field.</span>
            </div>

            <button class="bg-gray-300 text-gray-800 font-bold py-2 px-3 text-sm rounded-lg cursor-pointer" @click="handleCancel">Cancel</button>
            <button class="bg-emerald-300 text-emerald-800 font-bold py-2 px-3 text-sm rounded-lg cursor-pointer" @click="handleSubmit">Start import</button>
        </div>

        <div class="flex w-full items-start mt-10">
            <div class="flex-grow">

                <div class="mb-4">
                    <span class="font-primary text-lg font-bold block pb-2">CSV File fields</span>
                    <span class="block">from: {{ filename }}</span>
                </div>

                <draggable :list="csvHeaders">
                    <div v-for="header in csvHeaders" :key="header.id">
                        <div v-if="header.value !== ''" class="bg-white rounded-xl mb-2 w-full px-4 box-border flex items-center cursor-move h-12 pt-2">
                            <span class="text-blue-600 text-xl leading-0">✥</span>
                            <span class="font-bold flex-grow">{{header.value}}</span>
                            <button @click="handleRemove(header.value)" class="text-gray-600 cursor-pointer">✕</button>
                        </div>
                        <div v-else class="bg-gray-200 rounded-xl mb-2 w-full px-4 box-border flex items-center cursor-move h-12 pt-2">
                            <span class="text-blue-600 text-xl leading-0">✥</span>
                            <span class="italic text-gray-400">No value specified</span>
                        </div>
                    </div>
                </draggable>

            </div>

            <div class="w-20 pt-16 mt-2.5">
                <div
                    v-for="header in csvHeaders"
                    :key="header"
                    class="mb-2 w-full px-4 box-border flex items-center justify-center cursor-move h-12 pt-2">
                    <span class="text-blue-600 inline-block text-xl">→</span>
                </div>
            </div>

            <div class="flex-grow">

                <div class="mb-4">
                    <span class="font-primary text-lg font-bold block pb-2">Attendees fields</span>
                    <span class="block">to: database</span>
                </div>

                <div v-for="value in values" class="bg-white rounded-xl mb-2 w-full px-4 box-border flex items-center cursor-move h-12 pt-2">
                    <span class="font-bold inline-block ">{{value}}</span>
                </div>

            </div>
        </div>



        <div class="block bg-gray-100 w-6 left-full pb-6 top-0 h-full absolute"></div>
        <div class="block bg-gray-100 h-6 top-full left-0 w-full absolute"></div>

    </section>

</template>

<script lang="ts">
    import {defineComponent, ref, watchEffect} from 'vue'
  import { VueDraggableNext } from 'vue-draggable-next'

    export default defineComponent({
        components: {
            'draggable': VueDraggableNext
        },
        props: {
            headers: {
                type: String,
                required: true
            },
            filename: {
                type: String,
                default: ''
            },
            parent: {
                type: String,
                default: ''
            }
        },
        setup(props){
            const values = ref(['Organisation / School', 'URN', 'Post Code', 'Attendee Name', 'Attendee Email Address', 'Job Role', 'Attending Days', 'Newsletter'])
            const csvHeaders = ref(JSON.parse(props.headers))
            const spacers = ref(values.value.length > csvHeaders.value.length ? values.value.length - csvHeaders.value.length : 0 )

            for(let i = 0; i < spacers.value; i++){
                csvHeaders.value.push('')
            }

            csvHeaders.value = csvHeaders.value.map((el, i) => { return {'id': i, 'value': el} })

            const handleRemove = (value) => {
                console.log("map?",value)
                csvHeaders.value = csvHeaders.value.map(el => el.value === value ? {'id': el.id, 'value': ''} : el)
            }

            const handleSubmit = () => {
                console.log("submit")
            }

            const handleCancel = () => {
                window.location.href = props.parent?.length > 0 ? props.parent : '/'
            }

            return { values, csvHeaders, handleRemove, handleSubmit, handleCancel}
        }
    })

</script>
