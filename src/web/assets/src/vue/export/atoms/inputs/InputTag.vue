<template>

    <div class="w-full relative">
        <input
            name="tag"
            v-model="tag"
            @input="delay"
            @focus="handleFocus(true)"
            @blur="handleFocus(false)"
            @keydown.down.prevent="onArrowDown"
            @keydown.up.prevent="onArrowUp"
            @keydown.enter.prevent="selectCurrentSelection"
            :class="[
                'block peer w-full px-2 py-2 text-sm text-gray-600 box-border bg-gray-100 rounded-lg pr-7',
                errors?.tags ? 'border-solid border-red-300' : 'border-solid border-gray-100'
            ]"
            placeholder="Search for a tag"
            :aria-owns="`metaseed-list-${uniqueId}`"
            aria-autocomplete="list"
            role="combobox"
        />

        <span v-if="tag?.length > 3" @click="handleClear" class="text-blue-800 bg-gray-100 block w-6 text-center flex items-center justify-center h-8 absolute right-0 top-1 cursor:pointer">&#x2715</span>

        <ul
            class="absolute right-0 top-full mt-1 w-128 max-h-52 overflow-scroll z-10 bg-gray-100 rounded-lg shadow-xl"
            v-show="showDropdown"
            ref="schoolDropdown"
            :aria-expanded="showDropdown"
            role="listbox"
            :id="`tag-list-${uniqueId}`"
        >
            <li
                v-if="!loading"
                v-for="(tag, i) in tags"
                :key="tag?.slug"
                role="option"
            >
                <span
                    @mousedown="handleTagSelect"
                    @touch="handleTagSelect"
                    role="button"
                    :data-tag="tag?.title"
                    :class="[
                     'p-2 pointer-all hover:bg-blue-600 hover:text-white focus:bg-blue-600 block group',
                     currentSelectionIndex === i ? 'bg-blue-600 text-white' : ''
                    ]"
                >
                    <span class="block w-full text-sm font-medium">{{tag.title}}</span>
                </span>
            </li>

            <li
                v-if="loading"
                class="p-2 pt-3 inline-block"
            >
                <svg class="animate-spin mr-1 h-4 w-4 text-gray-800" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </li>
        </ul>
    </div>

</template>

<script lang="ts">
import {defineComponent, watchEffect, watch, ref} from 'vue'
import { useExportStore } from '@/store/export'
import { storeToRefs } from 'pinia'

export default defineComponent({
    setup( props, {emit} ){
        const schoolDropdown = ref(null)
        const store = useExportStore()
        const uniqueId = ref(Math.floor(Math.random() * 100) + Date.now())
        const {
            tags,
            loading
        } = storeToRefs(store)
        const tag = ref('')
        const timer = ref(null)
        const showDropdown = ref(false)
        const currentSelectionIndex = ref(0)
        const clearResults = ref(true)
        const errors = ref({})

        const handleTagSelect = evt => {
            tag.value = evt.currentTarget?.dataset?.tag

            showDropdown.value = false
            store.clearTags()
        }

        const handleTagInput = () => {

            clearResults.value = true

            if(tag.value.length > 2){
                showDropdown.value = true;
                store.fetchTags(tag.value)
            }
        }

        const handleFocus = (val) => {
            if (tag.value?.length === 0) {
                store.clearTags()
            }
            showDropdown.value = val
        }

        const delay = () => {
            if(timer.value){
                clearTimeout(timer.value)
            }
            timer.value = setTimeout(handleTagInput, 200)
        }

        const onArrowDown = () => {
            currentSelectionIndex.value = currentSelectionIndex.value < tags.value?.length - 1 ? currentSelectionIndex.value + 1 : tags.value?.length - 1
            schoolDropdown.value.querySelectorAll('li')[currentSelectionIndex.value].scrollIntoView({ block: 'nearest' })
        }

        const onArrowUp = () => {
            currentSelectionIndex.value = currentSelectionIndex.value > 0 ? currentSelectionIndex.value - 1 : 0
            schoolDropdown.value.querySelectorAll('li')[currentSelectionIndex.value].scrollIntoView({ block: 'nearest' })
        }

        const selectCurrentSelection = () => {

            tag.value = tags.value[currentSelectionIndex.value]?.title
            showDropdown.value = false
            store.clearTags()
        }

        const handleClear = () => {
            tag.value = null
            resetInput()
        }

        const resetInput = () => {
            showDropdown.value = false
            store.clearTags()
        }


        return {
            tag,
            showDropdown,
            schoolDropdown,
            currentSelectionIndex,
            clearResults,
            loading,
            errors,
            uniqueId,
            tags,
            delay,
            handleTagSelect,
            handleTagInput,
            handleFocus,
            handleClear,
            onArrowDown,
            onArrowUp,
            selectCurrentSelection
        };

    }
})
</script>
