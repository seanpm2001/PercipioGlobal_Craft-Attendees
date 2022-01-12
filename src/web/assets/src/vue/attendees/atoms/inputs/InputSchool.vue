<template>

    <input type="hidden" name="orgUrn" :value="urn ?? ''">

    <label class="block mb-6">
        <span class="text-xs font-bold text-gray-400 block mb-1">School / Organisation <span class="text-blue-500">*</span> </span>
        <div class="w-full relative">
            <input
                name="orgName"
                v-model="school"
                @input="delay"
                @focus="handleFocus(true)"
                @blur="handleFocus(false)"
                @keydown.down.prevent="onArrowDown"
                @keydown.up.prevent="onArrowUp"
                @keydown.enter.prevent="selectCurrentSelection"
                :class="[
                    'block peer w-full px-2 py-2 text-sm text-gray-600 box-border bg-gray-100 rounded-lg pr-7',
                    attendeeFormErrors?.orgName ? 'border-solid border-red-300' : 'border-solid border-gray-100'
                ]"
                placeholder="Search for a school or organisation"
                :aria-owns="`metaseed-list-${uniqueId}`"
                aria-autocomplete="list"
                role="combobox"
            />

            <span v-if="school?.length > 3" @click="handleClear" class="text-blue-800 bg-gray-100 block w-6 text-center flex items-center justify-center h-8 absolute right-0 top-1 cursor:pointer">&#x2715</span>

            <ul
                class="absolute left-0 top-full mt-1 w-128 max-h-52 overflow-scroll z-10 bg-gray-100 rounded-lg shadow-xl"
                v-show="showDropdown"
                ref="schoolDropdown"
                :aria-expanded="showDropdown"
                role="listbox"
                :id="`metaseed-list-${uniqueId}`"
            >
                <li
                    v-if="!loadingApi"
                    v-for="(school, i) in schools"
                    :key="school?.data?.urn"
                    role="option"
                >
                    <span
                        v-if="school?.data"
                        @mousedown="handleSchoolSelect"
                        @touch="handleSchoolSelect"
                        role="button"
                        :data-postcode="school?.data?.postcode"
                        :data-urn="school?.data?.urn"
                        :class="[
                         'p-2 pointer-all hover:bg-blue-600 hover:text-white focus:bg-blue-600 block group',
                         currentSelectionIndex === i ? 'bg-blue-600 text-white' : ''
                        ]"
                    >
                        <span class="block w-full text-sm font-medium">{{school.value}}</span>
                        <span :class="['block text-xs group-focus:text-white', currentSelectionIndex === i ? 'text-white' : 'text-gray-500']">[URN: {{ school?.data?.urn ?? '-' }}] {{ school?.data?.street ?? '-' }}, {{ school?.data?.town ?? '-' }} {{ school?.data?.postcode }} (Age {{ school?.data?.ageFrom ?? '/' }} - {{ school?.data?.ageTo ?? '/' }})</span>
                    </span>
                    <span v-else class="p-2 block">
                        There's no school that matches the input
                    </span>
                </li>

                <li
                    v-if="loadingApi"
                    class="p-2 pt-3 inline-block"
                >
                    <svg class="animate-spin mr-1 h-4 w-4 text-emerald-800" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </li>
            </ul>
        </div>
    </label>

    <label class="block mb-6">
        <span class="text-xs font-bold text-gray-400 block mb-1">Post code <span class="text-blue-500">*</span></span>
        <input
            name="postCode"
            :value="postcode"
            v-bind:readonly="urn?.length > 0"
            :class="[
                'block w-full px-2 py-2 text-sm text-gray-600 appearance-none box-border bg-gray-100 rounded-lg',
                urn?.length > 0 ? 'text-gray-400 cursor-not-allowed' : '',
                attendeeFormErrors?.postCode ? 'border-solid border-red-300' : 'border-solid border-gray-100'
            ]"
            placeholder="Enter a post code"
        />
    </label>

</template>

<script lang="ts">
import {defineComponent, watchEffect, watch, ref} from 'vue'
import { useTrainingsStore } from '@/store/trainings'
import { storeToRefs } from 'pinia'
import InputSwitch from '@/vue/attendees/atoms/inputs/InputSwitch.vue';

export default defineComponent({
    props: {
        values: {
            type: Object,
            default: {}
        }
    },
    emits: ["schoolSelect"],
    setup( props, {emit} ){
        const schoolDropdown = ref(null)
        const store = useTrainingsStore()
        const uniqueId = ref(Math.floor(Math.random() * 100) + Date.now())
        const {
            attendeeInput,
            attendeeFormErrors,
            schools,
            loadingApi
        } = storeToRefs(store)
        const school = ref(attendeeInput.value?.orgName ?? props.values?.orgName ?? '')
        const urn = ref(attendeeInput.value?.orgUrn ?? props.values?.orgUrn ?? '')
        const postcode = ref(attendeeInput.value?.postCode ?? props.values?.postCode ?? '')
        const timer = ref(null)
        const showDropdown = ref(false)
        const currentSelectionIndex = ref(0)
        const clearResults = ref(true)

        const handleSchoolSelect = evt => {
            school.value = evt.currentTarget.textContent
            urn.value = evt.currentTarget?.dataset?.urn
            postcode.value = evt.currentTarget?.dataset?.postcode

            clearResults.value = false

            emit('schoolSelect', school.value, urn.value, postcode.value)

            showDropdown.value = false
            store.clearSchools()
        }

        const handleSchoolInput = () => {

            clearResults.value = true

            if(school.value.length > 2){
                showDropdown.value = true;
                store.fetchSchools(school.value)
            }
        }

        const handleFocus = (val) => {
            if (school.value?.length === 0) {
                store.clearSchools()
            }
            showDropdown.value = val
        }

        const delay = () => {
            if(timer.value){
                clearTimeout(timer.value)
            }
            timer.value = setTimeout(handleSchoolInput, 200)
        }

        const onArrowDown = () => {
            currentSelectionIndex.value = currentSelectionIndex.value < schools.value?.length - 1 ? currentSelectionIndex.value + 1 : schools.value?.length - 1
            schoolDropdown.value.querySelectorAll('li')[currentSelectionIndex.value].scrollIntoView({ block: 'nearest' })
        }

        const onArrowUp = () => {
            currentSelectionIndex.value = currentSelectionIndex.value > 0 ? currentSelectionIndex.value - 1 : 0
            schoolDropdown.value.querySelectorAll('li')[currentSelectionIndex.value].scrollIntoView({ block: 'nearest' })
        }

        const selectCurrentSelection = () => {

            const selected = {
                currentTarget: {
                    textContent: schools.value[currentSelectionIndex.value]?.value,
                    dataset: {
                        urn: schools.value[currentSelectionIndex.value]?.data?.urn,
                        postcode: schools.value[currentSelectionIndex.value]?.data?.postcode,
                    }
                }
            }

            handleSchoolSelect(selected)
        }

        const handleClear = () => {
            school.value = null

            if(urn.value?.length > 0){
                resetInput()
            }
        }

        watch(school, (school, prevSchool) => {
            //reset urn and postcode when school value has changed
            if(school !== prevSchool && urn.value?.length > 0 && clearResults.value){
                resetInput();
            }

            clearResults.value = true
        })

        const resetInput = () => {
            urn.value = null
            postcode.value = null

            emit('schoolSelect', school.value, urn.value, postcode.value)

            showDropdown.value = false
            store.clearSchools()
        }


        return {
            attendeeInput,
            attendeeFormErrors,
            school,
            schools,
            urn,
            uniqueId,
            postcode,
            showDropdown,
            schoolDropdown,
            currentSelectionIndex,
            clearResults,
            loadingApi,
            delay,
            handleSchoolSelect,
            handleSchoolInput,
            handleFocus,
            handleClear,
            onArrowDown,
            onArrowUp,
            selectCurrentSelection
        };

    }
})
</script>
