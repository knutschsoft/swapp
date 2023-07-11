<script setup>
'use strict';
import { ImageRating, StarRating } from 'vue-rate-it';
import { computed } from 'vue';

const props = defineProps({
    rating: {
        type: Number,
        required: true,
    },
    client: {
        type: Object,
        required: false,
        default: () => { return {ratingImageSrc: false, ratingImageFileData: false} },
    },
    readOnly: {
        type: Boolean,
        required: false,
        default: true,
    },
    showRating: {
        type: Boolean,
        required: false,
        default: true,
    },
    itemSize: {
        type: Number,
        required: false,
        default: 50,
    },
});

const imageSrc = computed(() => {
    if (props.client.ratingImageFileData) {
        return props.client.ratingImageFileData;
    }

    if (props.client.ratingImageSrc) {
        return props.client.ratingImageSrc;
    }

    return false;
})

const emit = defineEmits(['select-rating']);
</script>

<template>
    <div>
        <image-rating
            v-if="imageSrc"
            :rating="rating"
            :src="imageSrc"
            :max-rating="5"
            :read-only="readOnly"
            :item-size="itemSize"
            :show-rating="showRating"
            :data-test="`rating${readOnly ? '-read' : ''}`"
            @rating-selected="emit('select-rating', $event)"
        />
        <star-rating
            v-else
            :rating="rating"
            :max-rating="5"
            :read-only="readOnly"
            :item-size="itemSize"
            :show-rating="showRating"
            :data-test="`rating${readOnly ? '-read' : ''}`"
            @rating-selected="emit('select-rating', $event)"
        />
    </div>
</template>

<style scoped lang="scss">
</style>
