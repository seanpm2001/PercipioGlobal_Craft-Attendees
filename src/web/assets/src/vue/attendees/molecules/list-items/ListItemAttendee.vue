<template>
    <div
        tabindex="0"
        :class="[
            'w-full border-l-2 border-solid',
            expanded ? 'border-blue-800 bg-blue-100 bg-opacity-10' : 'border-white'
        ]"
    >
        <div class="grid grid-cols-11 w-full">
            <div class="col-span-3 p-3 flex flex-nowrap items-center font-bold cursor-pointer" @click="toggle">
                <span class="inline-flex mb-0"><input type="checkbox"/></span>
                <span class="inline-flex pl-2">{{ attendee.orgName }}</span>
            </div>
            <div class="col-span-2 box-border p-3 cursor-pointer" @click="toggle">
                {{ attendee.name }}
            </div>
            <div class="col-span-3 box-border p-3 cursor-pointer" @click="toggle">
                {{ attendee.email }}
            </div>
            <div class="box-border p-3 cursor-pointer" @click="toggle">
                {{ attendee.days }}
            </div>
            <div class="box-border p-3 cursor-pointer" @click="toggle">
                <span v-if="attendee.approved == 0 || !attendee.approved" class="inline-block rounded-full text-xs bg-orange-300 text-orange-800 text-bold px-4 py-1">Pending</span>
                <span v-else class="inline-block rounded-full text-xs bg-emerald-400 text-white text-bold px-4 py-1">Approved</span>
            </div>
            <div class="w-full box-border pr-3" @click="toggle">
                <button class="float-right block bg-gray-300 text-gray-800 mt-2 py-1 px-3 text-sm font-bold rounded-lg">Actions v</button>
            </div>
        </div>

        <!-- expanded version -->
        <div class="px-10 py-6" v-if="expanded">
            <h3 class="text-lg">Attendee details</h3>

            <div class="grid grid-cols-3 gap-x-4" v-if="!edit">
                <div>
                    <div class="mb-6">
                        <span class="text-xs font-bold text-gray-400 block">School or organisation</span>
                        <span class="text-base mt-1 block">{{ attendee.orgName }}</span>
                    </div>
                    <div class="mb-6">
                        <span class="text-xs font-bold text-gray-400 block">School or organisation post code</span>
                        <span class="text-base mt-1 block">{{ attendee.postCode }}</span>
                    </div>
                    <div class="mb-6">
                        <span class="text-xs font-bold text-gray-400 block">Name of the attendee</span>
                        <span class="text-base mt-1 block">{{ attendee.name }}</span>
                    </div>
                    <div class="mb-6">
                        <span class="text-xs font-bold text-gray-400 block">Attendee's job role</span>
                        <span class="text-base mt-1 block">{{ attendee.jobRole }}</span>
                    </div>
                    <div class="mb-6">
                        <span class="text-xs font-bold text-gray-400 block">Email address of attendee</span>
                        <span class="text-base mt-1 block">{{ attendee.email }}</span>
                    </div>
                </div>
                <div>
                    <div class="mb-6">
                        <span class="text-xs font-bold text-gray-400 block">Attending days</span>
                        <span class="text-base mt-1 block">{{ attendee.days }}</span>
                    </div>
                    <div class="mb-6">
                        <span class="text-xs font-bold text-gray-400 block">Subscribed for the newsletter?</span>
                        <span class="text-base mt-1 block">{{ attendee.newsletter == 0 ? 'No' : 'Yes' }}</span>
                    </div>
                    <div class="mb-6">
                        <span class="text-xs font-bold text-gray-400 block mb-2">Approved status</span>
                        <span v-if="attendee.approved == 0 || !attendee.approved" class="inline-block rounded-full text-xs bg-orange-300 text-orange-800 text-bold px-4 py-1">Pending</span>
                        <span v-else class="inline-block rounded-full text-xs bg-emerald-400 text-white text-bold px-4 py-1">Approved</span>
                    </div>
                </div>
                <div>
                    <div class="mb-6">
                        <span class="text-xs font-bold text-gray-400 block">Actions</span>
                        <button @click="toggleEdit" class="block bg-gray-300 text-gray-800 mt-2 py-2 px-3 text-sm font-bold rounded-lg cursor-pointer">Edit</button>
                        <button class="block bg-red-300 text-red-800 font-bold mt-2 py-2 px-3 text-sm rounded-lg">Delete</button>
                        <button @click="handleApprove" v-if="attendee.approved == 0 || !attendee.approved" class="block bg-emerald-300 text-emerald-800 font-bold mt-2 py-2 px-3 text-sm rounded-lg cursor-pointer">
                            <svg v-if="loading" class="animate-spin -ml-1 mr-1 mt-1 h-3 w-3 text-emerald-800" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Approve
                        </button>
                        <button @click="handleDisapprove" v-if="attendee.approved == 1 || attendee.approved" class="block bg-orange-300 text-orange-800 font-bold mt-2 py-2 px-3 text-sm rounded-lg cursor-pointer">
                            <svg v-if="loading" class="animate-spin -ml-1 mr-1 mt-1 h-3 w-3 text-orange-800" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Disapprove
                        </button>
                    </div>
                </div>
            </div>

            <form-attendee v-if="edit" :csrf="csrf" :values="attendee" :event="event" @hideForm="toggleEdit" @submitForm="submitEdit" />

        </div>
    </div>
</template>

<script lang="ts">
import {defineComponent, ref, watch, watchEffect} from 'vue'
    import { useAttendeeStore } from '@/store/attendees'
    import { storeToRefs } from 'pinia'
    import FormAttendee from '@/vue/attendees/organisms/forms/FormAttendee.vue';

    export default defineComponent({
        components: {
            'form-attendee': FormAttendee,
        },
        props: {
            attendee: {
                type: Object,
                required: true
            },
            csrf: {
                type: String,
                required: true
            },
            event: {
                type: String,
                required: true
            }
        },
        setup(props){
            const expanded = ref(false)
            const edit = ref(false)
            const store = useAttendeeStore();
            const { attendeeSuccess, loading } = storeToRefs(store)
            const formSubmitted = ref(false)

            const toggle = () => {
                expanded.value = !expanded.value
            }

            const toggleEdit = () => {
                edit.value = !edit.value
            }

            const submitEdit = () => {
                formSubmitted.value = true
            }

            const handleApprove = () => {
                let approveAttendee = {...props.attendee}
                approveAttendee.approved = 1
                approveAttendee.CRAFT_CSRF_TOKEN = props.csrf
                approveAttendee.event = props.event
                approveAttendee.attendeeId = props.id
                approveAttendee.action = 'actions/craft-attendees/training/save'

                store.submitAttendee(approveAttendee)
            }

            const handleDisapprove = () => {
                let disapproveAttendee = {...props.attendee}
                disapproveAttendee.approved = 0
                disapproveAttendee.CRAFT_CSRF_TOKEN = props.csrf
                disapproveAttendee.event = props.event
                disapproveAttendee.attendeeId = props.attendee.id
                disapproveAttendee.action = 'actions/craft-attendees/training/save'

                store.submitAttendee(disapproveAttendee)
            }

            watchEffect(() => {
                if(formSubmitted.value && attendeeSuccess.value){
                    edit.value = false
                    formSubmitted.value = false
                }
            })

            return { expanded, edit, loading, toggle, toggleEdit, submitEdit, handleApprove, handleDisapprove }

        }
    })
</script>
