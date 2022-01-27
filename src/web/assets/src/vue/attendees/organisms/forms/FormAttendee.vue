<template>

    <status-approved :approved="parseInt(approved)" :urn="urn" :disabled="school.length === 0" />

    <form
        ref="form"
        method="post"
        action=""
        accept-charset="UTF-8"
        class="mt-8"
        @keyup="keyup"
        @submit="handleSubmit"
    >

        <input type="hidden" name="action" value="actions/craft-attendees/training/save">
        <input type="hidden" name="event" :value="event">
        <input type="hidden" name="CRAFT_CSRF_TOKEN" :value="csrf">
        <input type="hidden" name="siteId" :value="site">
        <input type="hidden" name="attendeeId" :value="attendeeInput?.id ?? values?.id ?? ''">

        <div class="grid grid-cols-3 gap-x-4 border-b border-blue-100 mb-6 border-solid">
            <input-school :values="values" @schoolSelect="handleSchoolSelect" @schoolInput="handleSchoolInput" />
            <label class="block mb-6">
                <span class="text-xs font-bold text-gray-400 block mb-1">School is verified</span>
                <input-switch name="approved" :checked="approved" @changed="handleSchoolVerifyChange" />
            </label>
        </div>

        <div class="grid grid-cols-3 gap-x-4">
            <label class="block mb-6">
                <span class="text-xs font-bold text-gray-400 block mb-1">Name of attendee <span class="text-blue-500">*</span></span>
                <div class="relative">
                    <input
                        name="name"
                        :value="name"
                        :class="[
                            'block w-full px-2 py-2 text-sm text-gray-600 appearance-none box-border bg-gray-100 rounded-lg',
                            attendeeFormErrors?.name ? 'border-solid border-red-300' : 'border-solid border-gray-100'
                        ]"
                        placeholder="Enter the name of the attendee"
                    />
                    <span v-if="anonymous" class="bg-gray-100 absolute left-0 top-0 w-full h-full rounded-lg flex">
                        <span class="italic text-gray-400 px-2" style="margin-bottom:0px!important;">Anonymous attendee</span>
                    </span>
                </div>
            </label>
            <label class="block mb-6">
                <span class="text-xs font-bold text-gray-400 block mb-1">Email address of attendee <span class="text-blue-500">*</span></span>
                <div class="relative">
                    <input
                        name="email"
                        :value="email"
                        :class="[
                            'block w-full px-2 py-2 text-sm text-gray-600 appearance-none box-border bg-gray-100 rounded-lg',
                            attendeeFormErrors?.email ? 'border-solid border-red-300' : 'border-solid border-gray-100'
                        ]"
                        placeholder="Enter the email of the attendee"
                    />
                    <span v-if="anonymous" class="bg-gray-100 absolute left-0 top-0 w-full h-full rounded-lg flex">
                        <span class="italic text-gray-400 px-2" style="margin-bottom:0px!important;">Anonymous attendee</span>
                    </span>
                </div>
            </label>
            <label class="block mb-6">
                <span class="text-xs font-bold text-gray-400 block mb-1">Attendee is anonymous?</span>
                <input-switch name="anonymous" :checked="attendeeInput?.anonymous ?? values?.anonymous ?? 0" @changed="handleAnonymous" />
            </label>
        </div>

        <div class="grid grid-cols-3 gap-x-4">
            <label class="block mb-6">
                <span class="text-xs font-bold text-gray-400 block mb-1">Attendee's job role <span class="text-blue-500">*</span></span>

                <select
                    name="jobRole"
                    :value="jobRole"
                    :class="[
                    'block h-10 px-1 rounded-md bg-gray-100 w-full',
                    attendeeFormErrors?.jobRole ? 'border-solid border-red-300' : 'border-none'
                ]"
                >
                    <option value="" default disabled class="text-gray-400">Select the job role of the attendee</option>
                    <option value="na">Not Applicable</option>
                    <option value="support">Support Staff</option>
                    <option value="leader-middle">Middle leader</option>
                    <option value="leader">Leadership</option>
                    <option value="teacher">Teacher</option>
                </select>
            </label>
            <label class="block mb-6">
                <span class="text-xs font-bold text-gray-400 block mb-1">Modules attended <span class="text-blue-500">*</span></span>
                <select
                    name="days"
                    :value="attendeeInput?.days ?? values?.days ?? 1"
                    :class="[
                        'block h-10 px-1 rounded-md border-none w-40 bg-gray-100',
                        attendeeFormErrors?.days ? 'border-solid border-red-300' : ''
                    ]"
                >
                    <option default value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                    <option value="5">5</option>
                    <option value="6">6</option>
                    <option value="7">7</option>
                    <option value="8">8</option>
                    <option value="9">9</option>
                    <option value="10">10</option>
                    <option value="11">11</option>
                    <option value="12">12</option>
                    <option value="13">13</option>
                    <option value="14">14</option>
                    <option value="15">15</option>
                </select>
            </label>
            <label class="block mb-6">
                <span class="text-xs font-bold text-gray-400 block mb-1">Subscribe for the newsletter</span>
                <input-switch name="newsletter" :checked="newsletter ?? 0" />
            </label>
        </div>

        <div class="text-right">
            <span role="button" class="inline-block bg-gray-300 text-gray-800 font-bold py-2 px-3 mr-2 text-sm rounded-lg cursor-pointer" @click="hideForm">Cancel</span>
            <button
                v-if="!hideAnother"
                class="bg-emerald-300 text-emerald-800 font-bold py-2 px-3 text-sm rounded-lg cursor-pointer inline-block mr-2"
                :data-saveanother="true"
                @click="handleSubmitButton"
            >
                <svg v-if="loading" class="animate-spin -ml-1 mr-1 h-3 w-3 text-emerald-800" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Save and add another
            </button>
            <button
                class="bg-emerald-300 text-emerald-800 font-bold py-2 px-3 text-sm rounded-lg cursor-pointer inline-block"
                :data-saveanother="false"
                @click="handleSubmitButton"
            >
                <svg v-if="loading" class="animate-spin -ml-1 mr-1 h-3 w-3 text-emerald-800" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Save
            </button>
        </div>
    </form>
</template>

<script lang="ts">
    import {defineComponent, watchEffect, ref, nextTick} from 'vue'
    import { useTrainingsStore } from '@/store/trainings'
    import { storeToRefs } from 'pinia'
    import InputSwitch from '@/vue/attendees/atoms/inputs/InputSwitch.vue';
    import InputSchool from "@/vue/attendees/atoms/inputs/InputSchool.vue";
    import StatusApproved from '@/vue/attendees/molecules/statusses/StatusApproved.vue';

    export default defineComponent({
        components: {
            'input-school': InputSchool,
            'input-switch': InputSwitch,
            'status-approved': StatusApproved
        },
        emits: ['hideForm', 'saveAnother', 'submitForm'],
        props: {
            csrf: {
                type: String,
                required: true
            },
            event: {
                type: String,
                required: true
            },
            site: {
                type: String,
                required: true
            },
            title: {
                type: String,
            },
            values: {
                type: Object,
                default: {}
            },
            hideAnother: {
                type: Boolean,
                default: false
            }
        },
        setup( props, {emit} ){
            const form = ref(null)
            const errors = ref(null)
            const schoolDropdown = ref(null)
            const saveAnother = ref(false)

            const urn = ref( props.values?.urn ?? null)
            const approved = ref(props.attendeeInput?.approved ?? (urn.value?.length > 0 ? 1 : 0))
            const school = ref(props.values?.orgName ?? '')
            const name = ref(props.attendeeInput?.name ?? props.values?.name ?? '')
            const email = ref(props.attendeeInput?.email ?? props.values?.email ?? '')
            const jobRole = ref(props.attendeeInput?.jobRole ?? props.values?.jobRole ?? '')
            const newsletter = ref(props.attendeeInput?.newsletter ?? props.values?.newsletter ?? 0)
            const anonymous = ref(props.attendeeInput?.anonymous ?? props.values?.anonymous ?? 0)

            const store = useTrainingsStore()
            const {
                attendeeInput,
                attendeeFormErrors,
                loading,
                showForm,
                resetAttendeeInput
            } = storeToRefs(store)


            const handleSubmit = (evt) => {
                evt.preventDefault();

                emit('saveAnother', saveAnother.value)

                if(form.value){
                    let formValues = new FormData(form.value)

                    store.submitAttendee(formValues)
                    emit('submitForm')
                }
            }

            const handleSubmitButton = (evt) => {
                saveAnother.value = evt.currentTarget.dataset.saveanother
            }

            const hideForm = () => {
                store.resetForm()
                emit('hideForm')
            }

            const keyup = evt => {
                if(evt.keyCode === 27){
                    hideForm();
                }
            }

            const handleSchoolSelect = (schoolVal, urnVal, postcodeVal) => {
                urn.value = urnVal
                approved.value = urnVal ? 1 : 0
            }

            const handleSchoolInput = (input) => {
                school.value = input
            }

            const handleSchoolVerifyChange = () => {
                approved.value = approved.value == 1 ? 0 : 1
            }

            const handleAnonymous = (val) => {
                anonymous.value = val

                if(val === 1){
                    newsletter.value = 0
                    name.value = ''
                    email.value = ''
                    jobRole.value = 'na'
                }

            }

            watchEffect(() => {
                form.value?.querySelectorAll('input[name="orgName"]')[0].focus()

                //on change
                if(showForm.value){
                    form.value?.querySelectorAll('input[name="orgName"]')[0].focus()
                }
            })

            return {
                form,
                attendeeInput,
                attendeeFormErrors,
                loading,
                showForm,
                urn,
                name,
                email,
                jobRole,
                newsletter,
                anonymous,
                approved,
                school,
                handleSubmitButton,
                handleSubmit,
                hideForm,
                keyup,
                handleSchoolInput,
                handleSchoolSelect,
                handleSchoolVerifyChange,
                handleAnonymous
            };

        }
    })
</script>
