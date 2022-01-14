<template>
    <div :class="[
        'fixed left-1/2 top-0 z-[100] bg-emerald-400 text-emerald-800 px-8 py-2 rounded-bl-lg rounded-br-lg font-bold transition-all duration-500',
        show ? 'opacity-100' : 'opacity-0 pointer-events-none'
    ]">Saved succesfully</div>
</template>

<script lang="ts">

    import { defineComponent } from "vue";

    export default defineComponent({
        props: {
            show: {
                type: Boolean,
                default: false
            }
        }
    })
</script>
