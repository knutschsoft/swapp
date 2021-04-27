<template>
    <b-badge
        class="font-weight-bold"
        :style="`background-color: ${ color }`"
        pill
        font-scale="2"
    >
        <div
            style="height:inherit; background: inherit; -webkit-background-clip: text; background-clip: text; color: transparent; text-align: center; filter: invert(1) grayscale(1) contrast(999);"
        >
            {{ color }}
        </div>
    </b-badge>
</template>

<script>
'use strict';

export default {
    name: 'ColorBadge',
    props: {
        color: {
            type: String,
            required: true,
        },
    },
};
</script>
