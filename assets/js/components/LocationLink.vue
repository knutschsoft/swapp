<template>
    <a
        :href="`https://nominatim.openstreetmap.org/ui/search.html?q=${value}`"
        target="_blank"
        class="text-nowrap"
    >
        {{ value }}
        <mdicon
            name="open-in-new"
            size="14"
        />
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
