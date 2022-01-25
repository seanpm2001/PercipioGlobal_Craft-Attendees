<template>
    <div class="relative" v-click-away="onClickAway">
        <button class="bg-blue-600 text-white font-bold py-2 px-3 text-sm rounded-lg cursor-pointer flex items-center" @click="handleActionsToggle">Actions <span class="inline-block ml-1 text-lg -mt-2" style="margin-bottom:0!important">⌄</span></button>
        <transition leave-active-class="transition ease-in duration-100" leave-from-class="opacity-100" leave-to-class="opacity-0">
            <ul class="absolute top-full right-0 z-10 min-w-40 bg-white drop-shadow-xl rounded-md mt-2" v-if="show">
                <li class="whitespace-nowrap">
                    <button class="p-3 w-full text-left cursor-pointer text-red-600 font-bold flex flex-nowrap" @click="handleDeleteToggle">
                        Delete selection ({{ attendees.length}})
                        <svg v-if="loading" class="animate-spin ml-2 mt-1 h-4 w-4 text-gray-400 inline-block -mt-px" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" style="margin-bottom: 0!important;">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </button>
                </li>
            </ul>
        </transition>
        <popup-delete-attendee :show="showDelete" :csrf="csrf" :attendees="attendees" @hidePopup="handleDeleteToggle" />
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
        emits: ["reset"],
        directives: {
            ClickAway: directive
        },
        props: {
            attendees: {
                type: Array,
                required: true
            },
            csrf: {
                type: String,
                required: true
            }
        },
        setup(props,{emit}){
            const store = useTrainingsStore()
            const { loading } = storeToRefs(store)
            const show = ref(false)
            const showDelete = ref(false)
            const state = ref('')

            const handleActionsToggle = () => {
                show.value = !show.value
            }

            const handleDeleteToggle = (action) => {
                showDelete.value = !showDelete.value
                show.value = false

                if(action !== 'delete'){
                    state.value = 'delete'
                }
            }

            const onClickAway = () => {
                show.value = false
            }

            watch(loading, (loading, prevLoading) => {
                if(loading !== prevLoading){
                    show.value = false

                    if(state.value === 'delete'){
                        emit('reset')
                    }
                }
            })

            return { show, showDelete, loading, onClickAway, handleActionsToggle, handleDeleteToggle }
        }
    })
</script>
