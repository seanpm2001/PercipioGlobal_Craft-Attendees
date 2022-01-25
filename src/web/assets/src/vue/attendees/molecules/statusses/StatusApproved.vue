<template>
    <div class="absolute right-0 top-8 text-xs text-gray-600">
        <p v-if="status === 0" class="flex items-center">
            <span class="inline-block w-3 h-3 bg-gray-400 rounded-full"></span>
            <span>Use the fields below to add attendee data. All data must be verified to count towards reporting metrics.</span>
        </p>
        <p v-if="status === 1" class="flex items-center">
            <span class="inline-block w-3 h-3 bg-emerald-400 rounded-full"></span>
            <span>School verified and linked to <a :href="`https://get-information-schools.service.gov.uk/Establishments/Establishment/Details/${urnVal}`" class="underline text-gray-600" target="_blank">government establishment data</a></span>
        </p>
        <p v-if="status === 2" class="flex items-center">
            <span class="inline-block w-3 h-3 bg-orange-400 rounded-full"></span>
            <span>School pending verification. Linked to <a :href="`https://get-information-schools.service.gov.uk/Establishments/Establishment/Details/${urnVal}`" class="underline text-gray-600" target="_blank">government establishment data</a></span>
        </p>
        <p v-if="status === 3" class="flex items-center">
            <span class="inline-block w-3 h-3 border-2 border-solid border-emerald-400 bg-emerald-100 rounded-full"></span>
            <span>Organisation verified. Not linked to government establishment data</span>
        </p>
        <p v-if="status === 4" class="flex items-center">
            <span class="inline-block w-3 h-3 border-2 border-solid border-orange-400 bg-orange-100 rounded-full"></span>
            <span>Organisation pending verification. Not linked to government establishment data.</span>
        </p>
    </div>
</template>

<script lang="ts">
    import {defineComponent, ref, watch, watchEffect} from "vue";

    export default defineComponent({
       props:{
            approved: {
                type: Number,
            },
           urn: {
                type: Number,
                default: ''
           },
           disabled: {
               type: Boolean,
               default: false
           }
       },
       setup(props) {
           const status = ref(0)
           const urnVal = ref(props.urn)

           watchEffect(() => {

               urnVal.value = props.urn
               if(!props.urn){
                   urnVal.value = ''
               }

               if(!props.disabled){
                   if(props.approved == 1 && urnVal.value.length > 0) {
                       status.value = 1
                   }
                   else if (props.approved == 0 && urnVal.value.length > 0) {
                       status.value = 2
                   }
                   else if(props.approved == 1 && urnVal.value.length == 0) {
                       status.value = 3
                   }
                   else if(props.approved == 0 && urnVal.value.length == 0) {
                       status.value = 4
                   }
                   else {
                       status.value = 0
                   }
               }else{
                   status.value = 0
               }

           })

           return { status, urnVal }
       }
   })
</script>
