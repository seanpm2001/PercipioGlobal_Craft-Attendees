<template>
    <div>
        <div class="w-full bg-gray-200 h-1 mb-1 transition-all duration-250 ease-in-out" v-for="data in graph">
            <div :class="[data.class, 'h-1']" :style="`width: ${getValue(data)}%`"></div>
        </div>
    </div>
</template>

<script lang="ts">

    import { defineComponent, onMounted, ref } from "vue"

    export default defineComponent({
        props: {
            event: {
                type: Number
            }
        },
        setup(props) {

            const engagement = ref()
            const total = ref(0)
            const graph = ref([
                {
                    value: 0,
                    class: 'bg-emerald-500',
                },
                {
                    value: 0,
                    class: 'bg-blue-600',
                },
                {
                    value: 0,
                    class: 'bg-yellow-500',
                },
            ])

            const getValue = (data) => {
                const percentage = (data.value/total.value) * 100
                return isNaN(percentage) ? 0 : percentage
            }

            onMounted(() => {

                const ENDPOINT = window?.Attendee?.cpUrl ?? 'https://researchschool.org.uk/cp'

                axios({
                    method: 'get',
                    url: `${ENDPOINT}/craft-attendees/trainings/engagement-data/${props.event}`,
                })
                .then(function (response) {
                    engagement.value = response?.data?.engagement[0]

                    graph.value = [
                        {
                            value:  parseInt(engagement.value?.engaged),
                            class: 'bg-emerald-500',
                        },
                        {
                            value: parseInt(engagement.value?.sustained),
                            class: 'bg-blue-600',
                        },
                        {
                            value: parseInt(engagement.value?.embedded),
                            class: 'bg-yellow-500',
                        },
                    ]

                    total.value = parseInt(engagement.value?.engaged) + parseInt(engagement.value?.sustained) + parseInt(engagement.value?.embedded)
                })
            })

            return { graph, getValue }
        }
    })
</script>
