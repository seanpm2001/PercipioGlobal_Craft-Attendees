<template>
    <article class="bg-white rounded-xl p-4">
        <div v-if="loading">
            <span class="animate-pulse flex items-end pb-2" style="align-items: end">
                <span class="text-6xl block text-blue-600 bg-blue-600 bg-opacity-10 rounded-md" style="margin-bottom:0!important">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
                <span class="block pb-1 bg-gray-200 bg-opacity-50 flex-grow rounded-md" style="margin-bottom:0!important">&nbsp;</span>
            </span>
            <span class="animate-pulse block pb-1 text-xs bg-gray-200 bg-opacity-50 rounded-md">&nbsp;</span>
        </div>
        <div v-else class="flex items-center justify-center w-full h-full font-bold">
            There is no data to preview
        </div>
    </article>
</template>

<script lang="ts">

    import { defineComponent } from "vue"
    import {useDashboardStore} from "@/store/dashboard";
    import {storeToRefs} from "pinia";

    export default defineComponent({
        setup(){
            const store = useDashboardStore()
            const { loading } = storeToRefs(store)

            return { loading }
        }
    })

</script>
