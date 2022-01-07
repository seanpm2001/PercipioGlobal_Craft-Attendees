<template>
    <div :class="[
        'transition-all duration-500 delay-50 ease-in-out fixed left-0 top-0 w-screen h-screen',
        showForm ? 'z-[200] opacity-100 bg-gray-900 bg-opacity-50 poiner-events-all' : 'z-0 opacity-0 pointer-events-none'
    ]">
        <div class="max-h-screen overflow-auto fixed left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 w-3/4 max-w-4xl">
            <div class="bg-white p-6 rounded-xl mb-10">
                <h3 class="text-lg">Add attendee</h3>
                <form-attendee :csrf="csrf" :event="event" @hideForm="hideForm" />
            </div>
        </div>
    </div>
</template>

<script lang="ts">
import {defineComponent, ref, watchEffect} from 'vue'
import { useAttendeeStore } from '@/store/attendees'
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
        }
    },
    setup(){
        const store = useAttendeeStore()
        const { showForm, attendeeSuccess } = storeToRefs(store)

        const hideForm = () => {
            store.setShowFrom(false)
        }

        watchEffect(() => {
            if(attendeeSuccess.value){
                hideForm()
            }
        })

        return { showForm, hideForm };

    }
})
</script>
