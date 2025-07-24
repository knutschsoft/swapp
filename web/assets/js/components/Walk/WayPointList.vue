<template>
    <div class="pa-1 pa-sm-2 pa-md-4 pa-lg-5 pa-xl-6 pa-xxl-7">
        <v-btn
            v-if="wayPoints.length > 0"
            color="secondary"
            density="comfortable"
            block
            @click="isDetailsShowing = !isDetailsShowing"
            data-test="toggle-waypoint-details"
            class="text-transform-none"
            :append-icon="isDetailsShowing ? 'mdi-eye-off-outline' : 'mdi-eye-outline'"
        >
            Alle Details {{ isDetailsShowing ? 'verbergen' : 'anzeigen' }}
        </v-btn>
        <v-data-table-server
            v-if="walk"
            no-results-text="Für diese Runde gibt es keine Wegpunkte."
            density="compact"
            itemsLength=""
            :items="wayPoints"
            :headers="headers"
            v-model:expanded="expanded"
            hide-default-footer
            :loading="isLoading"
            item-value="@id"
        >
            <template #expanded-row="{item}">
                <td :colspan="headers.length" class="bg-grey-lighten-5 border-b-">
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
                <v-btn
                    :to="{name: 'WayPointDetail', params: { walkId: walk.walkId, wayPointId: item.wayPointId}}"
                    :data-test="`button-wegpunkt-ansehen-${ item.locationName }`"
                    density="compact"
                    class="text-transform-none"
                    color="secondary"
                >
                    Wegpunkt ansehen
                    <v-icon icon="mdi-routes" class="ml-1"></v-icon>
                    <v-icon icon="mdi-eye" class="ml-1"></v-icon>
                </v-btn>
            </template>
        </v-data-table-server>
    </div>
</template>

<script>
'use strict';

import LocationLink from '../LocationLink.vue';
import { useWalkStore, useWayPointStore } from '@/js/stores';
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
            expandeds: [],
            isDetailsShowing: false,
            walkStore: useWalkStore(),
            wayPointStore: useWayPointStore(),
        };
    },
    computed: {
        headers () {
            const headers = [
                { key: 'locationName', title: 'Ort' },
                {
                    key: 'visitedAt',
                    title: 'Ankunft',
                    sortDirection: 'desc',
                },
                {
                    key: 'isMeeting',
                    title: 'Meeting',
                    sortable: true,
                },
            ];
            if (this.walk.isWithAgeRanges) {
                headers.push(...[
                    { key: 'malesCount', title: 'Männer' },
                    { key: 'femalesCount', title: 'Frauen' },
                    { key: 'queerCount', title: 'Andere' },
                ]);
            }
            if (!(this.walk.isWithAgeRanges || !this.walk.isWithPeopleCount)) {
                headers.push({ key: 'peopleCount', title: 'Anzahl Personen'})
            }
            headers.push({ key: 'actions', title: 'Aktionen', sortable: false })

            return headers;
        },
        isLoading () {
            return this.walkStore.isLoading || this.wayPointStore.isLoading;
        },
        walk() {
            return this.walkStore.getWalkById(this.walkId);
        },
        expanded() {
            if (this.isDetailsShowing) {
                return this.wayPoints.map(wayPoint => wayPoint['@id']);
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
