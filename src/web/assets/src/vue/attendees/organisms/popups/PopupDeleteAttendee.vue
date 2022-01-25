<template>
    <div :class="[
        'transition-all duration-500 delay-50 ease-in-out fixed left-0 top-0 w-screen h-screen',
        show ? 'z-[100] opacity-100 bg-gray-900 bg-opacity-50' : 'z-0 opacity-0 pointer-events-none'
    ]">
        <div class="max-h-screen text-center overflow-auto fixed left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 w-80">
            <div class="bg-white p-6 rounded-xl mb-10">
                <h3 class="text-lg">Delete <span v-if="attendees.length < 2">attendee</span><span v-else>{{attendees.length }} attendees</span></h3>

                <div class="flex justify-center">
                    <button @click="handleCancel" class="block bg-gray-300 text-gray-800 font-bold mt-2 py-2 px-3 text-sm rounded-lg cursor-pointer">
                        Cancel
                    </button>
                    <button @click="handleDelete" class="block bg-red-300 text-red-800 font-bold mt-2 py-2 px-3 text-sm rounded-lg cursor-pointer">
                        Yes, delete <span v-if="attendees.length < 2">attendee</span><span v-else>attendees</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
    import {defineComponent, ref, watchEffect} from 'vue'
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
            attendees: {
                type: Array,
                required: true
            },
            show: {
                type: Boolean,
                required: true,
            }
        },
        setup(props, {emit}){
            const store = useTrainingsStore()
            const { showForm, attendeeSuccess } = storeToRefs(store)

            const handleDelete = () => {
                let deleteAttendees = {}
                deleteAttendees.CRAFT_CSRF_TOKEN = props.csrf
                deleteAttendees.action = 'actions/craft-attendees/training/delete'
                deleteAttendees.attendees = props.attendees
                store.deleteAttendees(deleteAttendees)
                emit('hidePopup', 'delete')
            }

            const handleCancel = () => {
                emit('hidePopup', 'cancel')
            }

            return { showForm, handleCancel, handleDelete };

        }
    })
</script>
