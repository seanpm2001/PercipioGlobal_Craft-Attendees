<template>
    <div :class="[
        'transition-all duration-500 delay-50 ease-in-out fixed left-0 top-0 w-screen h-screen',
        show ? 'z-[100] opacity-100 bg-gray-900 bg-opacity-50' : 'z-0 opacity-0 pointer-events-none'
    ]">
        <div class="max-h-screen overflow-auto fixed left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 w-1/2 xl:w-1/3">
            <div class="bg-white p-6 rounded-xl mb-10">

                <div class="w-full mb-8">
                    <h3 class="text-lg whitespace-nowrap text-ellipsis overflow-hidden text-center">Verify {{ attendee.name }}</h3>
                    <p class="text-sm">Attendee data needs to be verified against the government establishment data. Using organisation name provided you will find some suggestions below. Click the correct school to verify the data.<br/><br/>If <strong>{{ attendee.name }}</strong> is not associated with a school, you can still verify without selecting a school.</p>
                </div>

                <input-school :values="attendee" @schoolSelect="handleSchoolSelect" />

                <div class="flex">
                    <div class="flex-grow">
                        <button @click="handleEdit" class="block bg-gray-300 text-gray-800 font-bold mt-2 py-2 px-3 text-sm rounded-lg cursor-pointer">
                            Edit
                        </button>
                    </div>
                    <button @click="handleCancel" class="block bg-gray-300 text-gray-800 font-bold mt-2 py-2 px-3 text-sm rounded-lg cursor-pointer">
                        Cancel
                    </button>
                    <button @click="handleVerify" class="block bg-emerald-300 text-emerald-800 font-bold mt-2 py-2 px-3 text-sm rounded-lg cursor-pointer">
                        Verify organisation
                    </button>
                </div>

            </div>
        </div>
    </div>
</template>

<script lang="ts">
    import {defineComponent, ref, watchEffect} from 'vue'
    import { useTrainingsStore } from '@/store/trainings'
    import SelectSchool from "@/vue/attendees/atoms/inputs/SelectSchool.vue";

    export default defineComponent({
        components: {
            'input-school': SelectSchool,
        },
        emits: ['hidePopup','edit'],
        props: {
            csrf: {
                type: String,
                required: true
            },
            attendee: {
                type: Object,
                required: true
            },
            show: {
                type: Boolean,
                required: true,
            }
        },
        setup(props, {emit}){
            const store = useTrainingsStore()
            const schoolData = ref({school: props.attendee.orgName, urn: props.attendee.urn, postcode: props.attendee.postCode})

            // const handleDelete = () => {
            //     let deleteAttendee = {}
            //     deleteAttendee.CRAFT_CSRF_TOKEN = props.csrf
            //     deleteAttendee.action = 'actions/craft-attendees/training/delete'
            //     deleteAttendee.attendeeId = props.attendee.id
            //     store.deleteAttendee(deleteAttendee)
            //     emit('hidePopup')
            // }

            const handleVerify = () => {

                let approveAttendee = {...props.attendee}
                approveAttendee.orgName = schoolData.value.school ?? props.attendee.orgName;
                approveAttendee.orgUrn = schoolData.value.urn ?? props.attendee.orgUrn;
                approveAttendee.postCode = schoolData.value.postcode ?? props.attendee.postCode;
                approveAttendee.approved = 1
                approveAttendee.CRAFT_CSRF_TOKEN = props.csrf
                approveAttendee.event = props.attendee.eventId
                approveAttendee.attendeeId = props.attendee.id
                approveAttendee.action = 'actions/craft-attendees/training/save'

                store.submitAttendee(approveAttendee)
                store.clearSchools()

                emit('hidePopup')
            }

            const handleCancel = () => {
                store.clearSchools()
                emit('hidePopup')
            }

            const handleEdit = () => {
                emit('edit')
            }

            const handleSchoolSelect = (school, urn, postcode) => {
                if(school || urn || postcode){
                    //selected from api
                    schoolData.value = {school: school, urn: urn, postcode: postcode}
                }else{
                    //not selected from api
                    schoolData.value = {school: props.attendee.orgName, urn: props.attendee.urn, postcode: props.attendee.postcode}
                }
            }

            return { handleCancel, handleVerify, handleSchoolSelect, handleEdit };

        }
    })
</script>
