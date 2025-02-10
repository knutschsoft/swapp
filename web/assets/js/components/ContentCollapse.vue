<template>
    <v-expansion-panels
        v-model="expansionPanelsModel"
        elevation="1"
        class="mb-1 mb-sm-2 mb-md-4 mb-lg-5 mb-xl-6 mb-xxl-7"
    >
        <v-expansion-panel>
            <v-expansion-panel-title
                color="secondary"
                class="elevation-1"
                :data-test="getCollapseId"
            >
                <v-skeleton-loader
                    v-if="isLoading"
                    type="text"
                    :width="titleWidth"
                    :max-width="titleWidth"
                ></v-skeleton-loader>
                <div
                    v-else
                    v-html="title"
                />
                <template v-slot:actions>
                    <v-icon>
                        $expand
                    </v-icon>
                </template>
            </v-expansion-panel-title>
            <v-expansion-panel-text
                class="p-0"
            >
                <slot />
            </v-expansion-panel-text>
        </v-expansion-panel>
    </v-expansion-panels>
</template>

<script lang="ts">
import {computed, nextTick, onMounted, ref, watch} from 'vue';
import {useStorage} from '@vueuse/core';

export default {
    name: 'ContentCollapse',
    props: {
        title: {
            type: String,
            required: true,
        },
        collapseKey: {
            type: String,
            required: true,
        },
        isVisibleByDefault: {
            type: Boolean,
            required: false,
            default: false,
        },
        isLoading: {
            type: Boolean,
            required: false,
            default: false,
        },
    },
    setup(props) {
        const expansionPanelsModel = ref<number | null>(null);
        const titleLengthState = ref<string>('');
        const visibleState = ref<boolean>(false);

        const title = computed(() => props.title);
        const isLoading = computed(() => props.isLoading);
        const getCollapseId = computed(() => `collapse-${props.collapseKey}`);
        const getTitleLengthId = computed(() => `${getCollapseId.value}-title-width-in-px`);
        const titleWidth = computed(() => (titleLengthState.value ? titleLengthState.value : '100'));

        const storedTitleLength = useStorage(`swapp-store-${getTitleLengthId.value}`, '100');
        const storedVisibleState = useStorage(`swapp-store-${getCollapseId.value}`, props.isVisibleByDefault);

        onMounted(() => {
            titleLengthState.value = storedTitleLength.value;
            visibleState.value = storedVisibleState.value;

            if (visibleState.value) {
                expansionPanelsModel.value = 0;
            }
        });

        watch(
            () => props.isLoading,
            (newValue) => {
                if (!newValue) {
                    // Update der Titelbreite nach der nächsten DOM-Aktualisierung
                    nextTick(() => {
                        const titleWidth = document.querySelector('[data-test="collapse-${props.collapseKey}"]');
                        titleLengthState.value = titleWidth ? `${titleWidth.getBoundingClientRect().width}` : '100';
                    });
                }
            }
        );

        watch(
            () => expansionPanelsModel.value,
            (newValue) => {
                storedVisibleState.value = newValue === 0;
            }
        );

        return {
            expansionPanelsModel,
            getCollapseId,
            titleWidth,
            title,
            isLoading,
        };
    },
};
</script>
