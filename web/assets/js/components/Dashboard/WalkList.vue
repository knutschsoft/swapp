<template>
    <div class="px-2 pt-2">
        <v-row dense class="">
            <v-col
                cols="12"
                sm="6"
                md="3"
                :xl="!guestNames.length ? 2 : 3"
            >
                <filter-boolean-field
                    v-model="filter.isResubmission"
                    label="Wiedervorlage zur Dienstberatung?"
                    :is-loading="isLoading"
                    @cleared="filter.isResubmission = 'null'"
                />
            </v-col>
            <v-col
                cols="12"
                sm="6"
                md="3"
                :xl="!guestNames.length ? 2 : 3"
            >
                <filter-boolean-field
                    v-model="filter.isUnfinished"
                    label="Beendet?"
                    :is-loading="isLoading"
                    @cleared="filter.isUnfinished = 'null'"
                />
            </v-col>
            <v-col
                cols="12"
                no-gutters
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
                cols="12"
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
                v-if="guestNames.length"
                cols="12"
                sm="6"
                md="6"
            >
                <filter-combobox-field
                    v-model="filter.guestNames"
                    label="weitere Teilnehmende"
                    data-test="filter-team-guest-names"
                    :is-loading="isLoading"
                    :suggestions="guestNames"
                />
            </v-col>
            <v-col
                cols="12"
                xs="12"
                :sm="!guestNames.length ? 12 : 6"
                :md="!guestNames.length ? 12 : 6"
                :xl="!guestNames.length ? 2 : null"
            >
                <date-range-picker
                    v-model="filter.startTime"
                    :is-loading="isLoading"
                    data-test="start-time-filter"
                    placeholder="Rundenbeginn"
                    @cleared="unsetFilterStartTime"
                />
            </v-col>
            <v-col
                cols="12"
                class="my-1"
                xs="12"
                sm="12"
                md="12"
                xl="12"
            >
                <v-btn
                    color="secondary"
                    block
                    :disabled="(isLoading || isExportLoading || !this.hasFilter) && this.currentPage === 1"
                    data-test="reset-walk-filter"
                    class="text-transform-none"
                    density="comfortable"
                    :append-icon="hasFilter ? 'mdi-filter-remove-outline' : 'mdi-filter-outline'"
                    @click="unsetAllFilter"
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
                    class="text-transform-none"
                    density="comfortable"
                    append-icon="mdi-download"
                    :loading="isExportLoading"
                    @click="exportWalks"
                >
                    {{ this.totalItems > 5000 ? 5000 : this.totalItems }} Rund{{ this.totalItems === 1 ? 'e' : 'en' }} als .csv-Datei exportieren
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
            multi-sort
            :sort-by="sortBy"
            mobile-breakpoint="md"
            density="compact"
            show-current-page
            @update:options="loadItems"
            :no-results-text="noItemsText"
            @update:items-per-page="handlePerPageChange"
            @update:page="handleCurrentPageChange"
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
                <div class="d-flex flex-wrap align-center align-content-center justify-center">
                    <v-btn
                        :to="{name: 'WalkDetail', params: { walkId: item.walkId}}"
                        :data-test="`button-runde-ansehen-${ item.name }`"
                        color="secondary"
                        :disabled="isLoading"
                        density="compact"
                        class="my-1 text-transform-none"
                    >
                        Runde ansehen
                        <v-icon icon="mdi-walk" class="ml-1"></v-icon>
                        <v-icon icon="mdi-eye" class="ml-1"></v-icon>
                    </v-btn>
                    <v-btn
                        v-if="item.isUnfinished"
                        :to="{name: 'WalkAddWayPoint', params: { walkId: item.walkId}}"
                        :data-test="`button-runde-fortsetzen-${ item.name }`"
                        density="compact"
                        color="secondary"
                        class="ml-1 my-1 text-transform-none"
                        :disabled="isLoading"
                    >
                        Runde fortsetzen
                        <v-icon icon="mdi-walk" class="ml-1"></v-icon>
                        <v-icon icon="mdi-shoe-print" class="ml-1"></v-icon>
                    </v-btn>
                </div>
            </template>
        </v-data-table-server>
    </div>
</template>

<script>
'use strict';
import WalkAPI from '../../api/walk.js';
import dayjs from 'dayjs';
import WalkRating from '../Walk/WalkRating.vue';
import { useClientStore, useGeneralStore, useWalkStore } from '@/js/stores';
import {formatDateTimeNoSecondsWithDayOfWeek, formatTime, itemsPerPageOptions, itemsPerPageText, loadingText, noItemsText} from "@/js/utils";
import {FilterBooleanField, FilterComboboxField, FilterTextField, TextareaField} from "@/js/components/Common";
import {WalkConceptOfDayField} from "@/js/components/Common/Walk";
import {DateRangePicker} from "@/js/components/Common";

export default {
    name: 'WalkList',
    components: {
        DateRangePicker,
        FilterBooleanField,
        WalkConceptOfDayField,
        FilterComboboxField,
        FilterTextField,
        TextareaField,
        WalkRating,
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
            abortController: null,
            exportCtx: null,
            isResubmission: null,
            isUnfinished: null,
            headers: [
                { key: 'name', title: 'Name', sortDirection: 'desc', align: 'center' },
                { key: 'rating', title: 'Bewertung', align: 'center' },
                { key: 'startTime', title: 'Rundenbeginn' },
                { key: 'endTime', title: 'Ende', sortable: false },
                { key: 'peopleCount', title: 'Anzahl Personen', sortable: false, align: 'center' },
                { key: 'teamName', title: 'Team', align: 'center' },
                { key: 'isResubmission', title: 'WV DB?' },
                { key: 'actions', title: 'Aktionen', align: 'center', sortable: false },
            ],
            allTeamNames: [],
            allGuestNames: [],
            itemsPerPageText,
            itemsPerPageOptions,
            loadingText,
            noItemsText,
            totalItems: 0,
            search: '',
            currentPage: 1,
            itemsPerPage: itemsPerPageOptions[1].value,
            serverItems: [],
            tableOptions: [],
            sortBy: [{key: 'startTime', order: 'desc'}],
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
        guestNames() {
            return this.allGuestNames.map((guestName) => guestName.name);
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
    async created() {
        this.itemsPerPage = this.generalStore.walkPerPage;
        this.currentPage = this.generalStore.walkCurrentPage;
        // this.currentPage = 1;
        const allTeamNames = await WalkAPI.findAllTeamNames();
        this.allTeamNames = allTeamNames.data['member'];
        const allGuestNames = await WalkAPI.findAllGuestNames();
        this.allGuestNames = allGuestNames.data['member'];
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

            if (this.abortController) {
                this.abortController.abort();
            }
            this.abortController = new AbortController();
            const signal = this.abortController.signal;

            const data = {
                page,
                itemsPerPage,
                teamName: this.filter.teamName,
                guestNames: this.filter.guestNames,
                name: '' !== this.filter.name ? this.filter.name : undefined,
                isResubmission: this.filter.isResubmission !== 'null' ? this.filter.isResubmission : undefined,
                isUnfinished: this.filter.isUnfinished !== 'null' ? !this.filter.isUnfinished : undefined,
            }
            sortBy.forEach((val) => {
                data[`order[${val.key}]`] = val.order;
            })

            if (this.filter.startTime[0] && this.filter.startTime[1]) {
                data['startTime[after]'] = dayjs(this.filter.startTime[0]).startOf('day').toISOString()
                data['startTime[before]'] = dayjs(this.filter.startTime[1]).endOf('day').toISOString()
            }

            this.exportCtx = data

            this.isLoading = true;
            const result = await WalkAPI.find(data, signal);
            this.isLoading = false;
            const items = result.data['member'];
            const total = result.data['totalItems'] ?? 0;
            this.generalStore.updateWalkFilterResult(items);
            this.serverItems = items;
            this.totalItems = total;
            await this.$emit('refresh-total-walks', this.totalItems);
        },
        handleCurrentPageChange(value) {
            this.currentPage = Number(value);
            this.generalStore.updateWalkCurrentPage(Number(value));
        },
        handlePerPageChange(value) {
            this.itemsPerPage = Number(value);
            this.generalStore.updateWalkPerPage(Number(value));
        },
        unsetFilterStartTime() {
            this.filter.startTime = this.defaultDateRange;
        },
        unsetAllFilter() {
            this.generalStore.updateWalkFilter(this.defaultFilter);
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
        exportWalks: async function () {
            this.isExportLoading = true;
            const response = await WalkAPI.export(this.exportCtx);
            this.forceFileDownload(response, this.getFileName());
            this.isExportLoading = false;
        },
        getFileName() {
            let title = `streetworkrunden_export.csv`;

            if (this.filter.teamName.length) {
                title = `TEAM_${this.filter.teamName.join('_')}_${title}`;
            }
            if (this.filter.guestNames.length) {
                title = `WEITERE_TEILNEHMENDE_${this.filter.guestNames.join('_')}_${title}`;
            }
            if ('null' !== this.filter.isResubmission) {
                title = `WV_DB_${this.filter.isResubmission ? 'ja' : 'nein'}_${title}`;
            }
            if ('null' !== this.filter.isUnfinished) {
                title = `BEENDET_${!this.filter.isUnfinished ? 'nein' : 'ja'}_${title}`;
            }
            if (this.filter.name) {
                title = `NAME_${this.filter.name}_${title}`;
            }

            const startDate = dayjs(this.filter?.startTime[0]);
            const endDate = dayjs(this.filter?.startTime[1]);
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
