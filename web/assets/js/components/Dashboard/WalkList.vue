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
                    :disabled="isLoading"
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
                sm="6"
                md="3"
            >
                <b-input-group
                    size="sm"
                >
                    <b-input-group-prepend
                    >
                        <b-input-group-text
                            title="Wiedervorlage zur Dienstberatung?"
                            :class="filter.isResubmission !== null ? 'font-weight-bold' : ''"
                        >
                            WV DB?
                        </b-input-group-text>
                    </b-input-group-prepend>
                    <b-form-select
                        v-model="filter.isResubmission"
                        :options="isResubmissionOptions"
                    />
                    <my-input-group-append
                        @click="unsetFilterIsResubmission"
                        :is-active="filter.isResubmission !== defaultFilter.isResubmission"
                    />
                </b-input-group>
            </b-col>
            <b-col
                class="my-1"
                sm="6"
                md="3"
            >
                <b-input-group size="sm" class="">
                    <b-input-group-prepend>
                        <b-input-group-text
                            title="Wurde die Runde schon beendet?"
                            :class="filter.isUnfinished !== null ? 'font-weight-bold' : ''"
                        >
                            Beendet?
                        </b-input-group-text>
                    </b-input-group-prepend>
                    <b-form-select
                        v-model="filter.isUnfinished"
                        :options="isUnfinishedOptions"
                    />
                    <my-input-group-append
                        @click="unsetFilterIsUnfinished"
                        :is-active="filter.isUnfinished !== defaultFilter.isUnfinished"
                    />
                </b-input-group>
            </b-col>
            <b-col
                class="my-1"
                sm="6"
                md="3"
            >
                <b-input-group size="sm">
                    <b-input-group-prepend>
                        <b-input-group-text
                            :class="filter.name ? 'font-weight-bold' : ''"
                        >
                            Name
                        </b-input-group-text>
                    </b-input-group-prepend>
                    <b-form-input
                        v-model="filter.name"
                        placeholder="Name"
                        debounce="500"
                        size="sm"
                    />
                    <my-input-group-append
                        @click="unsetFilterName"
                        :is-active="filter.name !== defaultFilter.name"
                    />
                </b-input-group>
            </b-col>
            <b-col
                class="my-1"
                sm="6"
                md="3"
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
                        list="team-name-for-walk-list"
                        placeholder="Teamname"
                        data-test="filter-team-walk"
                        autocomplete="off"
                        debounce="500"
                        size="sm"
                    ></b-form-input>
                    <datalist id="team-name-for-walk-list">
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
                            :class="(filter?.startTime?.startDate !== defaultDateRange.startDate || filter?.startTime?.endDate !== defaultDateRange.endDate) ? 'font-weight-bold' : ''"
                        >
                            Beginn
                        </b-input-group-text>
                    </b-input-group-prepend>
                    <date-range-picker
                        ref="picker"
                        class="form-control"
                        v-model="filter.startTime"
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
                        @click="unsetFilterStartTime"
                        :is-active="!((filter?.startTime?.startDate === defaultDateRange.startDate && filter?.startTime?.endDate === defaultDateRange.endDate) || isLoading)"
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
                    :disabled="(isLoading || isExportLoading || !this.hasFilter) && this.currentPage === 1"
                    data-test="reset-walk-filter"
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
                    @click="exportWalks"
                >
                    {{ this.totalRows > 5000 ? 5000 : this.totalRows }} Rund{{ this.totalRows === 1 ? 'e' : 'en' }} als .csv-Datei exportieren
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
            stacked="md"
            :items="itemProvider"
            :fields="fields"
            :current-page="currentPage"
            :per-page="perPage"
            :filter="filter"
            :sort-by.sync="sortBy"
            :sort-desc.sync="sortDesc"
            :sort-direction="sortDirection"
        >
            <template v-slot:cell(rating)="row">
                <walk-rating
                    v-if="!row.item.isUnfinished && getClientByIri(row.item.client)"
                    :rating="row.item.rating"
                    :client="getClientByIri(row.item.client)"
                    :item-size="30"
                    :show-rating="false"
                    read-only
                />
                <template
                    v-else
                >-</template>
            </template>
            <template v-slot:cell(actions)="row">
                <div class="d-flex justify-content-around">
                    <router-link
                        :to="{name: 'WalkDetail', params: { walkId: row.item.walkId}}"
                        :data-test="`button-runde-ansehen-${ row.item.name }`"
                    >
                        <b-button
                            size="sm"
                            :disabled="isLoading"
                        >
                            Runde ansehen
                            <span class="text-nowrap">
                                <font-awesome-icon
                                    icon="walking"
                                    class="bg-secondary ml-2"
                                />
                                <font-awesome-icon icon="eye" class="ml-2"/>
                            </span>
                        </b-button>
                    </router-link>
                    <router-link
                        v-if="row.item.isUnfinished"
                        :to="{name: 'WalkAddWayPoint', params: { walkId: row.item.walkId}}"
                        :data-test="`button-runde-fortsetzen-${ row.item.name }`"
                        class="mt-ml-0 ml-1"
                    >
                        <b-button
                            size="sm"
                            :disabled="isLoading"
                        >
                            Runde fortsetzen
                            <span class="text-nowrap">
                                <font-awesome-icon
                                    icon="walking"
                                    class="bg-secondary ml-2"
                                />
                                <font-awesome-layers>
                                    <font-awesome-icon animation="fade" icon="shoe-prints" class="faa-blink animated" size="xs" transform="shrink-8 down-7" flip="vertical"/>
                                    <font-awesome-icon animation="fade" icon="shoe-prints" class="faa-blink animated" style="animation-delay: 1s;" size="xs"
                                                       transform="shrink-8 down-7"/>
                                </font-awesome-layers>
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
import WalkAPI from '../../api/walk.js';
import dayjs from 'dayjs';
import dateRangePicker from '../../utils/date-range-picker'
import WalkRating from '../Walk/WalkRating.vue';
import { useClientStore } from '../../stores/client';
import { useGeneralStore } from '../../stores/general';
import { useWalkStore } from '../../stores/walk';

export default {
    name: 'WalkList',
    components: {
        WalkRating,
        DateRangePicker,
        MyInputGroupAppend,
    },
    props: {},
    data: function () {
        const generalStore = useGeneralStore();

        return {
            clientStore: useClientStore(),
            generalStore: generalStore,
            walkStore: useWalkStore(),
            isLoading: false,
            isExportLoading: false,
            exportCtx: null,
            locale: dateRangePicker.locale,
            ranges: dateRangePicker.ranges,
            isResubmission: null,
            isUnfinished: null,
            isResubmissionOptions: [
                { value: null, text: 'egal' },
                { value: 1, text: 'ja' },
                { value: 0, text: 'nein' },
            ],
            isUnfinishedOptions: [
                { value: null, text: 'egal' },
                { value: 0, text: 'ja' },
                { value: 1, text: 'nein' },
            ],
            fields: [
                { key: 'name', label: 'Name', sortable: true, sortDirection: 'desc', class: 'text-center align-middle' },
                { key: 'rating', label: 'Bewertung', sortable: true, class: 'text-center align-middle' },
                { key: 'startTime', label: 'Beginn', sortable: true, class: 'text-center align-middle', formatter: (value) => {return this.formatStartDate(value);} },
                { key: 'endTime', label: 'Ende', sortable: false, class: 'text-center align-middle',
                    formatter: (value, key, item) => {
                        return item.isUnfinished ? '-' : this.formatEndDate(value, item.startTime);

                    }
                },
                { key: 'peopleCount', label: 'Anzahl Personen', sortable: false, class: 'text-center align-middle',
                    formatter: (value, key, item) => {
                        return item.isWithPeopleCount ? value : '-';
                    }
                },
                { key: 'teamName', label: 'Team', sortable: true, class: 'text-center align-middle' },
                {
                    key: 'isResubmission',
                    label: 'WV DB?',
                    formatter: (value, key, item) => {
                        return value ? 'Ja' : 'Nein';
                    },
                    sortable: true,
                    sortByFormatted: true,
                    filterByFormatted: true,
                    class: 'text-center align-middle',
                },
                {
                    key: 'startTime',
                    label: 'Rundenbeginn',
                    sortable: true,
                    class: 'text-center align-middle',
                    formatter: (value) => this.formatStartDate(value),
                },
                { key: 'actions', label: 'Aktionen', class: 'text-center p-y-0' },
            ],
            allTeamNames: [],
            totalRows: 10000,
            currentPage: 1,
            perPage: 5,
            pageOptions: [5, 10, 25, 50, 100],
            sortBy: 'startTime',
            sortDesc: true,
            sortDirection: 'desc',
        };
    },
    computed: {
        filter() {
            return this.generalStore.getWalkFilter;
        },
        defaultFilter() {
            return this.generalStore.defaultWalkFilter;
        },
        defaultDateRange() {
            return this.generalStore.defaultWalkFilter.startTime;
        },
        teamNames() {
            const filterTeamName = this.filter.teamName ? this.filter.teamName.toLowerCase() : '';
            return this.allTeamNames.filter((teamName) => {
                return teamName.teamName.toLowerCase().startsWith(filterTeamName);
            }).map((teamName) => teamName.teamName);
        },
        hasWalks() {
            return this.walkStore.hasWalks;
        },
        walks() {
            return this.walkStore.getWalks;
        },
        totalWalks() {
            return this.walkStore.getTotalWalks;
        },
        hasFilter() {
            return JSON.stringify(this.filter) !== JSON.stringify(this.defaultFilter);
        },
    },
    async mounted() {
        this.perPage = this.generalStore.walkPerPage;
        this.currentPage = this.generalStore.walkCurrentPage;
        const allTeamNames = await WalkAPI.findAllTeamNames();
        this.currentPage = this.generalStore.walkCurrentPage;
        this.allTeamNames = allTeamNames.data['hydra:member'];
    },
    methods: {
        getClientByIri(clientIri) {
            return this.clientStore.getClientByIri(clientIri);
        },
        formatStartDate: function (dateString) {
            let date = new Date(dateString);
            return date.toLocaleDateString('de-DE', { weekday: 'short', hour: '2-digit', minute: '2-digit', year: 'numeric', month: '2-digit', day: '2-digit' });
        },
        formatEndDate: function (dateString, startDateString) {
            let date = new Date(dateString);
            if (dayjs(dateString).isSame(dayjs(startDateString), 'day')) {
                return date.toLocaleTimeString('de-DE', { hour: '2-digit', minute: '2-digit' });
            }
            return this.formatStartDate(dateString);
        },
        async itemProvider(ctx) {
            this.exportCtx = ctx;
            this.isLoading = true;
            const result = await WalkAPI.find(ctx);
            this.isLoading = false;
            const walks = result.data['hydra:member']
            this.totalRows = result.data['hydra:totalItems'];
            this.generalStore.updateWalkFilterResult(walks);
            await this.$emit('refresh-total-walks', this.totalRows);

            return walks;
        },
        handleCurrentPageChange(value) {
            this.generalStore.updateWalkCurrentPage(Number(value));
        },
        handlePerPageChange(value) {
            this.generalStore.updateWalkPerPage(Number(value));
        },
        unsetFilterIsResubmission() {
            this.filter.isResubmission = null;
        },
        unsetFilterIsUnfinished() {
            this.filter.isUnfinished = null;
        },
        unsetFilterName() {
            this.filter.name = '';
        },
        unsetFilterTeamName() {
            this.filter.teamName = '';
        },
        unsetFilterStartTime() {
            this.filter.startTime = this.defaultDateRange;
        },
        unsetAllFilter() {
            this.generalStore.updateWalkFilter(this.defaultFilter);
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
        exportWalks: async function () {
            this.isExportLoading = true;
            const response = await WalkAPI.export(this.exportCtx);
            this.forceFileDownload(response, this.getFileName());
            this.isExportLoading = false;
        },
        getFileName() {
            let title = `streetworkrunden_export.csv`;

            if (this.filter.teamName) {
                title = `TEAM_${this.filter.teamName}_${title}`;
            }
            if (null !== this.filter.isResubmission) {
                title = `WV_DB_${this.filter.isResubmission ? 'ja' : 'nein'}_${title}`;
            }
            if (null !== this.filter.isUnfinished) {
                title = `BEENDET_${this.filter.isUnfinished ? 'nein' : 'ja'}_${title}`;
            }
            if (this.filter.name) {
                title = `NAME_${this.filter.name}_${title}`;
            }

            const startDate = dayjs(this.filter?.startTime?.startDate);
            const endDate = dayjs(this.filter?.startTime?.endDate);
            if (startDate.isValid() && endDate.isValid()) {
                const formattedStartDate = startDate.format('YYYYMMDD');
                const formattedEndDate = endDate.format('YYYYMMDD');
                if (formattedStartDate === formattedEndDate) {
                    title = `${formattedStartDate}_${title}`;
                } else {
                    title = `${formattedStartDate}-${formattedEndDate}_${title}`;
                }
            }

            return title;
        },
    },
};
</script>

<style scoped>
</style>
