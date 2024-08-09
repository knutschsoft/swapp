<template>
    <v-expansion-panels
        v-model="expansionPanelsModel"
        flat
        class="mt-0 mt-sm-2 mt-lg-3"
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

<script>
import { useStorage } from '@vueuse/core';
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
    data() {
        return {
            titleLengthState: false,
            visibleState: false,
            expansionPanelsModel: null,
        };
    },
    computed: {
        getCollapseId() {
            return `collapse-${this.collapseKey}`;
        },
        getTitleLengthId() {
            return `${this.getCollapseId}-title-width-in-px`;
        },
        isVisible() {
            return this.visibleState;
        },
        titleWidth() {
            return this.titleLengthState ? this.titleLengthState : '100';
        },
    },
    mounted() {
        this.titleLengthState = useStorage(`swapp-store-${this.getTitleLengthId}`, '100');
        this.visibleState = useStorage(`swapp-store-${this.getCollapseId}`, this.isVisibleByDefault);

        if (this.visibleState) {
            this.expansionPanelsModel = 0;
        }
    },
    watch: {
        isLoading() {
            if (!this.isLoading) {
                this.$nextTick(() => {
                    this.titleLengthState = `${this.$refs?.title?.getBoundingClientRect()?.width}`;
                });
            }
        },
        expansionPanelsModel() {
            const isJustShown = 0 === this.expansionPanelsModel;
            const state = useStorage(`swapp-store-${this.getCollapseId}`, isJustShown);
            state.value = isJustShown;
        }
    }
};
</script>
