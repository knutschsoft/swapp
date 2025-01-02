<template>
    <v-chip
        class="font-weight-bold d-flex align-items-center"
        :style="`background-color: ${ color }`"
        pill
    >
        <span
            :style="`background-color: ${ color }`"
        >
            <span
                style="height:inherit; background: inherit; -webkit-background-clip: text; background-clip: text; color: transparent; text-align: center; filter: invert(1) grayscale(1) contrast(999);"
            >
                {{ color }}
            </span>
        </span>
    </v-chip>
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
