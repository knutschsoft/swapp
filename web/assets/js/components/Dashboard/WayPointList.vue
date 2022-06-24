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
                        </b-input-group-text>
                    </b-input-group-prepend>
                    <b-form-group
                        v-slot="{ ariaDescribedby }"
                        class="pl-2 border-top border-bottom mb-0"
                        @change="handleFilterChange"
                    >
                        <div class="d-flex flex-wrap">
                            <b-form-checkbox
                                v-for="tag in tags"
                                v-model="filter.wayPointTags"
                                :key="tag['@id']"
                                :value="tag['@id']"
                                :aria-describedby="ariaDescribedby"
                                name="tags"
                                class="d-flex align-items-center flex-tags"
                            >
                                {{ tag.name }}
                            </b-form-checkbox>
                        </div>
                    </b-form-group>
                    <b-input-group-append>
                        <b-button
                            @click="unsetFilterWayPointTags"
                            :disabled="!filter.wayPointTags.length"
                        >
                            <mdicon name="CloseCircleOutline" size="18" />
                        </b-button>
                    </b-input-group-append>
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
                        @update="handleFilterChange"
                    />
                    <b-input-group-append>
                        <b-button
                            @click="unsetFilterNote"
                            :disabled="!filter.note"
                        >
                            <mdicon name="CloseCircleOutline" size="18"/>
                        </b-button>
                    </b-input-group-append>
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
                        @update="handleFilterChange"
                    />
                    <b-input-group-append>
                        <b-button
                            @click="unsetFilterOneOnOneInterview"
                            :disabled="!filter.oneOnOneInterview"
                        >
                            <mdicon name="CloseCircleOutline" size="18"/>
                        </b-button>
                    </b-input-group-append>
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
                        @update="handleFilterChange"
                    />
                    <b-input-group-append>
                        <b-button
                            @click="unsetFilterLocationName"
                            :disabled="!filter.locationName"
                        >
                            <mdicon name="CloseCircleOutline" size="18"/>
                        </b-button>
                    </b-input-group-append>
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
                        @update="handleFilterChange"
                    ></b-form-input>
                    <datalist id="team-name-for-wayPoint-list">
                        <option v-for="teamName in teamNames">{{ teamName }}</option>
                    </datalist>
                    <b-input-group-append>
                        <b-button
                            @click="unsetFilterTeamName"
                            :disabled="!filter.teamName"
                        >
                            <mdicon name="CloseCircleOutline" size="18" />
                        </b-button>
                    </b-input-group-append>
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
                    <b-input-group-append>
                        <b-button
                            @click="unsetFilterVisitedAt"
                            :disabled="(filter.visitedAt.startDate === defaultDateRange.startDate && filter.visitedAt.endDate === defaultDateRange.endDate) || isLoading"
                        >
                            <mdicon name="CloseCircleOutline" size="18" />
                        </b-button>
                    </b-input-group-append>
                </b-input-group>
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
                    :id="`tooltip-target-note-${ row.item.id }`"
                    class="mw-25"
                >
                    <nl2br
                        tag="div"
                        :text="row.item.note.trim()"
                        class-name="text-truncate"
                    />
                </div>
                <b-tooltip :target="`tooltip-target-note-${ row.item.id }`" triggers="hover click">
                    <nl2br
                        tag="div"
                        :text="row.item.note.trim()"
                    />
                </b-tooltip>
            </template>
            <template v-slot:cell(oneOnOneInterview)="row">
                <div
                    :id="`tooltip-target-oneOnOneInterview-${ row.item.id }`"
                    class="mw-25"
                >
                    <nl2br
                        tag="div"
                        :text="row.item.oneOnOneInterview.trim()"
                        class-name="text-truncate"
                    />
                </div>
                <b-tooltip :target="`tooltip-target-oneOnOneInterview-${ row.item.id }`" triggers="hover click">
                    <nl2br
                        tag="div"
                        :text="row.item.oneOnOneInterview.trim()"
                    />
                </b-tooltip>
            </template>
            <template v-slot:cell(actions)="row">
                <div class="d-flex justify-content-around">
                    <router-link
                        :to="{name: 'WayPointDetail', params: { wayPointId: row.item.id, walkId: getWalkByIri(row.item.walk).id }}"
                        :data-test="`button-wegpunkt-ansehen-${ row.item.lcoationName }`"
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
import dayjs from 'dayjs';
import WayPointAPI from '../../api/wayPoint';
import WalkAPI from '../../api/walk.js';

export default {
    name: 'WayPointList',
    components: {
        DateRangePicker,
    },
    props: {},
    data: function () {
        let defaultStartDate = null;
        let defaultEndDate = null;

        let filter = this.$localStorage.get('alle-wegpunkte-filter', {
            wayPointTags: [],
            locationName: '',
            note: '',
            teamName: '',
            oneOnOneInterview: '',
            visitedAt: {
                startDate: defaultStartDate,
                endDate: defaultEndDate,
            },
        });
        if (filter.visitedAt.startDate && filter.visitedAt.endDate) {
            filter.visitedAt.startDate = new Date(filter.visitedAt.startDate);
            filter.visitedAt.endDate = new Date(filter.visitedAt.endDate);
        }

        return {
            isExportLoading: false,
            exportCtx: null,
            locale: {
                direction: 'ltr',
                format: 'dd.mm.yyyy',
                separator: ' - ',
                applyLabel: 'Übernehmen',
                cancelLabel: 'Abbrechen',
                weekLabel: 'W',
                customRangeLabel: 'Custom Range',
                daysOfWeek: ['So', 'Mo', 'Di', 'Mi', 'Do', 'Fr', 'Sa'],
                monthNames: ['Jan', 'Feb', 'Mär', 'Apr', 'Mai', 'Jun', 'Jul', 'Aug', 'Sep', 'Okt', 'Nov', 'Dez'],
                firstDay: 1
            },
            defaultDateRange: {
                startDate: defaultStartDate,
                endDate: defaultEndDate,
            },
            ranges: {
                'Dieser Monat': [dayjs().startOf('month').toDate(), dayjs().endOf('month').toDate()],
                'Letzter Monat': [dayjs().subtract(1, 'month').startOf('month').toDate(), dayjs().subtract(1, 'month').endOf('month').toDate()],
                'Letzte 6 Monate': [dayjs().subtract(5, 'month').startOf('month').toDate(), dayjs().endOf('month').toDate()],
                'Dieses Jahr': [dayjs().startOf('year').toDate(), dayjs().endOf('year').toDate()],
                // 'Letztes Jahr': [dayjs().subtract(1, 'year').startOf('year').toDate(), dayjs().subtract(1, 'year').endOf('year').toDate()],
                // 'Vorletztes Jahr': [dayjs().subtract(2, 'year').startOf('year').toDate(), dayjs().subtract(2, 'year').endOf('year').toDate()],
            },
            fields: [
                { key: 'locationName', label: 'Ort', sortable: true, sortDirection: 'desc', class: 'text-center align-middle' },
                { key: 'malesCount', label: 'Männer', sortable: false, sortDirection: 'desc', class: 'text-center align-middle' },
                { key: 'femalesCount', label: 'Frauen', sortable: false, sortDirection: 'desc', class: 'text-center align-middle' },
                { key: 'queerCount', label: 'Andere', sortable: false, sortDirection: 'desc', class: 'text-center align-middle' },
                { key: 'note', label: 'Beobachtung', sortable: true, class: 'text-left align-middle' },
                { key: 'oneOnOneInterview', label: 'Einzelgespräch', sortable: true, class: 'text-left align-middle' },
                { key: 'wayPointTags', label: 'Tags', sortable: false, class: 'text-center align-middle', formatter: (value) => {return this.formatTags(value);} },
                {
                    key: 'walk.teamName', label: 'Team', sortable: true, class: 'text-center align-middle',
                    formatter: (value, key, item) => {
                        let walk = this.getWalkByIri(item.walk);

                        return walk.teamName;
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
                        let walk = this.getWalkByIri(item.walk);

                        return walk.name;
                    },
                },
                { key: 'actions', label: 'Aktionen', class: 'text-center p-y-0' },
            ],
            allTeamNames: [],
            totalRows: 0,
            currentPage: this.$localStorage.get('alle-wegpunkte-current-page', 1),
            perPage: this.$localStorage.get('alle-wegpunkte-per-page', 5),
            pageOptions: [5, 10, 25, 50, 100],
            sortBy: 'walk.startTime',
            sortDesc: true,
            sortDirection: 'desc',
            filter: filter,
            storagePerPageId: 'alle-wegpunkte-per-page',
            storageCurrentPageId: 'alle-wegpunkte-current-page',
            storageFilterId: 'alle-wegpunkte-filter',
            storageWayPointsId: 'alle-wegpunkte-wayPoints',
        };
    },
    computed: {
        teamNames() {
            const filterTeamName = this.filter.teamName ? this.filter.teamName.toLowerCase() : '';
            return this.allTeamNames.filter((teamName) => {
                return teamName.teamName.toLowerCase().startsWith(filterTeamName);
            }).map((teamName) => teamName.teamName);
        },
        wayPoints() {
            return this.$store.getters['wayPoint/wayPoints'];
        },
        tags() {
            return this.$store.getters['tag/tags'].slice(0).sort((tagA, tagB) => tagA.name > tagB.name ? 1 : -1);
        },
        isLoading() {
            return this.$store.getters['wayPoint/isLoading'];
        },
    },
    async mounted() {
        this.$store.dispatch('tag/findAll');
        const allTeamNames = await WalkAPI.findAllTeamNames();
        this.allTeamNames = allTeamNames.data['hydra:member'];
    },
    methods: {
        getTagByIri(iri) {
            return this.$store.getters['tag/getTagByIri'](iri);
        },
        getWalkByIri(iri) {
            const id = iri.replace('/api/walks/', '');

            return this.$store.getters['walk/getWalkById'](id);
        },
        formatTags: function (iriList) {
            let formattedTags = '';
            let tagList = [];
            iriList.forEach((iri) => {
                tagList.push(this.getTagByIri(iri));
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
                        walkPromises.push(this.$store.dispatch('walk/findById', id));
                        walkPromiseIds.push(id);
                    }
                }
            });
            await Promise.all(walkPromises);

            this.totalRows = result.data['hydra:totalItems'];
            this.$localStorage.set(this.storageWayPointsId, wayPoints);
            await this.$emit('refresh-total-way-points', this.totalRows);

            return wayPoints;
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
        unsetFilterWayPointTags() {
            this.filter.wayPointTags = [];
            this.handleFilterChange();
        },
        unsetFilterLocationName() {
            this.filter.locationName = '';
            this.handleFilterChange();
        },
        unsetFilterNote() {
            this.filter.note = '';
            this.handleFilterChange();
        },
        unsetFilterOneOnOneInterview() {
            this.filter.oneOnOneInterview = '';
            this.handleFilterChange();
        },
        unsetFilterTeamName() {
            this.filter.teamName = '';
            this.handleFilterChange();
        },
        unsetFilterVisitedAt() {
            this.filter.visitedAt = this.defaultDateRange;
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
        exportWayPoints: async function () {
            this.isExportLoading = true;
            const response = await WayPointAPI.export(this.exportCtx);
            let title = `streetworkwegpunkte_export.csv`;
            if (this.filter.visitedAt.startDate && this.filter.visitedAt.endDate) {
                title = `${dayjs(this.filter.visitedAt.startDate).format('YYYYMMDD')}-${dayjs(this.filter.visitedAt.endDate).format('YYYYMMDD')}_${title}`;
            }
            this.forceFileDownload(response, title);
            this.isExportLoading = false;
        },
    },
};
</script>

<style>
.mw-25 {
    max-width: 250px;
}
</style>
