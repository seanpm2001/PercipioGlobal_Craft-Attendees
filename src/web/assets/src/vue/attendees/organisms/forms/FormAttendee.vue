<template>
    <form ref="form" method="post" action="" accept-charset="UTF-8" class="grid grid-cols-11 border-b border-l border-r border-gray-200 border-solid">
        <input type="hidden" name="action" value="actions/craft-attendees/training/save ">
        <input type="hidden" name="event" :value="event">
        <input type="hidden" name="CRAFT_CSRF_TOKEN" :value="csrf">

        <label class="col-span-2 text-sm box-border p-1">
            <span class="sr-only">School / Organisation</span>
            <input name="orgName" class="block w-full px-2 py-2 mt-1 text-sm text-gray-600 appearance-none box-border border-none" placeholder="Search for a school or organisation" />
        </label>
        <label class="box-border p-1">
            <span class="sr-only">Post code</span>
            <input name="postCode" class="block w-full px-2 py-2 mt-1 text-sm text-gray-600 appearance-none box-border border-none" placeholder="Enter a post code" />
        </label>
        <label class="col-span-2 box-border p-1">
            <span class="sr-only">Name of attendee</span>
            <input name="name" class="block w-full px-2 py-2 mt-1 text-sm text-gray-600 appearance-none box-border border-none" placeholder="Enter the name of the attendee" />
        </label>
        <label class="col-span-2 box-border p-1">
            <span class="sr-only">Email</span>
            <input name="email" class="block w-full px-2 py-2 mt-1 text-sm text-gray-600 appearance-none box-border border-none" placeholder="Enter the email of the attendee" />
        </label>
        <label class="box-border p-1">
            <span class="sr-only">Role</span>
            <input name="jobRole" class="block w-full px-2 py-2 mt-1 text-sm text-gray-600 appearance-none box-border border-none" placeholder="Enter the job role of the attendee" />
        </label>
        <label class="box-border p-1">
            <span class="sr-only">Days Attended</span>
            <select name="days" class="block h-8 mt-1 px-1 bg-gray-200 rounded-md border-none">
                <option disabed>Choose days</option>
                <option val="1">1</option>
                <option val="2">2</option>
                <option val="3">3</option>
                <option val="4">4</option>
                <option val="5">5</option>
                <option val="6">6</option>
                <option val="7">7</option>
                <option val="8">8</option>
                <option val="9">9</option>
                <option val="10">10</option>
            </select>
        </label>
        <label class="box-border p-1 flex items-center justify-center">
            <span class="sr-only">Newsletter</span>
            <input type="checkbox" name="newsletter" value="1" class="mt-1"/>
        </label>
        <label class="w-full text-right">
            <span class="sr-only">Submit</span>
            <span
                role="button"
                @click="submitHandler"
                class="btn submit m-2"
            >Save</span>
        </label>
    </form>
</template>

<script lang="ts">
    import {defineComponent, watchEffect, ref} from 'vue'
    import axios from 'axios'

    export default defineComponent({
        props: {
            csrf: {
                type: String,
                required: true
            },
            event: {
                type: String,
                required: true
            }
        },
        setup(){
            const form = ref(null);


            const submitHandler = () => {
                if(form.value){
                    let formValues = new FormData(form.value)

                    axios({
                        method: 'post',
                        url: '/admin/actions/craft-attendees/training/save',
                        data: formValues
                    })
                    .then(function (response) {
                        console.log("response?",response);
                    });
                }
            }

            return { form, submitHandler };

        }
    })
</script>
