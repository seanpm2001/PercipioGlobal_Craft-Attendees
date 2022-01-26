<template>
    <div class="flex w-full sticky z-20 bg-gray-100 bg-opacity-70 py-4 backdrop-blur" style="top:50px;">
        <p class="text-xl font-bold inline-block flex-grow" style="margin-bottom:0!important">Showing data from <strong>09 September 2021</strong> to <strong>09 December 2021</strong></p>
        <div style="margin-top:0!important">
            <dropdown></dropdown>
        </div>
    </div>
</template>

<script lang="ts">

    // Async load the Vue 3 APIs we need from the Vue ESM
    import { defineComponent } from 'vue';
    import Dropdown from '@/vue/dashboard/atoms/inputs/Dropdown.vue';

    export default defineComponent({
        components: {
            'dropdown': Dropdown,
        }
    });

</script>
