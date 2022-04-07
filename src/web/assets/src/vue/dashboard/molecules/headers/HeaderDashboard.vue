<template>
    <div class="flex w-full sticky z-20 bg-gray-100 bg-opacity-70 py-4 backdrop-blur" style="top:50px;">
        <p class="text-xl font-bold inline-block flex-grow" style="margin-bottom:0!important">Showing data from <strong>{{ start }}</strong> to <strong>{{ end }}</strong></p>
        <div style="margin-top:0!important">
            <dropdown></dropdown>
            <slot></slot>
        </div>
    </div>
</template>

<script lang="ts">

    // Async load the Vue 3 APIs we need from the Vue ESM
    import {defineComponent, ref, watchEffect} from 'vue';
    import Dropdown from '@/vue/dashboard/atoms/inputs/Dropdown.vue';
    import {useDashboardStore} from "@/store/dashboard";
    import {storeToRefs} from "pinia";

    export default defineComponent({
        components: {
            'dropdown': Dropdown,
        },
<<<<<<< HEAD
        props: {
            start: {
                type: String,
                required: true
            },
            end: {
                type: String,
                required: true
            }
        },
=======
        // props: {
        //     start: {
        //           type: String,
        //           required: true
        //     },
        //     end: {
        //         type: String,
        //         required: true
        //     }
        // },
>>>>>>> 2dc83875de400fa9ec27f22097ea5a6af9d81984
        setup(){
            const store = useDashboardStore()
            const { loading, period } = storeToRefs(store)
            const start = ref('')
            const end = ref('')

            watchEffect(() => {
                if(period.value){

                    if(period.value < 12){
                        let today = new Date()
                        end.value = today.getDate() + ' ' + today.toLocaleString('default', { month: 'long' }) + ' ' + today.getFullYear()

                        today.setMonth(today.getMonth() - period.value)
                        start.value = today.getDate() + ' ' + today.toLocaleString('default', { month: 'long' }) + ' ' + today.getFullYear()
                    }else{
                        let endOfSchool = new Date(`31 july ${period.value}`)
                        let startOfSchool = new Date(`01 september ${period.value - 1}`)

                        end.value = endOfSchool.getDate() + ' ' + endOfSchool.toLocaleString('default', { month: 'long' }) + ' ' + endOfSchool.getFullYear()
                        start.value = startOfSchool.getDate() + ' ' + startOfSchool.toLocaleString('default', { month: 'long' }) + ' ' + startOfSchool.getFullYear()
                    }
                }
            })

            return { start, end, loading }
        }
    });

</script>
