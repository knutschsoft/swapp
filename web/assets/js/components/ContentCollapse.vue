<template>
    <v-expansion-panels
        v-model="expansionPanelsModel"
        flat
        class="mt-1 mt-sm-2 mt-lg-3"
    >
        <v-expansion-panel>
            <v-expansion-panel-header
                color="secondary"
                class="force-white"
                :data-test="getCollapseId"
            >
                <v-skeleton-loader
                    v-if="isLoading"
                    v-bind="attrs"
                    type="text"
                    color="secondary"
                    :width="titleWidth"
                    :max-width="titleWidth"
                ></v-skeleton-loader>
                <div
                    v-else
                    v-html="title"
                />
                <template v-slot:actions>
                    <v-icon color="white">
                        $expand
                    </v-icon>
                </template>
            </v-expansion-panel-header>
            <v-expansion-panel-content
                class="border border-secondary"
            >
                <slot />
            </v-expansion-panel-content>
        </v-expansion-panel>
    </v-expansion-panels>
</template>

<script lang="ts">
import {computed, onMounted, ref, watch} from 'vue';
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
        const titleLengthState = ref<string | boolean>(false);
        const visibleState = ref<boolean>(false);
        const { title } = props;

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
            isLoading: props.isLoading,
        };
    },
};
</script>
