<template>
    <b-form-group
        content-cols="12"
        label-cols="12"
        content-cols-lg="10"
        label-cols-lg="2"
        :label="label"
    >
        <slot />
    </b-form-group>
</template>

<script>
'use strict';

export default {
    name: 'FormGroup',
    props: {
        label: {
            required: true,
            type: String,
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
