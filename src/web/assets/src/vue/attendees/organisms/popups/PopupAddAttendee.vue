<template>
    <div :class="[
        'transition-all duration-500 delay-50 ease-in-out fixed left-0 top-0 w-screen h-screen',
        showForm ? 'z-[100] opacity-100 bg-gray-900 bg-opacity-50 poiner-events-all' : 'z-0 opacity-0 pointer-events-none'
    ]">
        <div class="max-h-screen overflow-auto fixed left-1/2 top-1/2 w-3/4 max-w-4xl translate-center width-128">
            <div class="bg-white px-6 pb-6 rounded-xl mb-10">
                <div class="relative">
                    <h3 class="text-lg pt-6">Add attendee</h3>
                    <form-attendee :csrf="csrf" :event="event" :site="site" @hideForm="hideForm" @saveAnother="setAnotherSave" v-if="showForm" />
                </div>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
    import {defineComponent, ref, watchEffect, nextTick} from 'vue'
    import { useTrainingsStore } from '@/store/trainings'
    import { storeToRefs } from 'pinia'
    import FormAttendee from '@/vue/attendees/organisms/forms/FormAttendee.vue';

    export default defineComponent({
        components: {
            'form-attendee': FormAttendee,
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
            site: {
                type: String,
                required: true
            }
        },
        setup(){
            const store = useTrainingsStore()
            const { showForm, attendeeSuccess } = storeToRefs(store)
            const addAnother = ref(false)

            const hideForm = () => {
                store.setShowFrom(false)
            }

            const setAnotherSave = (val) => {
                addAnother.value = val
            }

            watchEffect(() => {
                if(attendeeSuccess.value){
                    hideForm()

                    if(addAnother.value == "true"){

                        nextTick(() => {
                            store.setShowFrom(true)
                        })

                    }
                }
            })

            return { showForm, setAnotherSave, hideForm };

        }
    })
</script>
