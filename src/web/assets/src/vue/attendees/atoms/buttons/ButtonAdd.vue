<template>
    <button class="bg-emerald-300 text-emerald-800 font-bold py-2 px-3 text-sm rounded-lg cursor-pointer" @click="toggle">{{ showForm ? 'Close form' : 'Add attendee' }}</button>
</template>
<script lang="ts">
    import {defineComponent} from "vue"
    import { useTrainingsStore } from '@/store/trainings'
    import {storeToRefs} from "pinia"

    export default defineComponent({
        setup(){
            const store = useTrainingsStore()
            const { showForm } = storeToRefs(store)

            const toggle = () => {
                store.setShowFrom(true);
            }

            return { showForm, toggle }

        }
    })
</script>
