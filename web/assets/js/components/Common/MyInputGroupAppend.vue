<template>
    <b-input-group-append>
        <b-button
            @click="$emit('click')"
            :disabled="!isActive"
            :class="{'bg-primary': isActive}"
        >
            <mdicon
                :name="isActive ? 'FilterRemoveOutline' : 'FilterOutline'"
                size="18"
            />
        </b-button>
    </b-input-group-append>
</template>

<script>
'use strict';

export default {
    name: 'MyInputGroupAppend',
    props: {
        isActive: {
            required: true,
            type: Boolean,
        },
    },
};
</script>

<style scoped lang="scss">
</style>
