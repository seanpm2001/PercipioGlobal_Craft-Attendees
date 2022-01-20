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

        <div class="w-full items-start mt-10 grid grid-cols-13">
            <div class="col-span-6">

                <div class="mb-4">
                    <span class="font-primary text-lg font-bold block pb-2">CSV File fields</span>
                    <span class="block h-10">from: {{ filename }}</span>
                </div>

                <draggable v-model="csvHeaders">
                    <div v-for="header in csvHeaders" :key="header.id">
                        <div v-if="header.value !== ''" class="bg-white rounded-xl mb-2 w-full px-4 py-2 box-border flex items-center cursor-move h-12">
                            <span class="text-blue-600 text-xl leading-tight" style="margin-bottom:0!important">✥</span>
                            <span class="font-bold flex-grow" style="margin-bottom:0!important">{{header.value}}</span>
                            <button @click="handleRemove(header.value)" class="text-gray-600 cursor-pointer" style="margin-bottom:0!important">✕</button>
                        </div>
                        <div v-else class="bg-gray-200 rounded-xl mb-2 w-full px-4 box-border flex items-center cursor-move h-12 pt-2">
                            <span class="text-blue-600 text-xl leading-tight">✥</span>
                            <span class="italic text-gray-400">No value specified</span>
                        </div>
                    </div>
                </draggable>

            </div>

            <div class="col-span-1 pt-20 mt-2.5">
                <div
                    v-for="header in values"
                    :key="header"
                    class="mb-2 w-full px-4 py-2 box-border flex items-center justify-center cursor-move h-12">
                    <span class="text-blue-600 inline-block text-xl">→</span>
                </div>
            </div>

            <div class="col-span-6">

                <div class="mb-4">
                    <span class="font-primary text-lg font-bold block pb-2">Attendees fields</span>
                    <span class="block h-10">to: {{training.title}}</span>
                </div>

                <div
                    v-for="value in values"
                    :key="value.id"
                    class="bg-white rounded-xl mb-2 w-full px-4 py-2 box-border flex flex-wrap items-center h-12 leading-tight"
                >
                    <span class="font-bold inline-flex flex-grow items-center whitespace-nowrap" style="margin-bottom:0!important">{{value.name}} <span v-if="value.required" class="text-blue-500 inline-block pl-1 font-normal text-xs" style="margin-bottom:0!important">[required]</span></span>
                    <div class="whitespace-nowrap" style="margin-bottom:0!important">
                        <span class="text-gray-400 italic text-xs" v-if="value.info">{{value.info}}</span>
                        <button @click="handleShowInfo(value.id)" class="text-blue-800 cursor-pointer font-bold ml-1">ⓘ</button>
                    </div>
                </div>

                <div
                    v-for="(spacer, i) in (csvHeaders.length - values.length)"
                    v-if="(csvHeaders.length > values.length)"
                    :key="i"
                    class="bg-gray-200 rounded-xl mb-2 w-full px-4 box-border flex items-center cursor-move h-12 pt-2"
                >
                    &nbsp;&nbsp;
                </div>

            </div>
        </div>


        <popup-import :show="showPopup" @hidePopup="handleHidePopup" :info="popupId" />

        <form-import-match
            :csrf="csrf"
            :event="training.id"
            :filepath="filepath"
            :filename="filename"
            :columns="csvHeaders"
            :site="site"
            :submitForm="submitForm"
        />

        <div class="block bg-gray-100 w-6 left-full pb-6 top-0 h-full absolute"></div>
        <div class="block bg-gray-100 h-6 top-full left-0 w-full absolute"></div>

    </section>

</template>

<script lang="ts">
    import {defineComponent, ref, watchEffect} from 'vue'
    import { VueDraggableNext } from 'vue-draggable-next'
    import PopupImport from '@/vue/attendees/organisms/popups/PopupImport.vue'
    import FormImportMatch from '@/vue/attendees/organisms/forms/FormImportMatch.vue'

    export default defineComponent({
        components: {
            'draggable': VueDraggableNext,
            'popup-import': PopupImport,
            'form-import-match': FormImportMatch
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
            filepath: {
                type: String,
                default: ''
            },
            parent: {
                type: String,
                default: ''
            },
            event: {
                type: String,
                required: true
            },
            csrf: {
                type: String,
                required: true
            },
            site: {
                type: String,
                required: true
            }
        },
        setup(props){
            const values = ref([
                {
                    id: 1,
                    name: 'Organisation / School',
                    required: true,
                    info: 'Text'
                },
                {
                    id: 2,
                    name: 'URN',
                    required: false,
                    info: 'Number'
                },
                {
                    id: 3,
                    name: 'Postcode',
                    required: false,
                    info: 'Text'
                },
                {
                    id: 4,
                    name: 'Attendee Name',
                    required: true,
                    info: 'Text'
                },
                {
                    id: 5,
                    name: 'Attendee Email Address',
                    required: false,
                    info: 'Text'
                },
                {
                    id: 6,
                    name: 'Job Role',
                    required: true,
                    info: 'Text exact match [na, support, leader-middle, leader, teacher]',
                },
                {
                    id: 7,
                    name: 'Attending Days',
                    required: true,
                    info: 'Number between 1 - 10'
                },
                {
                    id: 8,
                    name: 'Newsletter',
                    required: false,
                    info: 'Yes | No'
                },
            ])
            const csvHeaders = ref(JSON.parse(props.headers))
            const spacers = ref(values.value.length > csvHeaders.value.length ? values.value.length - csvHeaders.value.length : 0 )
            const training = ref(JSON.parse(props.event))
            const showPopup = ref(false)
            const popupId = ref(null)
            const submitForm = ref(false)

            //add empty values when too less values are in the csv
            for(let i = 0; i < spacers.value; i++){
                csvHeaders.value.push('')
            }

            //convert string to object
            csvHeaders.value = csvHeaders.value.map((el, i) => { return {'id': i, 'value': el} })


            //handlers
            const handleRemove = (value) => {
                csvHeaders.value = csvHeaders.value.map(el => el.value === value ? {'id': el.id, 'value': ''} : el)
            }

            const handleSubmit = () => {
                submitForm.value = true
            }

            const handleCancel = () => {
                window.location.href = props.parent?.length > 0 ? props.parent : '/'
            }

            const handleShowInfo = (id) => {
                showPopup.value = true
                popupId.value = id
            }

            const handleHidePopup = () => {
                showPopup.value = false
                popupId.value = null
            }

            return {
                values,
                csvHeaders,
                popupId,
                showPopup,
                spacers,
                training,
                submitForm,
                handleRemove,
                handleSubmit,
                handleCancel,
                handleHidePopup,
                handleShowInfo
            }
        }
    })

</script>
