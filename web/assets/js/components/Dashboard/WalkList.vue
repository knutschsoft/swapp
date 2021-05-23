<template>
    <div class="p-2">
        <b-row
            class="mx-0"
        >
            <b-col sm="5" md="6" class="my-1">
                <b-form-group
                    label="pro Seite"
                    label-cols-sm="6"
                    label-cols-md="4"
                    label-cols-lg="3"
                    label-align-sm="right"
                    label-size="sm"
                    label-for="perPageSelect"
                    class="mb-0"
                >
                    <b-form-select
                        v-model="perPage"
                        id="perPageSelect"
                        size="sm"
                        :options="pageOptions"
                        @change="handlePerPageChange"
                    ></b-form-select>
                </b-form-group>
            </b-col>

            <b-col sm="7" md="6" class="my-1">
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
                            Runde Ansehen
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
import formatter from '../../utils/formatter.js';

export default {
    name: 'WalkList',
    components: {},
    props: {},
    data: function () {
        return {
            fields: [
                { key: 'name', label: 'Name', sortable: true, sortDirection: 'desc', class: 'text-center align-middle' },
                { key: 'rating', label: 'Bewertung', sortable: true, class: 'text-center align-middle', formatter: (value) => {return formatter.formatRating(value);} },
                { key: 'startTime', label: 'Beginn', sortable: true, class: 'text-center align-middle', formatter: (value) => {return this.formatStartDate(value);} },
                { key: 'endTime', label: 'Ende', sortable: false, class: 'text-center align-middle', formatter: (value, key, item) => {return this.formatEndDate(value, item.startTime);} },
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
                { key: 'actions', label: 'Aktionen', class: 'text-center p-y-0' },
            ],
            totalRows: 10000,
            currentPage: null,
            perPage: null,
            pageOptions: [5, 10, 25, 50, 100],
            sortBy: 'startTime',
            sortDesc: true,
            sortDirection: 'desc',
            filter: null,
            storagePerPageId: 'abgeschlossene-runden-per-page',
            storageCurrentPageId: 'abgeschlossene-runden-current-page',
            storageWalksId: 'abgeschlossene-runden-walks',
        };
    },
    computed: {
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
    },
    async created() {
        this.perPage = this.$localStorage.get(this.storagePerPageId, 10);
        this.currentPage = this.$localStorage.get(this.storageCurrentPageId, 1);
    },
    methods: {
        formatStartDate: function (dateString) {
            let date = new Date(dateString);
            return date.toLocaleDateString('de-DE', { weekday: 'short', hour: '2-digit', minute: '2-digit', year: 'numeric', month: '2-digit', day: '2-digit' });
        },
        formatEndDate: function (dateString, startDateString) {
            let date = new Date(dateString);
            let startDate = new Date(startDateString);
            if (startDate.getDay() === date.getDay()) {
                return date.toLocaleTimeString('de-DE', { hour: '2-digit', minute: '2-digit' });
            }
            return this.formatStartDate(dateString);
        },
        async itemProvider(ctx) {
            let walks = await this.$store.dispatch('walk/find', ctx);
            this.totalRows = this.totalWalks;
            this.$localStorage.set(this.storageWalksId, walks);

            return walks;
        },
        handleCurrentPageChange(value) {
            this.$localStorage.set(this.storageCurrentPageId, value);
        },
        handlePerPageChange(value) {
            this.$localStorage.set(this.storagePerPageId, value);
        },
    },
};
</script>

<style scoped>
</style>
