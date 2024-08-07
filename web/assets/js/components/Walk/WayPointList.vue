<template>
    <div>
        <div
            v-if="hasWayPoints"
            class="p-2"
        >
            <b-row class="mt-0 mb-0">
                <b-col
                    class="my-1"
                    xs="12"
                    sm="12"
                    md="12"
                    xl="12"
                >
                    <v-btn
                        color="secondary"
                        block
                        @click="isDetailsShowing = !isDetailsShowing"
                        data-test="toggle-waypoint-details"
                    >
                        Alle Details {{ isDetailsShowing ? 'verbergen' : 'anzeigen' }}
                        <mdicon
                            :name="isDetailsShowing ? 'EyeOffOutline' : 'EyeOutline'"
                        />
                    </v-btn>
                </b-col>
            </b-row>
        </div>
        <b-table
            v-if="!isLoading && walk"
            ref="waypointsTable"
            show-empty
            emptyText="Für diese Runde gibt es keine Wegpunkte."
            small
            stacked="md"
            :items="wayPoints"
            :fields="fields"
            sort-by="visitedAt"
            class="mb-0"
        >
            <template #cell(locationName)="data">
                <location-link
                    :value="data.value"
                />
            </template>
            <template #row-details="row">
                <WayPointDetailData
                    :walk-id="walk.walkId"
                    :way-point-id="row.item.wayPointId"
                    :excluded-attributes="['walk', 'locationName', 'visitedAt', 'isMeeting']"
                    :hide-empty-age-groups="true"
                />
            </template>

            <template v-slot:cell(actions)="row">
                <router-link
                    :to="{name: 'WayPointDetail', params: { walkId: walk.walkId, wayPointId: row.item.wayPointId}}"
                    :data-test="`button-wegpunkt-ansehen-${ row.item.locationName }`"
                >
                    <v-btn
                        small
                        color="secondary"
                    >
                        Wegpunkt ansehen
                        <font-awesome-icon icon="map-signs" class="ml-2" />
                        <font-awesome-icon icon="eye" class="ml-2" />
                    </v-btn>
                </router-link>
            </template>
        </b-table>
    </div>
</template>

<script>
'use strict';

import LocationLink from '../LocationLink.vue';
import dayjs from 'dayjs';
import { useWayPointStore } from '../../stores/way-point';
import { useWalkStore } from '../../stores/walk';
import WayPointDetailData from "../WayPoint/WayPointDetailData.vue";

export default {
    name: 'WayPointList',
    components: {
        WayPointDetailData,
        LocationLink,
    },
    props: {
        walkId: {
            required: true,
        },
    },
    data: function () {
        return {
            isDetailsShowing: false,
            walkStore: useWalkStore(),
            wayPointStore: useWayPointStore(),
        };
    },
    computed: {
        fields () {
            return [
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
                { key: 'malesCount', label: 'Männer', sortable: true, sortDirection: 'desc', class: !this.walk.isWithAgeRanges ? 'd-none' : 'text-center align-middle' },
                { key: 'femalesCount', label: 'Frauen', sortable: true, sortDirection: 'desc', class: !this.walk.isWithAgeRanges ? 'd-none' : 'text-center align-middle' },
                { key: 'queerCount', label: 'Andere', sortable: true, sortDirection: 'desc', class: !this.walk.isWithAgeRanges ? 'd-none' : 'text-center align-middle' },
                { key: 'peopleCount', label: 'Anzahl Personen', sortable: true, sortDirection: 'desc', class: (this.walk.isWithAgeRanges || !this.walk.isWithPeopleCount) ? 'd-none' : 'text-center align-middle' },
                { key: 'actions', label: 'Aktionen', class: 'text-center align-middle' },
            ];
        },
        isLoading () {
            return this.walkStore.isLoading || this.wayPointStore.isLoading;
        },
        walk() {
            return this.walkStore.getWalkById(this.walkId);
        },
        hasWayPoints() {
            return this.wayPointStore.hasWayPoints;
        },
        wayPoints() {
            const wayPoints = [];
            if (!this.walk) {
                return wayPoints;
            }
            this.walk.wayPoints.forEach(iri => {
                let wayPoint = this.getWayPointByIri(iri);
                if (wayPoint) {
                    this.$set(wayPoint, '_showDetails', this.isDetailsShowing);
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
    async mounted() {
        if (!this.walk) {
            await this.walkStore.fetchById(this.walkId);
        }
        if (this.walk && this.walk.wayPoints.length !== this.wayPoints.length) {
            await this.wayPointStore.fetchWayPoints({
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
            return this.wayPointStore.getWayPointByIri(iri);
        },
    },
};
</script>

<style scoped>

</style>
