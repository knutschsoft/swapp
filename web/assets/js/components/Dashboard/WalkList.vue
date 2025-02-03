<template>
    <div class="p-2">
        <v-row class="mt-0 mb-0">
            <v-col
                sm="6"
                md="3"
            >
                <filter-boolean-field
                    v-model="filter.isResubmission"
                    label="Wiedervorlage zur Dienstberatung?"
                    :is-loading="isLoading"
                />
            </v-col>
            <v-col
                sm="6"
                md="3"
            >
                <filter-boolean-field
                    v-model="filter.isUnfinished"
                    label="Beendet?"
                    :is-loading="isLoading"
                />
            </v-col>
            <v-col
                class="my-1"
                sm="6"
                md="3"
            >
                <filter-text-field
                    v-model="filter.name"
                    label="Name"
                    data-test="filter-name-walk"
                    :isLoading="isLoading"
                />
            </v-col>
            <v-col
                class="my-1"
                sm="6"
                md="3"
            >
                <filter-combobox-field
                    v-model="filter.teamName"
                    label="Team"
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
                    @click:append="unsetFilterStartTime"
                    @click:prepend="togglePicker"
                    hide-details
                >
                    <template v-slot:prepend>
                        <div
                            :class="!((filter?.startTime?.startDate === defaultDateRange.startDate && filter?.startTime?.endDate === defaultDateRange.endDate) || isLoading) ? 'font-weight-bold' : ''"
                            class="mt-2"
                        >
                            Zeitraum
                        </div>
                    </template>
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
                            :color="!((filter?.startTime?.startDate === defaultDateRange.startDate && filter?.startTime?.endDate === defaultDateRange.endDate) || isLoading) ? 'blue darken-2' : 'secondary lighten-4'"
                            x-small
                            fab
                        >
                            <v-icon
                                color="white"
                                @click="unsetFilterStartTime"
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
                    data-test="reset-walk-filter"
                    @click="unsetAllFilter"
                >
                    Alle Filter zurücksetzen
                    <mdicon
                        :name="hasFilter ? 'FilterRemoveOutline' : 'FilterOutline'"
                    />
                </v-btn>
            </v-col>
            <v-col cols="12">
                <hr class="my-1 mb-2" />
            </v-col>
            <v-col
                xs="12"
                sm="12"
                md="12"
                xl="12"
            >
                <v-btn
                    small
                    color="secondary"
                    block
                    :disabled="isLoading || isExportLoading || this.totalItems === 0"
                    @click="exportWalks"
                >
                    {{ this.totalItems > 5000 ? 5000 : this.totalItems }} Rund{{ this.totalItems === 1 ? 'e' : 'en' }} als .csv-Datei exportieren
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
            <template v-slot:item.startTime="{item}">
                {{ formatDateTimeNoSecondsWithDayOfWeek(item.startTime) }}
            </template>
            <template v-slot:item.rating="{item}">
                <walk-rating
                    v-if="!item.isUnfinished && getClientByIri(item.client)"
                    :rating="item.rating"
                    :client="getClientByIri(item.client)"
                    :item-size="30"
                    :show-rating="false"
                    read-only
                />
                <template
                    v-else
                >-</template>
            </template>
            <template v-slot:item.endTime="{item}">
                <template v-if="item.isUnfinished">-</template>
                <template v-else> {{ formatEndDate(item.endTime, item.startTime) }}</template>
            </template>
            <template v-slot:item.isResubmission="{item}">
                {{ item.isResubmission ? 'ja' : 'nein' }}
            </template>
            <template v-slot:item.actions="{item}">
                <div class="d-flex justify-content-around">
                    <router-link
                        :to="{name: 'WalkDetail', params: { walkId: item.walkId}}"
                        :data-test="`button-runde-ansehen-${ item.name }`"
                    >
                        <v-btn
                            small
                            color="secondary"
                            :disabled="isLoading"
                        >
                            Runde ansehen
                            <span class="text-nowrap">
                                <font-awesome-icon icon="walking" class="ml-2" />
                                <font-awesome-icon icon="eye" class="ml-2" />
                            </span>
                        </v-btn>
                    </router-link>
                    <router-link
                        v-if="item.isUnfinished"
                        :to="{name: 'WalkAddWayPoint', params: { walkId: item.walkId}}"
                        :data-test="`button-runde-fortsetzen-${ item.name }`"
                        class="mt-ml-0 ml-1"
                    >
                        <v-btn
                            small
                            color="secondary"
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
import MyInputGroupAppend from '../Common/MyInputGroupAppend.vue';
import WalkAPI from '../../api/walk.js';
import dayjs from 'dayjs';
import dateRangePicker from '../../utils/date-range-picker'
import WalkRating from '../Walk/WalkRating.vue';
import { useClientStore, useGeneralStore, useWalkStore } from '../../stores';
import {formatDateTimeNoSecondsWithDayOfWeek, formatTime, itemsPerPageOptions, itemsPerPageText, loadingText, noItemsText} from "@/js/utils";
import {FilterBooleanField, FilterComboboxField, FilterTextField, TextareaField} from "@/js/components/Common";
import {WalkConceptOfDayField} from "@/js/components/Common/Walk";

export default {
    name: 'WalkList',
    components: {
        FilterBooleanField,
        WalkConceptOfDayField,
        FilterComboboxField,
        FilterTextField,
        TextareaField,
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
            headers: [
                { value: 'name', text: 'Name', sortable: true, sortDirection: 'desc', class: 'text-center align-middle' },
                { value: 'rating', text: 'Bewertung', sortable: true, class: 'text-center align-middle' },
                { value: 'startTime', text: 'Rundenbeginn' },
                { value: 'endTime', text: 'Ende', sortable: false },
                { value: 'peopleCount', text: 'Anzahl Personen', sortable: false, class: 'text-center align-middle',
                    formatter: (value, key, item) => {
                        return item.isWithPeopleCount ? value : '-';
                    }
                },
                { value: 'teamName', text: 'Team', sortable: true, class: 'text-center align-middle' },
                {
                    value: 'isResubmission',
                    text: 'WV DB?',
                },
                { value: 'actions', text: 'Aktionen', class: 'text-center p-y-0' },
            ],
            allTeamNames: [],
            sortBy: 'startTime',
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
            return this.allTeamNames.map((teamName) => teamName.teamName);
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
        this.itemsPerPage = this.generalStore.walkPerPage;
        this.currentPage = this.generalStore.walkCurrentPage;
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
        getClientByIri(clientIri) {
            return this.clientStore.getClientByIri(clientIri);
        },
        formatEndDate: function (dateString, startDateString) {
            let date = new Date(dateString);
            if (dayjs(dateString).isSame(dayjs(startDateString), 'day')) {
                return formatTime(date);
            }
            return this.formatDateTimeNoSecondsWithDayOfWeek(dateString);
        },
        async loadItems({ page, itemsPerPage, sortBy }) {
            this.tableOptions = {page, itemsPerPage, sortBy};
            this.currentPage = page
            const data = {
                page,
                itemsPerPage,
                teamName: this.filter.teamName,
                name: this.filter.name,
                isResubmission: this.filter.isResubmission,
                isUnfinished: !this.filter.isUnfinished,
            }
            sortBy.forEach((val) => {
                data[`sortBy[${val.key}]`] = val.order;
            })
            if (this.filter.startTime?.startDate && this.filter.startTime?.endDate) {
                data['startTime[after]'] = dayjs(this.filter.startTime.startDate).startOf('day').toISOString()
                data['startTime[before]'] = dayjs(this.filter.startTime.endDate).endOf('day').toISOString()
            }

            try {
                this.isLoading = true;
                const result = await WalkAPI.find(data);
                this.isLoading = false;
                const items = result.data['hydra:member'];
                const total = result.data['hydra:totalItems'] ?? 0;
                this.generalStore.updateWalkFilterResult(items);
                this.serverItems = items;
                this.totalItems = total;
                await this.$emit('refresh-total-walks', this.totalItems);
            } catch (e) {
                console.error(e);
            }
        },
        handleCurrentPageChange(value) {
            this.generalStore.updateWalkCurrentPage(Number(value));
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
