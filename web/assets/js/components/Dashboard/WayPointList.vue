<template>
    <div class="p-2">
        <v-row class="mt-0 mb-0">
            <v-col
                sm="12"
            >
                <div class="d-flex flex-row align-items-center">
                    <div>Tags</div>
                    <v-tooltip
                        bottom
                        color="info"
                    >
                        <template v-slot:activator="{ on, attrs }">
                            <v-icon
                                class="text-muted ml-2"
                                v-bind="attrs"
                                v-on="on"
                            >
                                mdi-help-circle-outline
                            </v-icon>
                        </template>
                        <v-alert
                            prominent
                            type="info"
                            dense
                            class="mb-0 m-0"
                        >
                            <span>Welche Tags werden angezeigt?</span>
                            <ul class="mb-0">
                                <li>Alle aktivierten Tags, die mindestens einer Runde zugeordnet sind, werden angezeigt.</li>
                                <li>Alle deaktivierten Tags, die mindestens einer Runde zugeordnet sind, werden angezeigt.</li>
                            </ul>
                        </v-alert>
                    </v-tooltip>
                    <v-btn
                        title="Filterung nach Tags entfernen"
                        class="ml-auto"
                        :color="filter.wayPointTags.length ? 'blue darken-2' : 'secondary lighten-4'"
                        x-small
                        @click="unsetFilterWayPointTags"
                        fab
                    >
                        <v-icon
                            color="white"
                        >
                            mdi-filter-remove-outline
                        </v-icon>
                    </v-btn>
                </div>
                <v-chip-group
                    v-model="filter.wayPointTags"
                    multiple
                    column
                >
                    <v-chip
                        v-for="tag in tags"
                        :key="tag['id']"
                        v-if="tag.isEnabled"
                        active-class="primary--text"
                        class="mr-1 mb-1"
                        small
                        filter
                        outlined
                    >
                        {{ tag.name }}
                    </v-chip>
                    <hr
                        v-if="hasDisabledTag"
                        class="d-block w-100 my-1 mr-2"
                    >
                    <v-chip
                        v-for="tag in tags"
                        :key="tag['id']"
                        v-if="!tag.isEnabled"
                        active-class="primary--text"
                        class="mr-1 mb-1"
                        small
                        filter
                        outlined
                    >
                        {{ tag.name }}
                        <mdicon
                            name="TagOff"
                            class="text-muted ml-1"
                            title="deaktivierter Tag"
                            size="16"
                        />
                    </v-chip>
                </v-chip-group>
            </v-col>
            <v-col
                sm="6"
                md="6"
                xl="4"
            >
                <filter-text-field
                    v-model="filter.note"
                    label="Beobachtung"
                    data-test="filter-note-way-point"
                    :isLoading="isLoading"
                />
            </v-col>
            <v-col
                sm="6"
                md="6"
                xl="4"
            >
                <filter-text-field
                    v-model="filter.oneOnOneInterview"
                    label="Einzelgespräch"
                    data-test="filter-oneOnOneInterview-way-point"
                    :isLoading="isLoading"
                />
            </v-col>
            <v-col
                sm="6"
                md="6"
                xl="2"
            >
                <filter-text-field
                    v-model="filter.locationName"
                    label="Ort"
                    data-test="filter-locationName-way-point"
                    :isLoading="isLoading"
                />
            </v-col>
            <v-col
                sm="6"
                md="6"
                xl="2"
            >
                <filter-combobox-field
                    v-model="filter.teamName"
                    label="Teamname"
                    data-test="filter-team-walk"
                    :is-loading="isLoading"
                    :suggestions="teamNames"
                />
            </v-col>
            <v-col
                xs="12"
                sm="12"
                md="12"
                xl="12"
            >

                <v-input
                    @click:append="unsetFilterVisitedAt"
                    @click:prepend="togglePicker"
                    hide-details
                >
                    <template v-slot:prepend>
                        <div
                            :class="(filter?.visitedAt?.startDate !== defaultDateRange.startDate || filter?.visitedAt?.endDate !== defaultDateRange.endDate) ? 'font-weight-bold' : ''"
                            class="mt-2"
                        >
                            Ankunft
                        </div>
                    </template>
                    <date-range-picker
                        ref="picker"
                        class="form-control"
                        v-model="filter.visitedAt"
                        :ranges="ranges"
                        :locale-data="locale"
                        showWeekNumbers
                        auto-apply
                        show-dropdowns
                        opens="right"
                        :readonly="isLoading"
                        :disabled="isLoading"
                    >
                    </date-range-picker>

                    <template v-slot:append>
                        <v-progress-circular
                            v-if="isLoading"
                            size="18"
                            indeterminate
                            color="secondary"
                        />
                        <v-icon
                            v-else
                        >
                            mdi-calendar
                        </v-icon>

                        <v-btn
                            :color="(filter?.visitedAt?.startDate !== defaultDateRange.startDate || filter?.visitedAt?.endDate !== defaultDateRange.endDate) ? 'blue darken-2' : 'secondary lighten-4'"
                            x-small
                            fab
                        >
                            <v-icon
                                color="white"
                                @click="unsetFilterVisitedAt"
                            >
                                mdi-filter-remove-outline
                            </v-icon>
                        </v-btn>
                    </template>
                </v-input>
            </v-col>
            <v-col
                class="my-1"
                xs="12"
                sm="12"
                md="12"
                xl="12"
            >
                <v-btn
                    small
                    color="secondary"
                    block
                    :disabled="(isLoading || isExportLoading || !this.hasFilter) && this.currentPage === 1"
                    @click="unsetAllFilter"
                    data-test="reset-way-point-filter"
                >
                    Alle Filter zurücksetzen
                    <mdicon
                        :name="hasFilter ? 'FilterRemoveOutline' : 'FilterOutline'"
                    />
                </v-btn>
            </v-col>
            <v-col cols="12">
                <hr class="my-1" />
            </v-col>
            <v-col
                class="my-1"
                xs="12"
                sm="12"
                md="12"
                xl="12"
            >
                <v-btn
                    small
                    color="secondary"
                    size="sm"
                    block
                    :disabled="isLoading || isExportLoading || this.totalRows === 0"
                    @click="exportWayPoints"
                >
                    {{ this.totalRows > 5000 ? 5000 : this.totalRows }} Wegpunkt{{ this.totalRows !== 1 ? 'e' : '' }} als .csv-Datei exportieren
                    <mdicon
                        :name="isExportLoading ? 'Loading' : 'Download'"
                        :spin="isExportLoading"
                    />
                </v-btn>
            </v-col>
        </v-row>
        <b-table
            small
            striped
            class="mb-0"
            stacked="xl"
            :items="itemProvider"
            :fields="fields"
            :current-page="currentPage"
            :per-page="perPage"
            :filter="filter"
            :sort-by.sync="sortBy"
            :sort-desc.sync="sortDesc"
            :sort-direction="sortDirection"
        >
            <template v-slot:cell(note)="row">
                <div
                    :id="`tooltip-target-note-${ row.item.wayPointId }`"
                    class="mw-25"
                >
                    <nl2br
                        tag="div"
                        :text="row.item.note ? row.item.note.trim() : ''"
                        class-name="text-truncate"
                    />
                </div>
                <b-tooltip :target="`tooltip-target-note-${ row.item.wayPointId }`" triggers="hover click">
                    <nl2br
                        tag="div"
                        :text="row.item.note ? row.item.note.trim() : ''"
                    />
                </b-tooltip>
            </template>
            <template v-slot:cell(oneOnOneInterview)="row">
                <div
                    :id="`tooltip-target-oneOnOneInterview-${ row.item.wayPointId }`"
                    class="mw-25"
                >
                    <nl2br
                        tag="div"
                        :text="row.item.oneOnOneInterview.trim()"
                        class-name="text-truncate"
                    />
                </div>
                <b-tooltip :target="`tooltip-target-oneOnOneInterview-${ row.item.wayPointId }`" triggers="hover click">
                    <nl2br
                        tag="div"
                        :text="row.item.oneOnOneInterview.trim()"
                    />
                </b-tooltip>
            </template>
            <template v-slot:cell(actions)="row">
                <div class="d-flex justify-content-around">
                    <router-link
                        :to="{name: 'WayPointDetail', params: { wayPointId: row.item.wayPointId, walkId: getWalkByIri(row.item.walk)?.walkId }}"
                        :data-test="`button-wegpunkt-ansehen-${ row.item.locationName }`"
                    >
                        <v-btn
                            small
                            :disabled="isLoading"
                            color="secondary"
                        >
                            Wegpunkt ansehen
                            <span class="text-nowrap">
                                <font-awesome-icon icon="map-signs" class="ml-2" />
                                <font-awesome-icon icon="eye" class="ml-2" />
                            </span>
                        </v-btn>
                    </router-link>
                </div>
            </template>
        </b-table>
    </div>
</template>

<script>
'use strict';
import DateRangePicker from 'vue2-daterange-picker';
import 'vue2-daterange-picker/dist/vue2-daterange-picker.css';
import MyInputGroupAppend from '../Common/MyInputGroupAppend.vue';
import dayjs from 'dayjs';
import dateRangePicker from '../../utils/date-range-picker'
import WayPointAPI from '../../api/wayPoint';
import WalkAPI from '../../api/walk.js';
import TagAPI from '../../api/tag.js';
import { useGeneralStore, useTagStore, useWalkStore, useWayPointStore } from '../../stores';
import {FilterComboboxField, FilterTextField} from "@/js/components/Common";
import ColorBadge from "@/js/components/Tags/ColorBadge.vue";

export default {
    name: 'WayPointList',
    components: {
        ColorBadge,
        FilterComboboxField,
        FilterTextField,
        DateRangePicker,
        MyInputGroupAppend,
    },
    props: {},
    data: function () {
        const generalStore = useGeneralStore();

        return {
            generalStore: generalStore,
            tagStore: useTagStore(),
            wayPointStore: useWayPointStore(),
            walkStore: useWalkStore(),
            isLoading: false,
            isExportLoading: false,
            exportCtx: null,
            locale: dateRangePicker.locale,
            ranges: dateRangePicker.ranges,
            fields: [
                { key: 'locationName', label: 'Ort', sortable: true, sortDirection: 'desc', class: 'text-center align-middle' },
                { key: 'malesCount', label: 'Männer', sortable: false, sortDirection: 'desc', class: 'text-center align-middle', formatter: (value, key, item) => {return this.getWalkByIri(item.walk)?.isWithAgeRanges ? value : '-'} },
                { key: 'femalesCount', label: 'Frauen', sortable: false, sortDirection: 'desc', class: 'text-center align-middle', formatter: (value, key, item) => {return this.getWalkByIri(item.walk)?.isWithAgeRanges ? value : '-'}  },
                { key: 'queerCount', label: 'Andere', sortable: false, sortDirection: 'desc', class: 'text-center align-middle', formatter: (value, key, item) => {return this.getWalkByIri(item.walk)?.isWithAgeRanges ? value : '-'}  },
                { key: 'peopleCount', label: 'Anzahl Personen', sortable: false, class: 'text-center align-middle',
                    formatter: (value, key, item) => {
                        return this.getWalkByIri(item.walk)?.isWithPeopleCount ? value : '-';
                    }
                },
                { key: 'note', label: 'Beobachtung', sortable: true, class: 'text-left align-middle' },
                { key: 'oneOnOneInterview', label: 'Einzelgespräch', sortable: true, class: 'text-left align-middle' },
                { key: 'wayPointTags', label: 'Tags', sortable: false, class: 'text-center align-middle', formatter: (value) => {return this.formatTags(value);} },
                {
                    key: 'walk.teamName', label: 'Team', sortable: true, class: 'text-center align-middle',
                    formatter: (value, key, item) => {
                        return this.getWalkByIri(item.walk)?.teamName;
                    },
                },
                {
                    key: 'visitedAt',
                    label: 'Ankunft',
                    sortable: true,
                    class: 'text-center align-middle',
                    formatter: (value) => this.formatStartDate(value),
                },
                {
                    key: 'walk.name',
                    label: 'Runde',
                    sortable: true,
                    class: 'text-center align-middle',
                    formatter: (value, key, item) => {
                        return this.getWalkByIri(item.walk)?.name;
                    },
                },
                { key: 'actions', label: 'Aktionen', class: 'text-center p-y-0' },
            ],
            allTeamNames: [],
            totalRows: 10000,
            tags: [],
            currentPage: 1,
            perPage: 5,
            pageOptions: [5, 10, 25, 50, 100],
            sortBy: 'walk.startTime',
            sortDesc: true,
            sortDirection: 'desc',
            storagePerPageId: 'alle-wegpunkte-per-page',
            storageCurrentPageId: 'alle-wegpunkte-current-page',
            storageFilterId: 'alle-wegpunkte-filter',
            storageWayPointsId: 'alle-wegpunkte-wayPoints',
        };
    },
    computed: {
        filter() {
            return this.generalStore.getWayPointFilter;
        },
        defaultFilter() {
            return this.generalStore.defaultWayPointFilter;
        },
        defaultDateRange() {
            return this.generalStore.defaultWayPointFilter.visitedAt;
        },
        teamNames() {
            return this.allTeamNames.map((teamName) => teamName.teamName);
        },
        hasDisabledTag() {
            return !!this.tags.find(tag => !tag.isEnabled);
        },
        wayPoints() {
            return this.wayPointStore.getWayPoints;
        },
        hasFilter() {
            return JSON.stringify(this.filter) !== JSON.stringify(this.defaultFilter);
        },
    },
    async mounted() {
        this.perPage = this.generalStore.wayPointPerPage;
        this.currentPage = this.generalStore.wayPointCurrentPage;
        const tagResult = await TagAPI.findAllWithWayPoints();
        this.tags = tagResult.data['hydra:member'];
        this.tagStore.fetchTags();
        const allTeamNames = await WalkAPI.findAllTeamNames();
        this.allTeamNames = allTeamNames.data['hydra:member'];
    },
    methods: {
        getTagByIri(iri) {
            return this.tagStore.getTagByIri(iri);
        },
        getWalkByIri(iri) {
            return this.walkStore.getWalkByIri(iri);
        },
        formatTags: function (iriList) {
            let formattedTags = '';
            let tagList = [];
            iriList.forEach((iri) => {
                const tagByIri = this.getTagByIri(iri);
                if (!tagByIri) {
                    return;
                }
                tagList.push(tagByIri);
            });
            tagList = tagList.sort((tagA, tagB) => tagA.name > tagB.name ? 1 : -1);
            tagList.forEach((tag, key) => {
                if (key) {
                    formattedTags += ', ';
                }
                formattedTags += ` ${tag.name}`;
            });

            return formattedTags;
        },
        formatStartDate: function (dateString) {
            return dayjs(dateString).format('dd, DD.MM.YYYY HH:mm:ss');
        },
        async itemProvider(ctx) {
            this.exportCtx = ctx;
            this.isLoading = true;
            const result = await WayPointAPI.find(ctx);
            this.isLoading = false;
            const wayPoints = result.data['hydra:member'];

            let walkPromises = [];
            let walkPromiseIds = [];
            wayPoints.forEach(wayPoint => {
                if (!this.getWalkByIri(wayPoint.walk)) {
                    const id = wayPoint.walk.replace('/api/walks/', '');
                    if (!walkPromiseIds.includes(id)) {
                        walkPromises.push(this.walkStore.fetchById(id));
                        walkPromiseIds.push(id);
                    }
                }
            });
            await Promise.all(walkPromises);

            this.totalRows = result.data['hydra:totalItems'];
            this.generalStore.updateWayPointFilterResult(wayPoints);
            await this.$emit('refresh-total-way-points', this.totalRows);

            return wayPoints;
        },
        handleCurrentPageChange(value) {
            this.generalStore.updateWayPointCurrentPage(Number(value));
        },
        handlePerPageChange(value) {
            this.generalStore.updateWayPointPerPage(Number(value));
        },
        unsetFilterWayPointTags() {
            this.filter.wayPointTags = [];
        },
        unsetFilterLocationName() {
            this.filter.locationName = '';
        },
        unsetFilterNote() {
            this.filter.note = '';
        },
        unsetFilterOneOnOneInterview() {
            this.filter.oneOnOneInterview = '';
        },
        unsetFilterTeamName() {
            this.filter.teamName = '';
        },
        unsetFilterVisitedAt() {
            this.filter.visitedAt = this.defaultDateRange;
        },
        unsetAllFilter() {
            this.generalStore.updateWayPointFilter(this.defaultFilter);
            this.currentPage = 1;
            this.handleCurrentPageChange(1);
        },
        togglePicker() {
            this.$refs.picker.togglePicker(!this.$refs.picker.open);
        },
        forceFileDownload(response, title) {
            const url = window.URL.createObjectURL(new Blob([response.data]));
            const link = document.createElement('a');
            link.href = url;
            link.setAttribute('download', title);
            document.body.appendChild(link);
            link.click();
        },
        exportWayPoints: async function () {
            this.isExportLoading = true;
            const response = await WayPointAPI.export(this.exportCtx);
            this.forceFileDownload(response, this.getFileName());
            this.isExportLoading = false;
        },
        getFileName() {
            let title = `streetworkwegpunkte_export.csv`;

            if (this.filter.wayPointTags.length) {
                const tags = [];
                this.filter.wayPointTags.forEach((tagIri) => {
                    tags.push(this.getTagByIri(tagIri)?.name);
                });
                title = `TAGS_${tags.join('_')}_${title}`;
            }
            if (this.filter.oneOnOneInterview) {
                title = `EINZELGESPRAECH_${this.filter.oneOnOneInterview}_${title}`;
            }
            if (this.filter.note) {
                title = `BEOBACHTUNG_${this.filter.note}_${title}`;
            }
            if (this.filter.teamName) {
                title = `TEAM_${this.filter.teamName}_${title}`;
            }
            if (this.filter.locationName) {
                title = `ORT_${this.filter.locationName}_${title}`;
            }
            if (this.filter?.visitedAt?.startDate && this.filter?.visitedAt?.endDate) {
                const formattedStartDate = dayjs(this.filter.visitedAt.startDate).format('YYYYMMDD');
                const formattedEndDate = dayjs(this.filter.visitedAt.endDate).format('YYYYMMDD');
                if (formattedStartDate === formattedEndDate) {
                    title = `${formattedStartDate}_${title}`;
                } else {
                    title = `${formattedStartDate}-${formattedEndDate}_${title}`;
                }

            }

            return title;
        }
    },
};
</script>

<style>
.mw-25 {
    max-width: 250px;
}
</style>
