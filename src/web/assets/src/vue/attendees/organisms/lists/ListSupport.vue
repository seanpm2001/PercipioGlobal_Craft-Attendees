<template>

    <div class="bg-white w-full p-6 rounded-xl box-border">
        <div
            v-for="option in followOnSupportOptions"
            class="block flex items-center"
        >
            <input-switch name="option" :checked="getChecked(option)" :label="option.name" :value="option.value" @toggle="handleChange" />
        </div>
    </div>

    <div v-if="loading" class="bg-gray-200 bg-opacity-50 absolute left-0 top-0 w-full h-full">
        <div class="absolute left-1/2 top-1/2 -translate-y-1/2 -translate-x-1/2">
            <svg class="animate-spin h-10 w-10 text-gray-800" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        </div>

        <div class="block bg-gray-200 bg-opacity-50 w-6 left-full pb-6 top-0 h-full absolute z-10"></div>
        <div class="block bg-gray-200 bg-opacity-50 h-6 top-full left-0 w-full absolute z-10"></div>
    </div>
</template>

<script lang="ts">
    import {defineComponent, ref} from 'vue'
    import { useTrainingsStore } from '@/store/trainings'
    import { storeToRefs } from 'pinia'
    import InputSwitch from '@/vue/attendees/atoms/inputs/InputSwitch.vue'

    export default defineComponent({
        components: {
            'input-switch': InputSwitch,
        },
        props: {
            event: {
                type: String,
                required: true
            },
            csrf: {
                type: String,
                required: true
            },
        },
        setup(props){
            const store = useTrainingsStore()
            const { followOnSupportOptions, followOnSupportSelectedOptions , loading } = storeToRefs(store)

            const handleChange = (value) => {
                let option = {}
                option.value = value
                option.CRAFT_CSRF_TOKEN = props.csrf
                option.event = props.event
                option.action = 'actions/craft-attendees/training/saveOption'

                store.saveOption(option)
            }

            const getChecked = (option) => {
                const index = store.followOnSupportSelectedOptions.filter(e => e.optionId == option.id);
                return index.length > 0 ? 1 : 0
            }

            store.fetchOptions(props.event)

            return { followOnSupportOptions, followOnSupportSelectedOptions, loading, handleChange, getChecked }
        }
    })
</script>
