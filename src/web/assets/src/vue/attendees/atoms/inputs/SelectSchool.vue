<template>

    <input type="hidden" name="orgUrn" :value="urn ?? ''">
    <input type="hidden" name="priority" :value="priority ?? 0">

    <label class="block mb-6">
        <span class="text-xs font-bold text-gray-400 block mb-1">Saved school or organisation</span>
        <div class="w-full relative">
            <input
                name="orgName"
                v-model="values.orgName"
                disabled
                :class="[
                    'block peer w-full px-2 py-2 text-sm text-gray-600 box-border bg-gray-50 rounded-lg pr-7 border-solid border-gray-50'
                ]"
                :aria-owns="`metaseed-list-${uniqueId}`"
                aria-autocomplete="list"
                role="combobox"
            />
        </div>
    </label>

    <label class="block mb-6">
        <span class="text-xs font-bold text-gray-400 block mb-1">Do you mean one of these schools?</span>
        <ul
            class="bg-gray-100 rounded-xl w-full max-h-60 overflow-auto"
            ref="schoolDropdown"
            role="listbox"
        >
            <li
                v-if="!loadingApi"
                v-for="(option, i) in schools"
                :key="option?.data?.urn ?? i"
                role="option"
            >
                <span
                    v-if="option?.data"
                    @mousedown="handleSchoolSelect"
                    @touch="handleSchoolSelect"
                    role="button"
                    :data-postcode="option?.data?.postcode"
                    :data-urn="option?.data?.urn"
                    :data-name="option?.value"
                    :class="[
                     'p-4 cursor-pointer flex flex-nowrap focus:bg-blue-600 group',
                     'border-b border-gray-200 border-solid',
                     school == option.value && urn === option?.data?.urn ? 'bg-blue-600 hover:bg-blue-600' : 'bg-gray-100 hover:bg-gray-200'
                    ]"
                >
                    <div class="rounded-md border border-gray-400 border-solid w-4 h-4 bg-white flex-grow-0 flex-shrink-0 relative text-blue-600">
                        <span v-if="school == option.value && urn === option?.data?.urn" class="font-bold absolute left-1/2 top-1/2 transform -translate-y-1/2 -translate-x-1/2">✓</span>
                    </div>
                    <div class="pl-2">
                        <span :class="[
                            'block w-full text-sm font-medium',
                            school == option.value && urn === option?.data?.urn ? 'text-white' : 'text-gray-500'
                        ]" style="margin-bottom:0!important">{{option.value}}</span>
                        <span :class="[
                            'block text-xs',
                            school == option.value && urn === option?.data?.urn ? 'text-white' : 'text-gray-500'
                        ]" style="margin-bottom:0!important">
                            [URN: {{ option?.data?.urn ?? '-' }}] {{ option?.data?.street ?? '-' }}, {{ option?.data?.town ?? '-' }} {{ option?.data?.postcode }} (Age {{ option?.data?.ageFrom ?? '/' }} - {{ option?.data?.ageTo ?? '/' }})
                        </span>
                    </div>
                </span>
                <span v-else class="p-4 block">
                    There's no school that matches the saved school or organisation
                </span>
            </li>

            <!--li
                v-if="!loadingApi"
            >
                <span
                    @mousedown="handleClear"
                    @touch="handleClear"
                    role="button"
                    data-postcode=""
                    data-urn=""
                    data-name=""
                    :class="[
                     'p-4 cursor-pointer flex flex-nowrap hover:bg-blue-600 focus:bg-blue-600 group',
                     school === '' ? 'bg-blue-600' : 'bg-gray-100'
                    ]"
                >
                    <div class="rounded-full border border-gray-400 border-solid w-4 h-4 bg-white flex-grow-0 flex-shrink-0">
                        <span v-if="school === ''" class="rounded-full bg-blue-600 w-2 h-2 block m-1"></span>
                    </div>
                    <div class="pl-2">
                        <span :class="[
                            'block w-full text-sm font-medium group-hover:text-white',
                            school === '' ? 'text-white' : 'text-gray-500'
                        ]" style="margin-bottom:0!important">Don't select any of the schools</span>
                    </div>
                </span>
            </li-->

            <li
                v-if="loadingApi"
                class="p-2 pt-3 inline-block"
            >
                <svg class="animate-spin mr-1 h-4 w-4 text-gray-800" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </li>
        </ul>
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
        },
    },
    emits: ["schoolSelect", "schoolInput"],
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
        const priority = ref(attendeeInput.value?.priority ?? props.values?.priority ?? 0)
        const timer = ref(null)
        const clearResults = ref(true)

        store.fetchSchools(school.value)

        const handleSchoolSelect = evt => {

            if(school.value === evt.currentTarget?.dataset?.name && urn.value === evt.currentTarget?.dataset?.urn){
                handleClear()
            }else{
                school.value = evt.currentTarget?.dataset?.name
                urn.value = evt.currentTarget?.dataset?.urn
                postcode.value = evt.currentTarget?.dataset?.postcode
                priority.value = evt.currentTarget?.dataset?.priority

                emit('schoolSelect', school.value, urn.value, postcode.value, priority.value)
            }

        }

        const handleClear = () => {
            school.value = ""
            urn.value = null
            postcode.value = ""
            priority.value = 0

            emit('schoolSelect', null, null, null)
            // school.value = null
            //
            // if(urn.value?.length > 0){
            //     resetInput()
            // }
        }

        // const resetInput = () => {
        //     urn.value = null
        //     postcode.value = null
        //
        //     emit('schoolSelect', school.value, urn.value, postcode.value)
        // }

        // const selectCurrentSelection = () => {
        //
        //     const selected = {
        //         currentTarget: {
        //             dataset: {
        //                 name: schools.value[currentSelectionIndex.value]?.value,
        //                 urn: schools.value[currentSelectionIndex.value]?.data?.urn,
        //                 postcode: schools.value[currentSelectionIndex.value]?.data?.postcode,
        //             }
        //         }
        //     }
        //
        //     handleSchoolSelect(selected)
        // }

        // watch(school, (school, prevSchool) => {
        //     //reset urn and postcode when school value has changed
        //     if(school !== prevSchool && urn.value?.length > 0 && clearResults.value){
        //         resetInput();
        //     }
        //
        //     emit('schoolInput', school ? school : '')
        //
        //     clearResults.value = true
        // })

        return {
            attendeeInput,
            attendeeFormErrors,
            school,
            schools,
            priority,
            urn,
            uniqueId,
            postcode,
            schoolDropdown,
            clearResults,
            loadingApi,
            handleSchoolSelect,
            handleClear,
        };

    }
})
</script>
