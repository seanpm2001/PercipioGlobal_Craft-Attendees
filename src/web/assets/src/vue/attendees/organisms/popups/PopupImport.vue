<template>
    <div :class="[
        'transition-all duration-500 delay-50 ease-in-out fixed left-0 top-0 w-screen h-screen',
        show ? 'z-[100] opacity-100 bg-gray-900 bg-opacity-50' : 'z-0 opacity-0 pointer-events-none'
    ]">
        <div class="max-h-screen overflow-auto fixed left-1/2 top-1/2 translate-center w-80">
            <div class="bg-white p-6 rounded-xl mb-10 relative">

                <div class="w-full" v-if="info === 1">
                    <div class="w-full mb-2">
                        <h3 class="text-lg">Organisation / School</h3>
                    </div>

                    <p>Input expected: Text (Required)</p>
                    <p>This field is the name of the school or organisation associated with the attendee. School names will attempt to match to the schools database, so try to be as accurate as possible.</p>
                </div>

                <div class="w-full" v-if="info === 2">
                    <div class="w-full mb-2">
                        <h3 class="text-lg">URN</h3>
                    </div>

                    <p>Input expected: Numeric (Not Required)</p>
                    <p>This is the school's Unique Reference Number (URN). This field is not required and can be autocompleted during the import process if not known.<br/><br/>If the cell in the corresponding CSV row contains non-numeric characters or is longer than 8 characters, incorrect formatting will be assumed and null data will be entered.</p>
                </div>

                <div class="w-full" v-if="info === 3">
                    <div class="w-full mb-2">
                        <h3 class="text-lg">Postcode</h3>
                    </div>

                    <p>Input expected: Alphanumeric (Not required)</p>
                    <p>This is the school's post code. This field is not required and can be autocompleted during the import process, however it will assist when trying to match similarly named schools.  <br/><br/>If the cell in the corresponding CSV row exceeds more than 7 characters in length, incorrect formatting will be assumed and null data will be entered.</p>
                </div>

                <div class="w-full" v-if="info === 4">
                    <div class="w-full mb-2">
                        <h3 class="text-lg">Attendee Name</h3>
                    </div>

                    <p>Input expected: Text (Required)</p>
                    <p>The full name of the participant attending the event.<br/><br/>If the cell in the corresponding CSV row exceeds more than 256 characters in length, incorrect formatting will be assumed and null data will be entered. </p>
                </div>

                <div class="w-full" v-if="info === 5">
                    <div class="w-full mb-2">
                        <h3 class="text-lg">Attendee Email Address</h3>
                    </div>

                    <p>Input expected: Valid email or blank (Not Required)</p>
                    <p>This field is the valid email of the participant.</p>
                </div>

                <div class="w-full" v-if="info === 6">
                    <div class="w-full mb-2">
                        <h3 class="text-lg">Job Role</h3>
                    </div>

                    <p>Input expected: Text from a predefined list (Required)</p>

                    <ul class="text-left">
                        <li><strong>na</strong>: Not Applicable</li>
                        <li><strong>support</strong>: Support Staff</li>
                        <li><strong>leader-middle</strong>: Middle Leader</li>
                        <li><strong>leader</strong>: Leadership</li>
                        <li><strong>teacher</strong>: Teacher</li>
                    </ul>

                    <p>If the cell in the corresponding CSV row does not match one of the list items above, Not Appicable or NA will be selected. </p>
                </div>

                <div class="w-full" v-if="info === 7">
                    <div class="w-full mb-2">
                        <h3 class="text-lg">Modules Attended</h3>
                    </div>

                    <p>Input expected: Numeric from 1-15 (Not Required)</p>

                    <p>This field represents the number of modules the attendee participated in this event. A number from 1 to 15 is expected.<br/><br/>If the cell in the corresponding CSV row does not match 1-15, 1 will be automatically assumed.</p>
                </div>

                <div class="w-full" v-if="info === 8">
                    <div class="w-full mb-2">
                        <h3 class="text-lg">Newsletter</h3>
                    </div>

                    <p>Input expected: yes/no or y/n (Not Required)</p>

                    <p>This field informs us if the user wishes their email address be signed up to your Research School's mailing list.<br/><br/>If the cell in the corresponding CSV row does not match any of the expected inputs, no will be automatically assumed. </p>
                </div>

                <button @click="handleClose" class="block bg-gray-300 text-gray-800 font-bold mt-2 py-2 px-3 text-sm rounded-lg cursor-pointer absolute right-2 top-0">
                    ✕
                </button>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
    import {defineComponent, ref, watchEffect} from 'vue'

    export default defineComponent({
        props: {
            show: {
                type: Boolean,
                required: true,
            },
            info: {
                type: String
            }
        },
        setup(props, {emit}){

            const handleClose = () => {
                emit('hidePopup')
            }

            return { handleClose };

        }
    })
</script>
