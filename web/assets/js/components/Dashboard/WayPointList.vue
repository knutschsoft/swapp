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
                    :disabled="isLoading || isExportLoading || this.totalItems === 0"
                    @click="exportWayPoints"
                >
                    {{ this.totalItems > 5000 ? 5000 : this.totalItems }} Wegpunkt{{ this.totalItems !== 1 ? 'e' : '' }} als .csv-Datei exportieren
                    <mdicon
                        :name="isExportLoading ? 'Loading' : 'Download'"
                        :spin="isExportLoading"
                    />
                </v-btn>
            </v-col>
        </v-row>
        <v-data-table
            striped
            dense
            class="mb-0"
            :items-per-page="itemsPerPage"
            :headers="headers"
            :items="serverItems"
            :items-length="totalItems"
            :items-per-page-options="itemsPerPageOptions"
            :items-per-page-text="itemsPerPageText"
            :loading="isLoading"
            :search="search"
            item-value="name"
            :no-data-text="noItemsText"
            :loading-text="loadingText"
            multi-sort
            hover
            density="compact"
            @update:options="loadItems"
            :options.sync="deprecatedOptions"
            :no-results-text="noItemsText"
            :server-items-length="totalItems"
        >
            <template v-slot:item.malesCount="{item}">
                {{ getWalkByIri(item.walk)?.isWithAgeRanges ? item.malesCount : '-' }}
            </template>
            <template v-slot:item.femalesCount="{item}">
                {{ getWalkByIri(item.walk)?.isWithAgeRanges ? item.femalesCount : '-' }}
            </template>
            <template v-slot:item.queerCount="{item}">
                {{ getWalkByIri(item.walk)?.isWithAgeRanges ? item.queerCount : '-' }}
            </template>
            <template v-slot:item.peopleCount="{item}">
                {{ getWalkByIri(item.walk)?.isWithPeopleCount ? item.peopleCount : '-' }}
            </template>
            <template v-slot:item.note="{item}">
                <v-tooltip
                    bottom
                >
                    <template v-slot:activator="{ on, attrs }">
                        <div
                            class="mw-25"
                            v-bind="attrs"
                            v-on="on"
                        >
                            <nl2br
                                tag="div"
                                :text="item.note?.trim()"
                                class-name="text-truncate"
                            />
                        </div>
                    </template>
                    <nl2br
                        tag="div"
                        :text="item.note?.trim()"
                    />
                </v-tooltip>
            </template>
            <template v-slot:item.oneOnOneInterview="{item}">
                <v-tooltip
                    bottom
                >
                    <template v-slot:activator="{ on, attrs }">
                        <div
                            class="mw-25"
                            v-bind="attrs"
                            v-on="on"
                        >
                            <nl2br
                                tag="div"
                                :text="item.oneOnOneInterview.trim()"
                                class-name="text-truncate"
                            />
                        </div>

                    </template>
                    <nl2br
                        tag="div"
                        :text="item.oneOnOneInterview.trim()"
                    />
                </v-tooltip>
            </template>
            <template v-slot:item.wayPointTags="{item}">
                {{ formatTags(item.wayPointTags) }}
            </template>
            <template v-slot:item.walk.teamName="{item}">
                {{ getWalkByIri(item.walk)?.teamName }}
            </template>
            <template v-slot:item.walk.name="{item}">
                {{ getWalkByIri(item.walk)?.name }}
            </template>
            <template v-slot:item.visitedAt="{item}">
                {{ formatDateTimeNoSecondsWithDayOfWeek(item.visitedAt) }}
            </template>
            <template v-slot:item.actions="{item}">
                <div class="d-flex justify-content-around">
                    <router-link
                        :to="{name: 'WayPointDetail', params: { wayPointId: item.wayPointId, walkId: getWalkByIri(item.walk)?.walkId }}"
                        :data-test="`button-wegpunkt-ansehen-${ item.locationName }`"
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
        </v-data-table>
    </div>
</template>

<script>
'use strict';
import DateRangePicker from 'vue2-daterange-picker';
import 'vue2-daterange-picker/dist/vue2-daterange-picker.css';
import dayjs from 'dayjs';
import dateRangePicker from '../../utils/date-range-picker'
import WayPointAPI from '../../api/wayPoint';
import WalkAPI from '../../api/walk.js';
import TagAPI from '../../api/tag.js';
import { useGeneralStore, useTagStore, useWalkStore, useWayPointStore } from '../../stores';
import {FilterComboboxField, FilterTextField} from "@/js/components/Common";
import ColorBadge from "@/js/components/Tags/ColorBadge.vue";
import {formatDateTimeNoSecondsWithDayOfWeek, itemsPerPageOptions, itemsPerPageText, loadingText, noItemsText} from "@/js/utils";

export default {
    name: 'WayPointList',
    components: {
        ColorBadge,
        FilterComboboxField,
        FilterTextField,
        DateRangePicker,
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
            allTeamNames: [],
            tags: [],
            sortBy: 'walk.startTime',
            sortDesc: true,
            sortDirection: 'desc',
            itemsPerPageText,
            itemsPerPageOptions,
            loadingText,
            noItemsText,
            deprecatedOptions: {},
            totalItems: 0,
            search: '',
            currentPage: 1,
            itemsPerPage: itemsPerPageOptions[0].value,
            serverItems: [],
            tableOptions: [],
        };
    },
    computed: {
        headers() {
            const headers = [
                { value: 'locationName', text: 'Ort' }
            ]

            headers.push(...[
                { value: 'malesCount', text: 'Männer' },
                { value: 'femalesCount', text: 'Frauen' },
                { value: 'queerCount', text: 'Andere' },
            ])

            headers.push({ value: 'peopleCount', text: 'Anzahl Personen' })
            headers.push(...[
                { value: 'note', text: 'Beobachtung' },
                { value: 'oneOnOneInterview', text: 'Einzelgespräch' },
                { value: 'wayPointTags', text: 'Tags', sortable: false },
                {
                    value: 'walk.teamName', text: 'Team',
                },
                {
                    value: 'visitedAt',
                    text: 'Ankunft',
                },
                {
                    value: 'walk.name',
                    text: 'Runde',
                },
                { value: 'actions', text: 'Aktionen' },
            ])

            return headers
        },
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
    watch: {
        filter: {
            handler: async function () {
                this.search = String(Date.now());
                await this.loadItems({ ...this.tableOptions });
                // search.value = String(Date.now())
                // settings.betriebsbeauftragterFilter.store(betriebsbeauftragterFilter.value)
            },
            deep: true,
        },
        deprecatedOptions: {
            handler: async function () {
                await this.loadItems(this.deprecatedOptions);
            },
            deep: true,
        },
    },
    methods: {
        formatDateTimeNoSecondsWithDayOfWeek,
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
        async loadItems({ page, itemsPerPage, sortBy }) {
            this.tableOptions = {page, itemsPerPage, sortBy};
            this.currentPage = page
            const data = {
                page,
                itemsPerPage,
                wayPointTags: this.filter.wayPointTags,
                note: this.filter.note,
                oneOnOneInterview: this.filter.oneOnOneInterview,
                locationName: this.filter.locationName,
                teamName: !this.filter.teamName,
            }
            sortBy.forEach((val) => {
                data[`sortBy[${val.key}]`] = val.order;
            })
            if (this.filter.visitedAt?.startDate && this.filter.visitedAt?.endDate) {
                data['visitedAt[after]'] = dayjs(this.filter.visitedAt.startDate).startOf('day').toISOString()
                data['visitedAt[before]'] = dayjs(this.filter.visitedAt.endDate).endOf('day').toISOString()
            }

            // this.exportCtx = ctx;

            try {
                this.isLoading = true;
                const result = await WayPointAPI.find(data);
                this.isLoading = false;
                const items = result.data['hydra:member'];
                const total = result.data['hydra:totalItems'] ?? 0;

                let walkPromises = [];
                let walkPromiseIds = [];
                items.forEach(wayPoint => {
                    if (!this.getWalkByIri(wayPoint.walk)) {
                        const id = wayPoint.walk.replace('/api/walks/', '');
                        if (!walkPromiseIds.includes(id)) {
                            walkPromises.push(this.walkStore.fetchById(id));
                            walkPromiseIds.push(id);
                        }
                    }
                });
                await Promise.all(walkPromises);
                this.generalStore.updateWayPointFilterResult(items);
                this.serverItems = items;
                this.totalItems = total;
                await this.$emit('refresh-total-way-points', this.totalItems);
            } catch (e) {
                console.error(e);
            }
        },
        handleCurrentPageChange(value) {
            this.generalStore.updateWayPointCurrentPage(Number(value));
        },
        unsetFilterWayPointTags() {
            this.filter.wayPointTags = [];
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
