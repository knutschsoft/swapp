<template>
    <b-alert
        :show="hasError"
        variant="danger"
        class="mt-3 mb-0"
    >
        <ul class="mb-0">
            <li
                v-for="(validationError, name) in validationErrors"
                :key="name"
            >
                <b
                    v-if="name !== 'global'"
                    v-text="`${name}:`"
                    class="text-capitalize"
                />
                {{ validationError }}
            </li>
        </ul>
    </b-alert>
</template>

<script>
'use strict';

export default {
    name: 'FormError',
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
            const errors = {};
            if (!this.hasError) {
                return errors;
            }
            const error = this.error;
            if (error && error.data.violations) {
                error.data.violations.forEach((violation) => {
                    const key = violation.propertyPath ? violation.propertyPath : 'global';
                    errors[key] = violation.message;
                });
                return errors;
            }
            if (error.data && error.data["hydra:description"]) {
                errors.global = error.data["hydra:description"];
            }

            return errors;
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
