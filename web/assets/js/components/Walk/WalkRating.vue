<script lang="ts">
import { defineComponent, computed, PropType } from 'vue';
import { ImageRating, StarRating } from 'vue-rate-it';
import { type Client } from '../../model';

export default defineComponent({
    name: 'WalkRating',
    components: {
        ImageRating,
        StarRating,
    },
    props: {
        rating: {
            type: Number,
            required: true,
        },
        client: {
            type: Object as PropType<Client>,
            required: false,
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
    },
    emits: ['select-rating'],
    setup(props, { emit }) {
        const imageSrc = computed<string>(() => {
            if (props.client?.ratingImageFileData) {
                return props.client.ratingImageFileData;
            }
            if (props.client?.ratingImageSrc) {
                return props.client.ratingImageSrc;
            }
            return '';
        });

        const handleRatingSelected = (rating: number) => {
            emit('select-rating', rating);
        };

        return {
            imageSrc,
            handleRatingSelected,
        };
    },
});
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
            @rating-selected="handleRatingSelected"
        />
        <star-rating
            v-else
            :rating="rating"
            :max-rating="5"
            :read-only="readOnly"
            :item-size="itemSize"
            :show-rating="showRating"
            :data-test="`rating${readOnly ? '-read' : ''}`"
            @rating-selected="handleRatingSelected"
        />
    </div>
</template>

<style scoped lang="scss">
</style>
