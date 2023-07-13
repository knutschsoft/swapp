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
                        @change="handleFilterChange"
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
                        @change="handleFilterChange"
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
                        @update="handleFilterChange"
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
                        @update="handleFilterChange"
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
                            :class="(filter.startTime.startDate !== defaultDateRange.startDate || filter.startTime.endDate !== defaultDateRange.endDate) ? 'font-weight-bold' : ''"
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
                        @update="handleFilterChange"
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
                        :is-active="!((filter.startTime.startDate === defaultDateRange.startDate && filter.startTime.endDate === defaultDateRange.endDate) || isLoading)"
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
                        :to="{name: 'WalkDetail', params: { walkId: row.item.id}}"
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
                        :to="{name: 'WalkAddWayPoint', params: { walkId: row.item.id}}"
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

export default {
    name: 'WalkList',
    components: {
        WalkRating,
        DateRangePicker,
        MyInputGroupAppend,
    },
    props: {},
    data: function () {
        const defaultDateRange = {
            startDate: null,
            endDate: null,
        }
        const defaultFilter = {
            isResubmission: null,
            isUnfinished: null,
            name: '',
            teamName: '',
            startTime: defaultDateRange,
        };

        let filter = this.$localStorage.get('abgeschlossene-runden-filter', defaultFilter);
        if (!filter.startTime) {
            filter.startTime = defaultDateRange;
        }
        if (filter.startTime.startDate && filter.startTime.endDate) {
            filter.startTime.startDate = new Date(filter.startTime.startDate);
            filter.startTime.endDate = new Date(filter.startTime.endDate);
        }

        return {
            isExportLoading: false,
            exportCtx: null,
            locale: dateRangePicker.locale,
            defaultDateRange: defaultDateRange,
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
            totalRows: 0,
            currentPage: null,
            perPage: null,
            pageOptions: [5, 10, 25, 50, 100],
            sortBy: 'startTime',
            sortDesc: true,
            sortDirection: 'desc',
            filter: filter,
            defaultFilter: defaultFilter,
            storagePerPageId: 'abgeschlossene-runden-per-page',
            storageCurrentPageId: 'abgeschlossene-runden-current-page',
            storageFilterId: 'abgeschlossene-runden-filter',
            storageWalksId: 'abgeschlossene-runden-walks',
        };
    },
    computed: {
        teamNames() {
            const filterTeamName = this.filter.teamName ? this.filter.teamName.toLowerCase() : '';
            return this.allTeamNames.filter((teamName) => {
                return teamName.teamName.toLowerCase().startsWith(filterTeamName);
            }).map((teamName) => teamName.teamName);
        },
        hasWalks() {
            return this.$store.getters['walk/hasWalks'];
        },
        walks() {
            return this.$store.getters['walk/walks'];
        },
        totalWalks() {
            return this.$store.getters['walk/totalWalks'];
        },
        isLoading() {
            return this.$store.getters['walk/isLoading'];
        },
        hasFilter() {
            return JSON.stringify(this.filter) !== JSON.stringify(this.defaultFilter);
        },
    },
    async mounted() {
        this.perPage = this.$localStorage.get(this.storagePerPageId, 5);
        this.currentPage = this.$localStorage.get(this.storageCurrentPageId, 1);
        const allTeamNames = await WalkAPI.findAllTeamNames();
        this.allTeamNames = allTeamNames.data['hydra:member'];
    },
    methods: {
        getClientByIri(clientIri) {
            return this.$store.getters['client/getClientByIri'](clientIri);
        },
        formatStartDate: function (dateString) {
            let date = new Date(dateString);
            return date.toLocaleDateString('de-DE', { weekday: 'short', hour: '2-digit', minute: '2-digit', year: 'numeric', month: '2-digit', day: '2-digit' });
        },
        formatEndDate: function (dateString, startDateString) {
            let date = new Date(dateString);
            let startDate = new Date(startDateString);
            if (dayjs(dateString).isSame(dayjs(startDateString), 'day')) {
                return date.toLocaleTimeString('de-DE', { hour: '2-digit', minute: '2-digit' });
            }
            return this.formatStartDate(dateString);
        },
        async itemProvider(ctx) {
            this.exportCtx = ctx;
            const result = await WalkAPI.find(ctx);
            const walks = result.data['hydra:member']
            this.totalRows = result.data['hydra:totalItems'];
            this.$localStorage.set(this.storageWalksId, walks);
            await this.$emit('refresh-total-walks', this.totalRows);

            return walks;
        },
        handleCurrentPageChange(value) {
            this.$localStorage.set(this.storageCurrentPageId, value);
        },
        handlePerPageChange(value) {
            this.$localStorage.set(this.storagePerPageId, value);
        },
        handleFilterChange() {
            this.$localStorage.set(this.storageFilterId, this.filter);
        },
        unsetFilterIsResubmission() {
            this.filter.isResubmission = null;
            this.handleFilterChange();
        },
        unsetFilterIsUnfinished() {
            this.filter.isUnfinished = null;
            this.handleFilterChange();
        },
        unsetFilterName() {
            this.filter.name = '';
            this.handleFilterChange();
        },
        unsetFilterTeamName() {
            this.filter.teamName = '';
            this.handleFilterChange();
        },
        unsetFilterStartTime() {
            this.filter.startTime = this.defaultDateRange;
            this.handleFilterChange();
        },
        unsetAllFilter() {
            this.filter = JSON.parse(JSON.stringify(this.defaultFilter));
            this.handleFilterChange();
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
            if (this.filter.startTime.startDate && this.filter.startTime.endDate) {
                const formattedStartDate = dayjs(this.filter.startTime.startDate).format('YYYYMMDD');
                const formattedEndDate = dayjs(this.filter.startTime.endDate).format('YYYYMMDD');
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
