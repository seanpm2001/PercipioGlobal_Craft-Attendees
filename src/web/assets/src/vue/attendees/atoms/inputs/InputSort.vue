<template>
    <div class="relative" v-click-away="onClickAway">
        <button class="bg-white text-blue-600 font-bold py-2 px-3 text-sm rounded-lg cursor-pointer flex items-center" @click="handleActionsToggle">Sort By:
            {{ selected.label }} <span class="inline-block ml-1 text-lg -mt-2" style="margin-bottom:0!important">⌄</span></button>
        <transition leave-active-class="transition ease-in duration-100" leave-from-class="opacity-100" leave-to-class="opacity-0">
            <ul class="absolute top-full left-0 z-10 min-w-40 bg-white drop-shadow-xl rounded-md mt-2" v-if="show">
                <li class="whitespace-nowrap" v-for="option in actions" :key="option.action">
                    <button
                        :class="[
                            'p-3 w-full text-left cursor-pointer font-bold flex flex-nowrap hover:text-blue-600',
                            order === option.action ? 'text-blue-600' : 'text-gray-600'
                        ]"
                        @click="handleSort(option.action)"
                    >
                        {{ option.label }}
                    </button>
                </li>
            </ul>
        </transition>
    </div>

    <div v-if="loading" class="absolute left-0 top-0 z-10 w-full h-full">&nbsp; </div>
</template>
<script lang="ts">
import {defineComponent, ref, watch} from "vue";
import {storeToRefs} from "pinia";
import PopupDeleteAttendee from '@/vue/attendees/organisms/popups/PopupDeleteAttendee.vue'
import { directive } from "vue3-click-away"
import { useTrainingsStore } from '@/store/trainings'

export default defineComponent({
    components: {
        'popup-delete-attendee': PopupDeleteAttendee,
    },
    emits: ["fetch"],
    directives: {
        ClickAway: directive
    },
    setup(props,{emit}){
        const store = useTrainingsStore()
        const { order, loading } = storeToRefs(store)
        const show = ref(false)
        const actions = ref([
            {
                id: 1,
                action: 'date',
                label: 'Date Created'
            },
            {
                id: 2,
                action: 'org',
                label: 'School / Organisation'
            },
            {
                id: 3,
                action: 'attendee',
                label: 'Attendee'
            },
            {
                id: 4,
                action: 'approved',
                label: 'Verified'
            }
        ])
        const selected = ref(actions.value[0])

        const handleActionsToggle = () => {
            show.value = !show.value
        }

        const handleSort = (action) => {
            show.value = false
            selected.value = actions.value.find(a => a.action === action)
            emit('fetch', action)
        }

        const onClickAway = () => {
            show.value = false
        }

        return { loading, order, actions, show, selected, onClickAway, handleActionsToggle, handleSort }
    }
})
</script>
