<template>
    <article class="bg-white rounded-xl p-4">
        <span class="flex items-end pb-2" style="align-items: end">
            <span class="text-6xl block text-blue-600" style="margin-bottom:0!important">{{ value }}</span>
            <span class="block pb-1" style="margin-bottom:0!important">{{ type }}</span>
        </span>
        <span class="block pb-1 text-xs">Avg. <strong>{{ average }}</strong> {{ description }}</span>
    </article>
</template>

<script lang="ts">

    import { defineComponent } from "vue"

    export default defineComponent({
        props: {
            value: {
                type: Number,
                required: true
            },
            type: {
                type: String,
                required: true
            },
            average: {
                type: Number,
                required: true
            },
            description: {
                type: String,
                required: true
            }
        }
    })

</script>
