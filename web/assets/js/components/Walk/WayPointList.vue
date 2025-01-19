<template>
    <div>
        <div
            v-if="hasWayPoints"
            class="p-2"
        >
            <v-row class="mt-0 mb-0">
                <v-col
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
                </v-col>
            </v-row>
        </div>
        <v-data-table
            v-if="!isLoading && walk"
            no-results-text="Für diese Runde gibt es keine Wegpunkte."
            dense
            :items="wayPoints"
            :headers="headers"
            :expanded.sync="expanded"
            sort-by="visitedAt"
            hide-default-footer
        >
            <template #expanded-item="{headers, item}">
                <td :colspan="headers.length">
                    <WayPointDetailData
                        :walk-id="walk.walkId"
                        :way-point-id="item.wayPointId"
                        :excluded-attributes="['walk', 'locationName', 'visitedAt', 'isMeeting']"
                        :hide-empty-age-groups="true"
                    />
                </td>
            </template>
            <template #item.locationName="{item}">
                <location-link
                    :value="item.locationName"
                />
            </template>
            <template #item.isMeeting="{item}">
                {{ item.isMeeting ? 'Ja' : 'Nein' }}
            </template>
            <template #item.visitedAt="{item}">
                {{ formatDateTimeNoSeconds(item.visitedAt) }}
            </template>
            <template #item.actions="{item}">
                <router-link
                    :to="{name: 'WayPointDetail', params: { walkId: walk.walkId, wayPointId: item.wayPointId}}"
                    :data-test="`button-wegpunkt-ansehen-${ item.locationName }`"
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
        </v-data-table>
    </div>
</template>

<script>
'use strict';

import LocationLink from '../LocationLink.vue';
import { useWalkStore, useWayPointStore } from '../../stores/way-point';
import WayPointDetailData from "../WayPoint/WayPointDetailData.vue";
import {formatDateTimeNoSeconds} from "@/js/utils";

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
        headers () {
            const headers = [
                { value: 'locationName', text: 'Ort', sortDirection: 'desc' },
                {
                    value: 'visitedAt',
                    text: 'Ankunft',
                    sortDirection: 'desc',
                },
                {
                    value: 'isMeeting',
                    text: 'Meeting',
                    sortable: true,
                },
            ];
            if (this.walk.isWithAgeRanges) {
                headers.push(...[
                    { value: 'malesCount', text: 'Männer', sortDirection: 'desc' },
                    { value: 'femalesCount', text: 'Frauen', sortDirection: 'desc' },
                    { value: 'queerCount', text: 'Andere', sortDirection: 'desc' },
                ]);
            }
            if (!(this.walk.isWithAgeRanges || !this.walk.isWithPeopleCount)) {
                headers.push({ value: 'peopleCount', text: 'Anzahl Personen', sortDirection: 'desc'})
            }
            headers.push({ value: 'actions', text: 'Aktionen' })

            return headers;
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
        expanded() {
            if (this.isDetailsShowing) {
                return this.wayPoints;
            }

            return [];
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
        formatDateTimeNoSeconds,
        getWayPointByIri(iri) {
            return this.wayPointStore.getWayPointByIri(iri);
        },
    },
};
</script>

<style scoped>

</style>
