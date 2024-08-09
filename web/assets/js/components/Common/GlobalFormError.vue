<template>
    <v-alert
        v-if="hasError"
        type="danger"
        class="mt-2 mb-0"
    >
        <ul
            class="mb-0"
        >
            <li
                v-for="(validationError, index) in validationErrors"
                :key="index"
            >
                {{ validationError }}<span v-if="index + 1 !== validationErrors.length">.</span>
            </li>
        </ul>
    </v-alert>
</template>

<script>
'use strict';

export default {
    name: 'GlobalFormError',
    props: {
        error: {
            required: true,
        },
    },
    components: {
    },
    data: function () {
        return {
        };
    },
    computed: {
        hasError() {
            return !!this.error;
        },
        validationErrors() {
            return this.error.split('. ');
        },
    },
    async created() {
    },
    methods: {
    },
};
</script>

<style scoped lang="scss">
</style>
