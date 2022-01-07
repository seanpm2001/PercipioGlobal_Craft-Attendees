<template>

    <form
        ref="form"
        method="post"
        action=""
        accept-charset="UTF-8"
        class="grid grid-cols-3 gap-x-4"
        @keyup="keyup"
        @submit="submitHandler"
    >

        <input type="hidden" name="action" value="actions/craft-attendees/training/save">
        <input type="hidden" name="event" :value="event">
        <input type="hidden" name="CRAFT_CSRF_TOKEN" :value="csrf">
        <input type="hidden" name="attendeeId" :value="attendeeInput?.id ?? values?.id ?? ''">

        <div>
            <label class="block mb-6">
                <span class="text-xs font-bold text-gray-400 block mb-1">School / Organisation <span class="text-blue-500">*</span></span>
                <div class="w-full relative">
                    <input
                        name="orgName"
                        v-model="school"
                        @input="delay"
                        @focus="handleFocus(true)"
                        @blur="handleFocus(false)"
                        :class="[
                            'block peer w-full px-2 py-2 text-sm text-gray-600 box-border bg-gray-100 rounded-lg',
                            attendeeFormErrors?.orgName ? 'border-solid border-red-300' : 'border-solid border-gray-100'
                        ]"
                        placeholder="Search for a school or organisation"
                        :aria-owns="`metaseed-list-${uniqueId}`"
                        aria-autocomplete="list"
                        role="combobox"
                    />

                    <ul
                        class="absolute left-0 top-full mt-1 w-full max-h-52 overflow-scroll z-10 bg-gray-100 rounded-lg shadow-xl"
                        v-show="showDropdown"
                        aria-expanded="true"
                        role="listbox"
                        :id="`metaseed-list-${uniqueId}`"
                    >
                        <li class="p-2 pointer-all hover:bg-blue-600 hover:text-white text-sm" v-for="school in schools" role="option" @click="handleSchoolSelect">{{school.value}}</li>
                    </ul>
                </div>
            </label>
            <label class="block mb-6">
                <span class="text-xs font-bold text-gray-400 block mb-1">Post code <span class="text-blue-500">*</span></span>
                <input
                    name="postCode"
                    :value="attendeeInput?.postCode ?? values?.postCode ?? ''"
                    :class="[
                        'block w-full px-2 py-2 text-sm text-gray-600 appearance-none box-border bg-gray-100 rounded-lg',
                        attendeeFormErrors?.postCode ? 'border-solid border-red-300' : 'border-solid border-gray-100'
                    ]"
                    placeholder="Enter a post code"
                />
            </label>
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
                <input
                    name="jobRole"
                    :value="attendeeInput?.jobRole ?? values?.jobRole ?? ''"
                    :class="[
                        'block w-full px-2 py-2 text-sm text-gray-600 appearance-none box-border bg-gray-100 rounded-lg',
                        attendeeFormErrors?.jobRole ? 'border-solid border-red-300' : 'border-solid border-gray-100'
                    ]"
                    placeholder="Enter the job role of the attendee" />
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
                <span class="text-xs font-bold text-gray-400 block mb-1">School is approved</span>
                <input-switch name="approved" :checked="attendeeInput?.approved ?? values?.approved ?? 0" />
            </label>
            <label class="block mb-6">
                <span class="text-xs font-bold text-gray-400 block mb-1">Subscribe for the newsletter</span>
                <input-switch name="newsletter" :checked="attendeeInput?.newsletter ?? values?.newsletter ?? 0" />
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
    import { useAttendeeStore } from '@/store/attendees'
    import { storeToRefs } from 'pinia'
    import InputSwitch from '@/vue/attendees/atoms/inputs/InputSwitch.vue';

    export default defineComponent({
        components: {
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
            const errors = ref(null)
            const store = useAttendeeStore()
            const uniqueId = ref(Math.floor(Math.random() * 100) + Date.now())
            const {
                attendeeInput,
                attendeeFormErrors,
                loading,
                showForm,
                schools,
                resetAttendeeInput
            } = storeToRefs(store)
            const school = ref(attendeeInput.value ?? props.values?.orgName ?? '')
            const timer = ref(null)
            const showDropdown = ref(false)

            const submitHandler = (evt) => {
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

            const handleSchoolSelect = evt => {
                school.value = evt.currentTarget.textContent
                showDropdown.value = false
                store.clearSchools()
            }

            const handleSchoolInput = () => {
                if(school.value.length > 2){
                    store.fetchSchools(school.value)
                }
            }

            const handleFocus = (val) => {
                setTimeout(() => {
                    showDropdown.value = val
                },150)
            }

            const delay = (fn, ms) => {
                if(timer.value){
                    clearTimeout(timer.value)
                }
                timer.value = setTimeout(handleSchoolInput, 200)
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
                school,
                schools,
                uniqueId,
                showDropdown,
                delay,
                submitHandler,
                hideForm,
                keyup,
                handleSchoolSelect,
                handleSchoolInput,
                handleFocus
            };

        }
    })
</script>
