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
    computed: {
        getCollapseId() {
            return `collapse-${this.collapseKey}`;
        },
        getTitleLengthId() {
            return `${this.getCollapseId}-title-width-in-px`;
        },
        isVisible() {
            return this.$localStorage.get(
                this.getCollapseId,
                this.isVisibleByDefault
            );
        },
        titleWidth() {
            return this.$localStorage.get(
                this.getTitleLengthId,
                '100px'
            );
        },
    },
    watch: {
        isLoading() {
            if (!this.isLoading) {
                this.$nextTick(() => {
                    this.$localStorage.set(
                        this.getTitleLengthId,
                        `${this.$refs.title.getBoundingClientRect().width}px`
                    );
                });
            }
        },
    }
};
</script>
