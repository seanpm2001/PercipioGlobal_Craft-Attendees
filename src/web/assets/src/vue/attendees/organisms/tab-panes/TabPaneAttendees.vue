<template>

    <!-- TAB NAVIGATION  -->
    <nav class="mt-10 w-full relative z-10">
        <ul role="menubar" class="flex">
            <li
                role="menuitem"
                @click="handleTabNavigation('attendee')"
                :class="[
                    'font-bold text-base text-gray-600 border-b-2 border-solid hover:border-blue-600 pb-2 cursor-pointer',
                    activePane === 'attendee' ? 'border-blue-600' : 'border-gray-100'
                ]"
            >
                Attendees
            </li>
            <span class="inline-flex w-4"></span>
            <li
                role="menuitem"
                @click="handleTabNavigation('support')"
                :class="[
                    'font-bold text-base text-gray-600 border-b-2 border-solid hover:border-blue-600 pb-2 cursor-pointer',
                    activePane === 'support' ? 'border-blue-600' : 'border-gray-100'
                ]"
            >
                Follow on support
            </li>
            <span class="inline-flex w-4"></span>
            <li
                role="menuitem"
                @click="handleTabNavigation('logs')"
                :class="[
                    'font-bold text-base text-gray-600 border-b-2 border-solid hover:border-blue-600 pb-2 cursor-pointer',
                    activePane === 'logs' ? 'border-blue-600' : 'border-gray-100'
                ]"
            >
                Import logs
            </li>
        </ul>
    </nav>

    <!-- ATTENDEE  -->
    <section v-if="activePane === 'attendee'" class="bg-gray-100 p-6 pt-10 -mt-2.5 -ml-6 relative">

        <div class="w-full flex mb-10">
            <div class="heading flex-grow">
                <label class="text-xl font-bold inline-block w-full mb-2">Attendees</label>
                <span>Use the action buttons to either bulk upload from a CSV file or manually manage attendee data.<br/><a :href="csv" target="_blank" download class="text-blue-600 cursor-pointer inline-block">Download CSV template</a> to make the field mapping easier.</span>
            </div>
            <!--a href="#" class="bg-gray-300 text-gray-800 font-bold py-2 px-3 text-sm rounded-lg">Import</a-->


            <form-import :csrf="csrf" :event="event" :site="site" />
            <button-add label="Add"></button-add>
        </div>

        <popup-add-attendee :csrf="csrf" :event="event" :site="site"></popup-add-attendee>
        <list-attendees :csrf="csrf" :event="event" :site="site"></list-attendees>

        <div class="block bg-gray-100 w-6 left-full pb-6 top-0 h-full absolute"></div>
        <div class="block bg-gray-100 h-6 top-full left-0 w-full absolute"></div>

    </section>

    <!-- FOLLOW ON SUPPORT  -->
    <section v-if="activePane === 'support'" class="bg-gray-100 p-6 pt-10 -mt-2.5 -ml-6 relative">

        <div class="w-full flex mb-10">
            <div class="heading flex-grow">
                <label class="text-xl font-bold  inline-block w-full mb-2">Follow on support</label>
                <span>Indicate the relevant follow on support offered to attendees of the event.</span>
            </div>
        </div>

        <list-support :csrf="csrf" :event="event"></list-support>

        <div class="block bg-gray-100 w-6 left-full pb-6 top-0 h-full absolute"></div>
        <div class="block bg-gray-100 h-6 top-full left-0 w-full absolute"></div>

    </section>

    <!-- LOGS  -->
    <section v-if="activePane === 'logs'" class="bg-gray-100 p-6 pt-10 -mt-2.5 -ml-6 relative">

        <div class="w-full flex mb-10">
            <div class="heading flex-grow">
                <label class="text-xl font-bold  inline-block w-full mb-2">Import logs</label>
                <span>All the logs for this event that occurred after importing a CSV will be shown bellow. They will be saved for one week.</span>
            </div>
        </div>

        <list-logs :event="event"/>

        <div class="block bg-gray-100 w-6 left-full pb-6 top-0 h-full absolute"></div>
        <div class="block bg-gray-100 h-6 top-full left-0 w-full absolute"></div>

    </section>

</template>
<script lang="ts">
    import { defineComponent, ref } from 'vue'
    import ButtonAdd from '@/vue/attendees/atoms/buttons/ButtonAdd.vue'
    import InputActions from '@/vue/attendees/atoms/inputs/InputActions.vue';
    import ListAttendees from '@/vue/attendees/organisms/lists/ListAttendees.vue'
    import ListLogs from '@/vue/attendees/organisms/lists/ListLogs.vue'
    import ListSupport from '@/vue/attendees/organisms/lists/ListSupport.vue'
    import PopupAddAttendee from "@/vue/attendees/organisms/popups/PopupAddAttendee.vue"
    import FormImport from "@/vue/attendees/organisms/forms/FormImport.vue"

    export default defineComponent({
        components: {
            'button-add': ButtonAdd,
            'form-import': FormImport,
            'popup-add-attendee': PopupAddAttendee,
            'list-attendees': ListAttendees,
            'list-logs': ListLogs,
            'list-support': ListSupport
        },
        props: {
            csrf: {
                type: String,
                required: true
            },
            event: {
                type: String,
                required: true
            },
            site: {
                type: String,
                required: true
            },
            csv: {
                type: String,
                required: true
            },
            active: {
                type: String,
                default: 'attendee'
            }
        },
        setup(props) {

            const activePane = ref(props.active.length > 0 ? props.active : 'attendee')

            const handleTabNavigation = (pane) => {
                activePane.value = pane
            }

            return { activePane, handleTabNavigation }
        }
    })
</script>
