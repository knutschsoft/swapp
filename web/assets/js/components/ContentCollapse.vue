<template>
    <div class="w-100 border border-dark p-0 mt-1 mt-sm-2 mt-lg-3 flex-shrink-1">
        <div
            v-b-toggle="getCollapseId"
            class="bg-dark text-white p-2 font-weight-bold d-flex cursor-pointer no-select"
            :data-test="getCollapseId"
        >
            <b-skeleton
                v-if="isLoading"
                :width="titleWidth"
            />
            <div
                v-else
                ref="title"
                class="my-auto"
                v-html="title"
            />
            <b-icon-chevron-up
                class="my-auto ml-auto when-opened collapse-icon"
            />
            <b-icon-chevron-down
                class="my-auto ml-auto when-closed collapse-icon"
            />
        </div>
        <b-collapse
            :id="getCollapseId"
            :visible="isVisible"
        >
            <slot />
        </b-collapse>
    </div>
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
            return this.titleLengthState ? this.titleLengthState : '100px';
        },
    },
    mounted() {
        this.titleLengthState = useStorage(`swapp-store-${this.getTitleLengthId}`, '100px');
        this.visibleState = useStorage(`swapp-store-${this.getCollapseId}`, this.isVisibleByDefault);
    },
    watch: {
        isLoading() {
            if (!this.isLoading) {
                this.$nextTick(() => {
                    this.titleLengthState = `${this.$refs.title.getBoundingClientRect().width}px`;
                });
            }
        },
    }
};
</script>
