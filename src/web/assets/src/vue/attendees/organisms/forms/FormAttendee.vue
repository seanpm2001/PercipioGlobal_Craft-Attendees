<template>

    <form
        ref="form"
        method="post"
        action=""
        accept-charset="UTF-8"
        class="grid grid-cols-3 gap-x-4"
        @keyup="keyup"
        @submit="handleSubmit"
    >

        <input type="hidden" name="action" value="actions/craft-attendees/training/save">
        <input type="hidden" name="event" :value="event">
        <input type="hidden" name="CRAFT_CSRF_TOKEN" :value="csrf">
        <input type="hidden" name="attendeeId" :value="attendeeInput?.id ?? values?.id ?? ''">

        <div>

            <input-school :values="values" @schoolSelect="handleSchoolSelect" />

            <label class="block mb-6">
                <span class="text-xs font-bold text-gray-400 block mb-1">Name of attendee <span class="text-blue-500">*</span></span>
                <input
                    name="name"
                    :value="attendeeInput?.name ?? values?.name ?? ''"
                    :class="[
                        'block w-full px-2 py-2 text-sm text-gray-600 appearance-none box-border bg-gray-100 rounded-lg',
                        attendeeFormErrors?.name ? 'border-solid border-red-300' : 'border-solid border-gray-100'
                    ]"
                    placeholder="Enter the name of the attendee"
                />
            </label>
            <label class="block mb-6">
                <span class="text-xs font-bold text-gray-400 block mb-1">Attendee's job role <span class="text-blue-500">*</span></span>
                <!--input
                    name="jobRole"
                    :value="attendeeInput?.jobRole ?? values?.jobRole ?? ''"
                    :class="[
                        'block w-full px-2 py-2 text-sm text-gray-600 appearance-none box-border bg-gray-100 rounded-lg',
                        attendeeFormErrors?.jobRole ? 'border-solid border-red-300' : 'border-solid border-gray-100'
                    ]"
                    placeholder="Enter the job role of the attendee" /-->

                <select
                    name="jobRole"
                    :value="attendeeInput?.jobRole ?? values?.jobRole ?? ''"
                    :class="[
                        'block h-10 px-1 rounded-md border-none bg-gray-100 w-full',
                        attendeeFormErrors?.jobRole ? 'border-solid border-red-300' : ''
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
                <span class="text-xs font-bold text-gray-400 block mb-1">Email address of attendee <span class="text-blue-500">*</span></span>
                <input
                    name="email"
                    :value="attendeeInput?.email ?? values?.email ?? ''"
                    :class="[
                        'block w-full px-2 py-2 text-sm text-gray-600 appearance-none box-border bg-gray-100 rounded-lg',
                        attendeeFormErrors?.email ? 'border-solid border-red-300' : 'border-solid border-gray-100'
                    ]"
                    placeholder="Enter the email of the attendee"
                />
            </label>
        </div>
        <div>
            <label class="block mb-6">
                <span class="text-xs font-bold text-gray-400 block mb-1">Attending days <span class="text-blue-500">*</span></span>
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
                </select>
            </label>
            <label class="block mb-6">
                <span class="text-xs font-bold text-gray-400 block mb-1">Subscribe for the newsletter</span>
                <input-switch name="newsletter" :checked="attendeeInput?.newsletter ?? values?.newsletter ?? 0" />
            </label>
            <label class="block mb-6">
                <span class="text-xs font-bold text-gray-400 block mb-1">School is verified</span>
                <input-switch name="approved" :checked="attendeeInput?.approved ?? values?.approved ?? (urn?.length > 0 ? 1 : 0)" />
                <p v-if="attendeeInput?.orgUrn ?? values?.orgUrn ?? urn?.length > 0" class="text-blue-600 text-xs">This school is verified and linked to <a :href="`https://get-information-schools.service.gov.uk/Establishments/Establishment/Details/${urn}`" class="text-blue-600 underline" target="_blank">government establishment data</a></p>
                <!--p v-if="attendee?.approved == 1" class="text-blue-600 text-xs">Data verified, but not a not linked to government establishment data</p-->
            </label>

        </div>
        <div>
            <div class="mb-6">
                <span class="text-xs font-bold text-gray-400 block mb-2">Actions</span>
                <button
                    class="bg-emerald-300 text-emerald-800 font-bold py-2 px-3 text-sm rounded-lg cursor-pointer inline-block"
                >
                    <svg v-if="loading" class="animate-spin -ml-1 mr-1 h-3 w-3 text-emerald-800" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Save
                </button>

                <div></div>

                <span role="button" class="inline-block bg-red-300 text-red-800 font-bold mt-2 py-2 px-3 text-sm rounded-lg cursor-pointer" @click="hideForm">Cancel</span>

            </div>
        </div>
    </form>
</template>

<script lang="ts">
    import {defineComponent, watchEffect, ref} from 'vue'
    import { useTrainingsStore } from '@/store/trainings'
    import { storeToRefs } from 'pinia'
    import InputSwitch from '@/vue/attendees/atoms/inputs/InputSwitch.vue';
    import InputSchool from "@/vue/attendees/atoms/inputs/InputSchool.vue";

    export default defineComponent({
        components: {
            'input-school': InputSchool,
            'input-switch': InputSwitch,
        },
        props: {
            csrf: {
                type: String,
                required: true
            },
            event: {
                type: String,
                required: true
            },
            title: {
                type: String,
            },
            values: {
                type: Object,
                default: {}
            }
        },
        setup( props, {emit} ){
            const form = ref(null)
            const schoolDropdown = ref(null)
            const errors = ref(null)
            const urn = ref(null)
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

                if(form.value){
                    let formValues = new FormData(form.value)

                    let formObj = {};

                    for (var pair of formValues.entries()) {
                        formObj[pair[0]] = pair[1]
                    }

                    store.submitAttendee(formValues)
                    emit('submitForm')
                }
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
                handleSubmit,
                hideForm,
                keyup,
                handleSchoolSelect
            };

        }
    })
</script>
