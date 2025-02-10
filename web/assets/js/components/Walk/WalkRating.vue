<script lang="ts">
import {defineComponent, computed, PropType, ref} from 'vue';
import { type Client } from '../../model';

export default defineComponent({
    name: 'WalkRating',
    components: {
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

        const ratingForChange = ref<number>(props.rating);

        const handleRatingSelected = (rating: number) => {
            console.log('handleRrating');
            console.log(ratingForChange);
            emit('select-rating', ratingForChange);
        };

        return {
            ratingForChange,
            imageSrc,
            handleRatingSelected,
        };
    },
});
</script>

<template>
    <v-rating
        v-model="ratingForChange"
        :length="5"
        hover
        :readonly="readOnly"
        :item-size="itemSize"
        :show-rating="showRating"
        :data-test="`rating${readOnly ? '-read' : ''}`"
        @rating-selected="handleRatingSelected"
    >
        <template v-slot:item="props">
            <v-avatar v-if="imageSrc">
                <v-img
                    :src="imageSrc"
                    :class="!props.isFilled ? 'opacity-20' : ''"
                ></v-img>
            </v-avatar>
            <v-icon
                v-else
                color="primary"
                size="large"
            >
                {{ props.isFilled ? 'mdi-star' : 'mdi-star-outline' }}
            </v-icon>
        </template>
    </v-rating>
</template>

<style scoped lang="scss">
</style>
