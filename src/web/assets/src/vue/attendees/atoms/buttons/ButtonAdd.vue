<template>
    <button class="bg-emerald-300 text-emerald-800 font-bold py-2 px-3 text-sm rounded-lg cursor-pointer" @click="toggle">{{ showForm ? 'Close form' : 'Add attendee' }}</button>
</template>
<script lang="ts">
    import {defineComponent} from "vue"
    import { useAttendeeStore } from '@/store/attendees'
    import {storeToRefs} from "pinia"

    export default defineComponent({
        setup(){
            const store = useAttendeeStore()

            const toggle = () => {
                store.setShowFrom(true);
            }

            return { toggle }

        }
    })
</script>
