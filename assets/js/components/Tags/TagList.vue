<template>
    <div>
        <content-loading-spinner
            :is-loading="isLoading"
        />
        <b-table
            v-show="!isLoading && tags.length"
            :items="tags"
            :fields="fields"
            class="mb-0"
            stacked="md"
        >
            <template v-slot:cell(wayPoints)="data">
                <b-badge
                    class="font-weight-bold"
                    variant="info"
                    pill
                    font-scale="2"
                >
                    {{ data.item.wayPoints.length }}
                </b-badge>
            </template>
            <template v-slot:cell(walks)="data">
                <b-badge
                    class="font-weight-bold"
                    variant="info"
                    pill
                    font-scale="2"
                >
                    {{ data.item.walks.length }}
                </b-badge>
            </template>
            <template v-slot:cell(color)="data">
                <color-badge
                    :color="data.item.color"
                />
            </template>

            <template v-slot:cell(actions)="row">
                <b-button
                    size="sm"
                    @click="toggleEnabled(row.item.tagId, row.item.isEnabled)"
                >
                    {{ row.item }}
                    Tag ändern
                </b-button>
            </template>
        </b-table>

        <b-modal
            :id="editModalTag.id"
            :title="editModalTag.title"
            size="lg"
            @hide="resetEditModalTag"
        >
            currently unused
        </b-modal>
    </div>
</template>

<script>
'use strict';
import ColorBadge from './ColorBadge.vue';
import ContentLoadingSpinner from '../ContentLoadingSpinner.vue';

export default {
    name: 'TagList',
    components: { ContentLoadingSpinner, ColorBadge },
    data: function () {
        return {
            fields: [
                {
                    key: 'id',
                    label: 'ID',
                },
                {
                    key: 'name',
                    sortable: true,
                },
                {
                    key: 'color',
                    label: 'Farbe',
                    sortable: true,
                },
                {
                    key: 'walks',
                    sortable: false,
                },
                {
                    key: 'wayPoints',
                    sortable: false,
                },
                // { key: 'actions', label: 'Aktionen' },
            ],
            editModalTag: {
                id: 'edit-modal-tag',
                title: '',
                selectedTag: null,
            },
        };
    },
    computed: {
        tags() {
            return this.$store.getters['tag/tags'];
        },
        isLoading() {
            return this.$store.getters['tag/isLoading'];
        },
        error() {
            return this.$store.getters['tag/error'];
        },
    },
    async created() {
        await Promise.all([
            this.$store.dispatch('tag/findAll'),
            // this.$store.dispatch('wayPoint/findAll'),
        ]);
    },
    methods: {
        editTag(tag) {
            this.$root.$emit('bv::show::modal', this.editModalTag.id);
            this.editModalTag.selectedTag = tag;
        },
        resetEditModalTag() {
            this.editModalTag.title = '';
            this.editModalTag.tag = null;
        },
        getFormattedWayPoint: function (id) {
            let wayPoint = this.$store.getters['wayPoint/getWayPointById'](id);

            return `${wayPoint.name} - ${wayPoint.id}`;
        },
        getFormattedWalk: function (id) {
            let walk = this.$store.getters['walk/getWalkById'](id);

            return `${walk.name} - ${walk.id}`;
        },
        toggleEnabled: function (tagId, isEnabled) {
            if (isEnabled) {
                this.$store.dispatch('tag/disable', tagId);
            } else {
                this.$store.dispatch('tag/enable', tagId);
            }
        },
    },
};
</script>

<style scoped lang="scss">
</style>
