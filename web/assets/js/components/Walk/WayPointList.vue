<template>
    <div>
        <b-table
            v-if="!isLoading"
            show-empty
            emptyText="Für diese Runde gibt es keine Wegpunkte."
            small
            stacked="md"
            :items="wayPoints"
            :fields="fields"
            class="mb-0"
        >
            <template #cell(locationName)="data">
                <location-link
                    :value="data.value"
                />
            </template>
            <template v-slot:cell(actions)="row">
                <router-link
                    :to="{name: 'WayPointDetail', params: { walkId: walk.id, wayPointId: row.item.id}}"
                    :data-test="`button-wegpunkt-ansehen-${ row.item.locationName }`"
                >
                    <b-button size="sm">
                        Wegpunkt ansehen

                        <font-awesome-icon
                            icon="map-signs"
                            class="bg-secondary ml-2"
                        />
                        <font-awesome-icon icon="eye" class="ml-2"/>
                    </b-button>
                </router-link>
            </template>
        </b-table>
    </div>
</template>

<script>
'use strict';

import LocationLink from '../LocationLink.vue';
import dayjs from 'dayjs';

export default {
    name: 'WayPointList',
    components: {
        LocationLink,
    },
    props: {
        walkId: {
            required: true,
        },
    },
    data: function () {
        return {
            fields: [
                { key: 'locationName', label: 'Ort', sortable: true, sortDirection: 'desc', class: 'text-center align-middle' },
                {
                    key: 'visitedAt',
                    label: 'Ankunft',
                    sortable: true,
                    sortByFormatted: true,
                    filterByFormatted: true,
                    formatter: (value) => {
                        return dayjs(value).format('DD.MM.YYYY HH:mm');
                    },
                    sortDirection: 'desc',
                    class: 'text-center align-middle',
                },
                {
                    key: 'isMeeting',
                    label: 'Meeting',
                    formatter: (value, key, item) => {
                        return value ? 'Ja' : 'Nein';
                    },
                    sortable: true,
                    sortByFormatted: true,
                    filterByFormatted: true,
                    class: 'text-center align-middle',
                },
                { key: 'malesCount', label: 'Männer', sortable: true, sortDirection: 'desc', class: 'text-center align-middle' },
                { key: 'femalesCount', label: 'Frauen', sortable: true, sortDirection: 'desc', class: 'text-center align-middle' },
                { key: 'queerCount', label: 'Andere', sortable: true, sortDirection: 'desc', class: 'text-center align-middle' },
                { key: 'actions', label: 'Aktionen', class: 'text-center align-middle' },
            ],
        };
    },
    computed: {
        isLoading() {
            return this.$store.getters['walk/isLoading'] || this.$store.getters['wayPoint/isLoading'];
        },
        walk() {
            return this.$store.getters['walk/getWalkById'](this.walkId);
        },
        hasWayPoints() {
            return this.$store.getters['wayPoint/hasWayPoints'];
        },
        wayPoints() {
            const wayPoints = [];
            if (!this.walk) {
                return wayPoints;
            }
            this.walk.wayPoints.forEach(iri => {
                const wayPoint = this.getWayPointByIri(iri);
                if (wayPoint) {
                    wayPoints.push(wayPoint);
                }
            });

            return wayPoints;
        },
    },
    watch: {
        wayPoints() {
            this.$emit('refresh-total-way-points', this.wayPoints.length);
        },
    },
    async created() {
        if (!this.walk) {
            await this.$store.dispatch('walk/findById', this.walkId);
        }
        if (!this.walk) {
            this.$router.push({ name: 'Dashboard', params: { redirect: 'Diese Runde existiert nicht. Du wurdest auf das Dashboard weitergeleitet.' } });
        }
        if (this.walk.wayPoints.length !== this.wayPoints.length) {
            await this.$store.dispatch('wayPoint/find', {
                filter: {
                    walk: this.walk['@id'],
                },
                perPage: 1000,
                currentPage: 1,
            });
        }
    },
    methods: {
        getWayPointByIri(iri) {
            return this.$store.getters['wayPoint/getWayPointByIri'](iri);
        },
    },
};
</script>

<style scoped>

</style>
