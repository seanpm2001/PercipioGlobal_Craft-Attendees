<template>
    <form
        ref="form"
        method="post"
        action=""
        accept-charset="UTF-8"
        class="mt-8"
        @keyup="keyup"
        @submit="handleSubmit"
    >
        <input type="hidden" name="action" value="actions/craft-attendees/training/save">
        <input type="hidden" name="event" :value="event">
        <input type="hidden" name="CRAFT_CSRF_TOKEN" :value="csrf">
        <input type="hidden" name="siteId" :value="site">

        <input type="text" v-model="search" style="margin-bottom:0!important" />
    </form>
</template>

<script lang="ts">
    import {defineComponent} from 'vue'

    export default defineComponent({

    })
</script>
