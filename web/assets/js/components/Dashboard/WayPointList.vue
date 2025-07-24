<template>
    <div class="px-2 pt-2">
        <v-row density="compact">
            <v-col v-if="tags.length" cols="12">
                <div class="d-flex flex-row align-items-center">
                    <div>Tags</div>
                    <v-tooltip
                        bottom
                        content-class="bg-info"
                    >
                        <template v-slot:activator="{ props}">
                            <v-icon
                                class="text-muted ml-2"
                                v-bind="props"
                            >
                                mdi-help-circle-outline
                            </v-icon>
                        </template>
                        <v-alert
                            prominent
                            type="info"
                            density="compact"
                            class="mb-0 m-0"
                        >
                            <span>Welche Tags werden angezeigt?</span>
                            <ul class="mb-0 pl-5">
                                <li>Alle aktivierten Tags, die mindestens einer Runde zugeordnet sind, werden angezeigt.</li>
                                <li>Alle deaktivierten Tags, die mindestens einer Runde zugeordnet sind, werden angezeigt.</li>
                            </ul>
                        </v-alert>
                    </v-tooltip>
                    <v-btn
                        title="Filterung nach Tags entfernen"
                        class="ml-auto"
                        :color="filter.wayPointTags.length ? 'primary-lighten-2' : 'secondary-lighten-4'"
                        size="x-small"
                        @click="unsetFilterWayPointTags"
                        icon
                    >
                        <v-icon
                            color="white"
                            icon="mdi-filter-remove-outline"
                        />
                    </v-btn>
                </div>
                <v-chip-group
                    v-model="filter.wayPointTags"
                    multiple
                    column
                >
                    <template
                        v-for="tag in tags"
                        :key="tag['id']"
                    >
                        <v-chip
                            v-if="tag.isEnabled"
                            :value="tag.tagId"
                            class="mr-1 mb-1"
                            density="compact"
                            filter
                            color="primary"
                            variant="outlined"
                        >
                            {{ tag.name }}
                        </v-chip>
                    </template>
                    <hr
                        v-if="hasDisabledTag"
                        class="d-block w-100 my-1 mr-2"
                    >
                    <template
                        v-for="tag in tags"
                        :key="tag['id']"
                    >
                        <v-chip
                            v-if="!tag.isEnabled"
                            :value="tag.tagId"
                            class="mr-1 mb-1"
                            density="compact"
                            filter
                            color="primary"
                            variant="outlined"
                        >
                            {{ tag.name }}
                            <mdicon
                                name="TagOff"
                                class="text-muted ml-1"
                                title="deaktivierter Tag"
                                size="16"
                            />
                        </v-chip>
                    </template>
                </v-chip-group>
            </v-col>
            <v-col
                cols="12"
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
                cols="12"
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
                cols="12"
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
                cols="12"
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
                cols="12"
                xs="12"
                sm="12"
                md="12"
                xl="12"
            >
                <date-range-picker
                    v-model="filter.startTime"
                    :is-loading="isLoading"
                    data-test="visited-at-filter"
                    placeholder="Ankunft"
                    @cleared="unsetFilterVisitedAt"
                />
            </v-col>
            <v-col
                class="my-1"
                cols="12"
                xs="12"
                sm="12"
                md="12"
                xl="12"
            >
                <v-btn
                    color="secondary"
                    block
                    :disabled="(isLoading || isExportLoading || !this.hasFilter) && this.currentPage === 1"
                    @click="unsetAllFilter"
                    data-test="reset-way-point-filter"
                    density="comfortable"
                    class="text-transform-none"
                    :append-icon="hasFilter ? 'mdi-filter-remove-outline' : 'mdi-filter-outline'"
                >
                    Alle Filter zurücksetzen
                </v-btn>
            </v-col>
            <v-col
                cols="12"
                xs="12"
                sm="12"
                md="12"
                xl="12"
            >
                <v-btn
                    color="secondary"
                    block
                    :disabled="isLoading || isExportLoading || this.totalItems === 0"
                    density="comfortable"
                    class="text-transform-none"
                    append-icon="mdi-download"
                    :loading="isExportLoading"
                    @click="exportWayPoints"
                >
                    {{ this.totalItems > 5000 ? 5000 : this.totalItems }} Wegpunkt{{ this.totalItems !== 1 ? 'e' : '' }} als .csv-Datei exportieren
                </v-btn>
            </v-col>
        </v-row>
        <v-data-table-server
            class="mb-0"
            :items-per-page="itemsPerPage"
            :page="currentPage"
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
            :sort-by="sortBy"
            multi-sort
            mobile-breakpoint="lg"
            density="compact"
            show-current-page
            @update:options="loadItems"
            :no-results-text="noItemsText"
            @update:items-per-page="handlePerPageChange"
            @update:page="handleCurrentPageChange"
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
                <tooltip :text="item.note?.trim()" :nl2br="true" />
            </template>
            <template v-slot:item.oneOnOneInterview="{item}">
                <tooltip :text="item.oneOnOneInterview?.trim()" :nl2br="true" />
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
                    <v-btn
                        density="compact"
                        :disabled="isLoading"
                        color="secondary"
                        :to="{name: 'WayPointDetail', params: { wayPointId: item.wayPointId, walkId: getWalkByIri(item.walk)?.walkId }}"
                        :data-test="`button-wegpunkt-ansehen-${ item.locationName }`"
                        class="text-transform-none"
                    >
                        Wegpunkt ansehen
                        <v-icon icon="mdi-routes" class="ml-1"></v-icon>
                        <v-icon icon="mdi-eye" class="ml-1"></v-icon>
                    </v-btn>
                </div>
            </template>
        </v-data-table-server>
    </div>
</template>

<script>
'use strict';
import dayjs from 'dayjs';
import WayPointAPI from '../../api/wayPoint';
import WalkAPI from '../../api/walk.js';
import TagAPI from '../../api/tag.js';
import { useGeneralStore, useTagStore, useWalkStore, useWayPointStore } from '@/js/stores';
import {DateRangePicker, FilterComboboxField, FilterTextField} from "@/js/components/Common";
import ColorBadge from "@/js/components/Tags/ColorBadge.vue";
import {formatDateTimeNoSecondsWithDayOfWeek, itemsPerPageOptions, itemsPerPageText, loadingText, noItemsText} from "@/js/utils";
import Tooltip from "@/js/components/Common/Tooltip.vue";
import {useDate} from "vuetify";

export default {
    name: 'WayPointList',
    components: {
        Tooltip,
        DateRangePicker,
        ColorBadge,
        FilterComboboxField,
        FilterTextField,
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
            abortController: null,
            exportCtx: null,
            allTeamNames: [],
            tags: [],
            itemsPerPageText,
            itemsPerPageOptions,
            loadingText,
            noItemsText,
            deprecatedOptions: {},
            totalItems: 0,
            search: '',
            currentPage: 1,
            itemsPerPage: itemsPerPageOptions[1].value,
            serverItems: [],
            tableOptions: [],
            sortBy: [{key: 'visitedAt', order: 'desc'}],
        };
    },
    computed: {
        headers() {
            const headers = [
                { value: 'locationName', title: 'Ort', sortable: true  }
            ]

            headers.push(...[
                { value: 'malesCount', title: 'Männer', sortable: true  },
                { value: 'femalesCount', title: 'Frauen', sortable: true  },
                { value: 'queerCount', title: 'Andere', sortable: true  },
            ])

            headers.push({ value: 'peopleCount', title: 'Anzahl Personen', sortable: true, align: 'center'  })
            headers.push(...[
                { value: 'note', title: 'Beobachtung', sortable: true  },
                { value: 'oneOnOneInterview', title: 'Einzelgespräch', sortable: true },
                { value: 'wayPointTags', title: 'Tags', sortable: false },
                { value: 'walk.teamName', title: 'Team', sortable: true },
                { value: 'visitedAt', title: 'Ankunft' },
                { value: 'walk.name', title: 'Runde', sortable: true },
                { value: 'actions', title: 'Aktionen', sortable: false },
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
    async created() {
        this.itemsPerPage = this.generalStore.wayPointPerPage;
        this.currentPage = this.generalStore.wayPointCurrentPage;
        const tagResult = await TagAPI.findAllWithWayPoints();
        this.tags = tagResult.data['member'];
        this.tagStore.fetchTags();
        const allTeamNames = await WalkAPI.findAllTeamNames();
        this.allTeamNames = allTeamNames.data['member'];
    },
    watch: {
        filter: {
            handler: async function () {
                this.search = String(Date.now());
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

            if (this.abortController) {
                this.abortController.abort();
            }
            this.abortController = new AbortController();
            const signal = this.abortController.signal;

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
                data[`order[${val.key}]`] = val.order;
            })
            if (this.filter.visitedAt[0] && this.filter.visitedAt[1]) {
                data['visitedAt[after]'] = dayjs(this.filter.visitedAt[0]).startOf('day').toISOString()
                data['visitedAt[before]'] = dayjs(this.filter.visitedAt[1]).endOf('day').toISOString()
            }

            this.exportCtx = data;

            this.isLoading = true;
            const result = await WayPointAPI.find(data, signal);
            this.isLoading = false;
            const items = result.data['member'];
            const total = result.data['totalItems'] ?? 0;

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
        },
        handleCurrentPageChange(value) {
            this.currentPage = Number(value);
            this.generalStore.updateWayPointCurrentPage(Number(value));
        },
        handlePerPageChange(value) {
            this.itemsPerPage = Number(value);
            this.generalStore.updateWayPointPerPage(Number(value));
        },
        unsetFilterWayPointTags() {
            this.filter.wayPointTags = [];
        },
        unsetFilterVisitedAt() {
            this.filter.visitedAt = this.defaultDateRange;
        },
        unsetAllFilter() {
            this.generalStore.updateWayPointFilter(this.defaultFilter);
            this.handleCurrentPageChange(1);
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
            if (this.filter.teamName.length) {
                title = `TEAM_${this.filter.teamName.join('_')}_${title}`;
            }
            if (this.filter.locationName) {
                title = `ORT_${this.filter.locationName}_${title}`;
            }
            if (this.filter?.visitedAt[0] && this.filter?.visitedAt[1]) {
                const formattedStartDate = dayjs(this.filter.visitedAt[0]).format('YYYYMMDD');
                const formattedEndDate = dayjs(this.filter.visitedAt[1]).format('YYYYMMDD');
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
</style>
