<template>
    <a
        :href="`https://nominatim.openstreetmap.org/ui/search.html?q=${value}`"
        target="_blank"
        class="text-nowrap"
    >
        {{ value }}
        <v-icon
            size="14"
            color="primary"
        >
            mdi-open-in-new
        </v-icon>
    </a>
</template>

<script>
"use strict";

export default {
    name: 'LocationLink',
    props: {
        value: {
            type: String,
            required: true,
        },
    },
};
</script>
