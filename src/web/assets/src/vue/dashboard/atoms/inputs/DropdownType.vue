<template>
    <select
        name="type"
        class="block h-10 px-1 rounded-md bg-gray-300 border-gray-300"
        :disabled="loading"
        @change="handleTypeChanged"
    >
        <option value="16,17,25">
            All Types
        </option>
        <option
            v-for="type in types"
            :key="type.id" 
            :value="type.id">
            {{ type.name }}
        </option>
    </select>
</template>

<script lang="ts">
    import { defineComponent } from "vue"
    import { useDashboardStore } from "@/store/dashboard"
    import { storeToRefs } from "pinia"

    export default defineComponent({

        props: {
            types: {
                type: Object,
                required: true,
            },
            selected: {
                type: String,
                required: true,
            }
        },

        setup() {
            const store = useDashboardStore()
            const { loading, type } = storeToRefs(store)

            const handleTypeChanged = (evt) => {                
                store.type = parseInt(evt.currentTarget.value)
                store.fetchEvents()
            }

            return { loading, type, handleTypeChanged }
        }
    })
</script>
