<template>
    <form ref="form" method="post" action="" accept-charset="UTF-8" class="grid grid-cols-9 border-b border-gray-300 border-solid">
        <input type="hidden" name="action" value="actions/craft-attendees/training/save ">
        <input type="hidden" name="event" :value="event">
        <input type="hidden" name="CRAFT_CSRF_TOKEN" :value="csrf">

        <label class="col-span-2 text-sm box-border p-1">
            <span class="sr-only">School / Organisation</span>
            <input name="orgName" class="block w-full py-2 text-sm text-gray-600 appearance-none box-border border-none" placeholder="Search fro a school or organisation" />
        </label>
        <label class="box-border p-1">
            <span class="sr-only">Post code</span>
            <input name="postCode" class="block w-full py-2 text-sm text-gray-600 appearance-none box-border border-none" placeholder="Enter a post code" />
        </label>
        <label class="box-border p-1">
            <span class="sr-only">Name of attendee</span>
            <input name="name" class="block w-full py-2 text-sm text-gray-600 appearance-none box-border border-none" placeholder="Enter the name of the attendee" />
        </label>
        <label class="box-border p-1">
            <span class="sr-only">Email</span>
            <input name="email" class="block w-full py-2 text-sm text-gray-600 appearance-none box-border border-none" placeholder="Enter the email of the attendee" />
        </label>
        <label class="box-border p-1">
            <span class="sr-only">Role</span>
            <input name="jobRole" class="block w-full py-2 text-sm text-gray-600 appearance-none box-border border-none" placeholder="Enter the job role of the attendee" />
        </label>
        <label class="box-border p-1">
            <span class="sr-only">Days Attended</span>
            <input name="days" class="block w-full py-2 text-sm text-gray-600 appearance-none box-border border-none" placeholder="Enter the days attended" />
        </label>
        <label class="box-border p-1">
            <span class="sr-only">Newsletter</span>
            <input name="newsletter" class="block w-full py-2 text-sm text-gray-600 appearance-none box-border border-none" />
        </label>
        <label class="w-full text-right">
            <span class="sr-only">Submit</span>
            <button type="submit" class="btn submit m-2">Save</button>
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

            watchEffect(() => {
                if(form.value){
                    let formValues = new FormData(form.value)

                    let obj = {};
                    for (var key of formValues.keys()) {
                        obj[key] = formValues.get(key);
                    }

                    console.log(obj)

                    axios({
                        method: 'post',
                        url: '/admin/actions/craft-attendees/training/save',
                        data: formValues
                    })
                }
            })

            return { form };

        }
    })
</script>
