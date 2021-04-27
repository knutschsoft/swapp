<template>
    <div
        class="bg-dark text-white p-2 font-weight-bold d-flex cursor-pointer no-select"
    >
        <div
            class="my-auto"
            v-html="title"
        />
        <b-icon-chevron-up
            class="my-auto ml-auto when-opened collapse-icon"
        />
        <b-icon-chevron-down
            class="my-auto ml-auto when-closed collapse-icon"
        />
    </div>
</template>

<script>
    "use strict";
    export default {
        name: "ModulHeader",
        props: {
            title: {
                required: true,
                type: String,
            },
        },
        data: function () {
            return {
            }
        },
    }
</script>

<style scoped>
</style>
