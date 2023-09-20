<template>
    <div class="p-2">
        <b-row>
            <b-col
                class="my-1"
                xs="5"
                sm="4"
                md="3"
            >
                <b-input-group prepend="pro Seite" size="sm" class="">
                    <b-form-select
                        v-model="perPage"
                        id="perPageSelect"
                        size="sm"
                        :options="pageOptions"
                        @change="handlePerPageChange"
                    ></b-form-select>
                </b-input-group>
            </b-col>
            <b-col
                sm="8"
                md="9"
                class="my-1"
            >
                <b-pagination
                    v-model="currentPage"
                    :total-rows="totalRows"
                    :per-page="perPage"
                    @change="handleCurrentPageChange"
                    align="fill"
                    size="sm"
                    class="my-0"
                ></b-pagination>
            </b-col>
            <b-col cols="12">
                <hr class="my-1" />
            </b-col>
            <b-col
                class="my-1"
                sm="12"
            >
                <b-input-group size="sm" class="" style="flex-wrap: inherit;">
                    <b-input-group-prepend>
                        <b-input-group-text
                            :class="filter.wayPointTags.length ? 'font-weight-bold' : ''"
                        >
                            Tags
                            <div id="tag-filter-wayPoints">
                                <mdicon
                                    name="help-circle-outline"
                                    class="text-muted ml-1"
                                    size="22"
                                />
                            </div>
                            <b-popover
                                target="tag-filter-wayPoints"
                                triggers="hover"
                                placement="top"
                            >
                                <template #title>Welche Tags werden angezeigt?</template>
                                <ul class="mb-0">
                                    <li>Alle aktivierten Tags, die mindestens einer Runde zugeordnet sind, werden angezeigt.</li>
                                    <li>Alle deaktivierten Tags, die mindestens einer Runde zugeordnet sind, werden angezeigt.</li>
                                </ul>
                            </b-popover>
                        </b-input-group-text>
                    </b-input-group-prepend>
                    <b-form-group
                        v-slot="{ ariaDescribedby }"
                        class="pl-2 border-top border-bottom mb-0"
                    >
                        <div class="d-flex flex-wrap">
                            <template
                                v-for="tag in tags"
                            >
                                <b-form-checkbox
                                    v-if="tag.isEnabled"
                                    v-model="filter.wayPointTags"
                                    :key="tag['@id']"
                                    :value="tag['@id']"
                                    :aria-describedby="ariaDescribedby"
                                    name="tags"
                                    class="d-flex align-items-center flex-tags"
                                >
                                    {{ tag.name }}
                                </b-form-checkbox>
                            </template>
                            <hr
                                v-if="hasDisabledTag"
                                class="d-block w-100 my-1 mr-2"
                            >
                            <template
                                v-for="tag in tags"
                            >
                                <b-form-checkbox
                                    v-if="!tag.isEnabled"
                                    v-model="filter.wayPointTags"
                                    :key="tag['@id']"
                                    :value="tag['@id']"
                                    :aria-describedby="ariaDescribedby"
                                    name="tags"
                                    class="d-flex align-items-center flex-tags"
                                >
                                    {{ tag.name }}
                                    <mdicon
                                        name="TagOff"
                                        class="text-muted"
                                        title="deaktivierter Tag"
                                        size="16"
                                    />
                                </b-form-checkbox>
                            </template>
                        </div>
                    </b-form-group>
                    <my-input-group-append
                        @click="unsetFilterWayPointTags"
                        :is-active="filter.wayPointTags.length > 0"
                    />
                </b-input-group>
            </b-col>
            <b-col
                class="my-1"
                sm="6"
                md="6"
                xl="4"
            >
                <b-input-group size="sm">
                    <b-input-group-prepend>
                        <b-input-group-text
                            :class="filter.note ? 'font-weight-bold' : ''"
                        >
                            Beobachtung
                        </b-input-group-text>
                    </b-input-group-prepend>
                    <b-form-input
                        v-model="filter.note"
                        placeholder="Beobachtung"
                        debounce="500"
                        size="sm"
                    />
                    <my-input-group-append
                        @click="unsetFilterNote"
                        :is-active="filter.note !== defaultFilter.note"
                    />
                </b-input-group>
            </b-col>
            <b-col
                class="my-1"
                sm="6"
                md="6"
                xl="4"
            >
                <b-input-group size="sm">
                    <b-input-group-prepend>
                        <b-input-group-text
                            :class="filter.oneOnOneInterview ? 'font-weight-bold' : ''"
                        >
                            Einzelgespräch
                        </b-input-group-text>
                    </b-input-group-prepend>
                    <b-form-input
                        v-model="filter.oneOnOneInterview"
                        placeholder="Einzelgespräch"
                        debounce="500"
                        size="sm"
                    />
                    <my-input-group-append
                        @click="unsetFilterOneOnOneInterview"
                        :is-active="filter.oneOnOneInterview !== defaultFilter.oneOnOneInterview"
                    />
                </b-input-group>
            </b-col>
            <b-col
                class="my-1"
                sm="6"
                md="6"
                xl="2"
            >
                <b-input-group size="sm">
                    <b-input-group-prepend>
                        <b-input-group-text
                            :class="filter.locationName ? 'font-weight-bold' : ''"
                        >
                            Ort
                        </b-input-group-text>
                    </b-input-group-prepend>
                    <b-form-input
                        v-model="filter.locationName"
                        placeholder="Ort"
                        debounce="500"
                        size="sm"
                    />
                    <my-input-group-append
                        @click="unsetFilterLocationName"
                        :is-active="filter.locationName !== defaultFilter.locationName"
                    />
                </b-input-group>
            </b-col>
            <b-col
                class="my-1"
                sm="6"
                md="6"
                xl="2"
            >
                <b-input-group size="sm">
                    <b-input-group-prepend>
                        <b-input-group-text
                            :class="filter.teamName ? 'font-weight-bold' : ''"
                        >
                            Team
                        </b-input-group-text>
                    </b-input-group-prepend>
                    <b-form-input
                        v-model="filter.teamName"
                        type="text"
                        list="team-name-for-wayPoint-list"
                        placeholder="Teamname"
                        data-test="filter-team-wayPoint"
                        autocomplete="off"
                        debounce="500"
                        size="sm"
                    ></b-form-input>
                    <datalist id="team-name-for-wayPoint-list">
                        <option v-for="teamName in teamNames">{{ teamName }}</option>
                    </datalist>
                    <my-input-group-append
                        @click="unsetFilterTeamName"
                        :is-active="filter.teamName !== defaultFilter.teamName"
                    />
                </b-input-group>
            </b-col>
            <b-col
                class="my-1"
                xs="12"
                sm="12"
                md="12"
                xl="12"
            >
                <b-input-group size="sm">
                    <b-input-group-prepend
                        @click.stop="togglePicker"
                    >
                        <b-input-group-text
                            :class="(filter.visitedAt.startDate !== defaultDateRange.startDate || filter.visitedAt.endDate !== defaultDateRange.endDate) ? 'font-weight-bold' : ''"
                        >
                            Ankunft
                        </b-input-group-text>
                    </b-input-group-prepend>
                    <date-range-picker
                        ref="picker"
                        class="form-control"
                        v-model="filter.visitedAt"
                        :ranges="ranges"
                        :locale-data="locale"
                        auto-apply
                        show-dropdowns
                        opens="right"
                        :readonly="isLoading"
                        :disabled="isLoading"
                    >
                    </date-range-picker>
                    <b-input-group-append
                        @click.stop="togglePicker"
                    >
                        <b-input-group-text>
                            <mdicon
                                v-if="isLoading"
                                name="loading"
                                size="18"
                                spin
                            />
                            <mdicon
                                v-else
                                size="18"
                                name="calendar"
                            />
                        </b-input-group-text>
                    </b-input-group-append>
                    <my-input-group-append
                        @click="unsetFilterVisitedAt"
                        :is-active="!((filter.visitedAt.startDate === defaultDateRange.startDate && filter.visitedAt.endDate === defaultDateRange.endDate) || isLoading)"
                    />
                </b-input-group>
            </b-col>
            <b-col
                class="my-1"
                xs="12"
                sm="12"
                md="12"
                xl="12"
            >
                <b-button
                    size="sm"
                    block
                    :disabled="isLoading || isExportLoading || !this.hasFilter"
                    @click="unsetAllFilter"
                >
                    Alle Filter zurücksetzen
                    <mdicon
                        :name="hasFilter ? 'FilterRemoveOutline' : 'FilterOutline'"
                    />
                </b-button>
            </b-col>
            <b-col cols="12">
                <hr class="my-1" />
            </b-col>
            <b-col
                class="my-1"
                xs="12"
                sm="12"
                md="12"
                xl="12"
            >
                <b-button
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
                </b-button>
            </b-col>
        </b-row>
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
                        <b-button
                            size="sm"
                            :disabled="isLoading"
                        >
                            Wegpunkt ansehen
                            <span class="text-nowrap">
                                <font-awesome-icon
                                    icon="map-signs"
                                    class="bg-secondary ml-2"
                                />
                                <font-awesome-icon icon="eye" class="ml-2"/>
                            </span>
                        </b-button>
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
import MyInputGroupAppend from '../Common/MyInputGroupAppend';
import dayjs from 'dayjs';
import dateRangePicker from '../../utils/date-range-picker'
import WayPointAPI from '../../api/wayPoint';
import WalkAPI from '../../api/walk.js';
import TagAPI from '../../api/tag.js';
import { useTagStore } from '../../stores/tag';
import { useWayPointStore } from '../../stores/way-point';
import { useWalkStore } from '../../stores/walk';
import { useGeneralStore } from '../../stores/general';

export default {
    name: 'WayPointList',
    components: {
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
            totalRows: 0,
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
            return this.generalStore.wayPointFilter;
        },
        defaultFilter() {
            return this.generalStore.defaultWayPointFilter;
        },
        defaultDateRange() {
            return this.generalStore.defaultWayPointFilter.visitedAt;
        },
        teamNames() {
            const filterTeamName = this.filter.teamName ? this.filter.teamName.toLowerCase() : '';
            return this.allTeamNames.filter((teamName) => {
                return teamName.teamName.toLowerCase().startsWith(filterTeamName);
            }).map((teamName) => teamName.teamName);
        },
        hasDisabledTag() {
            return !!this.tags.find(tag => !tag.isEnabled);
        },
        wayPoints() {
            return this.wayPointStore.getWayPoints;
        },
        isLoading() {
            return this.wayPointStore.isLoading;
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
            const result = await WayPointAPI.find(ctx);
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
            this.filter = JSON.parse(JSON.stringify(this.defaultFilter));
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
            if (this.filter.visitedAt.startDate && this.filter.visitedAt.endDate) {
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
