<template>
    <b-form-group
        content-cols="12"
        label-cols="12"
        content-cols-lg="10"
        label-cols-lg="2"
        :label="label"
        :description="description"
        :state="state"
        :invalid-feedback="invalidFeedback"
    >
        <slot name="label" />
        <slot />
        <slot name="valid-feedback" />
    </b-form-group>
</template>

<script>
'use strict';

export default {
    name: 'FormGroup',
    props: {
        label: {
            required: false,
            type: String,
            default: '',
        },
        description: {
            required: false,
            type: String,
            default: '',
        },
        state: {
            required: false,
            default: null,
        },
        invalidFeedback: {
            required: false,
            default: '',
        },
    },
    components: {
    },
    data: function () {
        return {
        };
    },
    computed: {
    },
    async created() {
    },
    methods: {
    },
};
</script>

<style scoped lang="scss">
</style>
