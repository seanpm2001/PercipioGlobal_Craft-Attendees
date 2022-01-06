<template>

    <div class="inline-block relative">
        <input
            :name="name"
            type="checkbox"
            class="peer sr-only"
            value="1"
            :checked="checkedValue"
            @click="toggle"
        >

        <div class="peer-focus:ring-2 peer-focus:ring-opacity-50 peer-focus:ring-blue-700 peer-focus:shadow-lg outline-none rounded-full inline-block cursor-pointer">
            <div :class="[
                'block h-6 rounded-full w-10 cursor-pointer bg-opacity-100',
                checkedValue == 1 ? 'bg-emerald-100' : 'bg-indigo-100'
            ]"></div>

            <div :class="[
                'absolute w-4 h-4 transition rounded-full left-1 top-1 flex items-center not-sr-only',
                checkedValue == 1 ? 'bg-emerald-700 transform translate-x-full' : 'bg-gray-400'
            ]">
            </div>
        </div>

    </div>


</template>

<script lang="ts">
import { defineAsyncComponent, defineComponent, ref } from 'vue'
import { useModelWrapper } from '@/js/utils/modelWrapper'

export default defineComponent({

    props: {
        label: {
            type: String,
            required: false,
        },

        name: {
            type: String,
            required: true
        },

        checked: {
            default: 0
        },

    },

    setup(props) {

        const checkedValue = ref(props.checked == 0 ? 0 : 1);

        const toggle = () => {
            checkedValue.value = checkedValue.value == 0 ? 1 : 0
        }

        return { toggle, checkedValue }

    },

})
</script>
