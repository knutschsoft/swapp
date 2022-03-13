<template>
    <div class="p-2">

        <b-row>
            <b-col
                class="mb-1"
                xs="12"
                sm="12"
                md="12"
            >
                <b-input-group
                    size="sm"
                >
                    <b-input-group-prepend
                        @click.stop="togglePicker"
                    >
                        <b-input-group-text
                            :class="(dateRange.startDate.getTime() !== defaultDateRange.startDate.getTime() || dateRange.endDate.getTime() !== defaultDateRange.endDate.getTime()) ? 'font-weight-bold' : ''"
                        >
                            Zeitraum
                        </b-input-group-text>
                    </b-input-group-prepend>
                    <date-range-picker
                        ref="picker"
                        class="form-control"
                        v-model="dateRange"
                        :ranges="ranges"
                        :locale-data="locale"
                        showWeekNumbers
                        showDropdowns
                        auto-apply
                        opens="right"
                        linkedCalendars
                    />
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
                            @click="resetDefaultDateRange"
                            :disabled="(dateRange.startDate.getTime() === defaultDateRange.startDate.getTime() && dateRange.endDate.getTime() === defaultDateRange.endDate.getTime()) || isLoading"
                        >
                            <mdicon name="CloseCircleOutline" size="18" />
                        </b-button>
                    </b-input-group-append>
                </b-input-group>
            </b-col>
        </b-row>
        <b-button
            size="sm"
            block
            :disabled="isLoading"
            @click="exportWalks"
        >
            Runden im gewählten Zeitraum als .csv-Datei exportieren
            <mdicon
                :name="isLoading ? 'Loading' : 'Download'"
                :spin="isLoading"
            />
        </b-button>
    </div>
</template>

<script>
'use strict';
import WalkAPI from '../../api/walk';
import dayjs from 'dayjs';
import DateRangePicker from 'vue2-daterange-picker'
//you need to import the CSS manually
import 'vue2-daterange-picker/dist/vue2-daterange-picker.css'

export default {
    name: 'WalkExport',
    components: {
        DateRangePicker,
    },
    props: {},
    data: function () {
        let now = dayjs();
        let defaultStartDate = now.startOf('year').toDate();
        let defaultEndDate = now.endOf('year').toDate();
        const storageKeyWalkExportStartDate = 'walk-export-startDate';
        const storageKeyWalkExportEndDate = 'walk-export-endDate';
        return {
            isLoading: false,
            storageKeyWalkExportStartDate,
            storageKeyWalkExportEndDate,
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
            dateRange: {
                startDate: new Date(this.$localStorage.get(storageKeyWalkExportStartDate, defaultStartDate)),
                endDate: new Date(this.$localStorage.get(storageKeyWalkExportEndDate, defaultEndDate)),
            },
            ranges: {
                'Dieser Monat': [dayjs().startOf('month').toDate(), dayjs().endOf('month').toDate()],
                'Letzter Monat': [dayjs().subtract(1, 'month').startOf('month').toDate(), dayjs().subtract(1, 'month').endOf('month').toDate()],
                'Letzte 6 Monate': [dayjs().subtract(5, 'month').startOf('month').toDate(), dayjs().endOf('month').toDate()],
                'Dieses Jahr': [dayjs().startOf('year').toDate(), dayjs().endOf('year').toDate()],
                'Letztes Jahr': [dayjs().subtract(1, 'year').startOf('year').toDate(), dayjs().subtract(1, 'year').endOf('year').toDate()],
                'Vorletztes Jahr': [dayjs().subtract(2, 'year').startOf('year').toDate(), dayjs().subtract(2, 'year').endOf('year').toDate()],
            },
        };
    },
    computed: {
        currentUser() {
            return this.$store.getters['security/currentUser'];
        },
    },
    watch: {
        dateRange: async function (dateRange) {
            this.$localStorage.set(this.storageKeyWalkExportStartDate, dateRange.startDate);
            this.$localStorage.set(this.storageKeyWalkExportEndDate, dateRange.endDate);
        },
    },
    mounted() {
    },
    methods: {
        forceFileDownload(response, title) {
            const url = window.URL.createObjectURL(new Blob([response.data]));
            const link = document.createElement('a');
            link.href = url;
            link.setAttribute('download', title);
            document.body.appendChild(link);
            link.click();
        },
        exportWalks: async function () {
            this.isLoading = true;
            const start = dayjs(this.dateRange.startDate);
            const end = dayjs(this.dateRange.endDate);
            const response = await WalkAPI.export({
                startTimeFrom: start.toISOString(),
                startTimeTo: end.toISOString(),
                client: this.currentUser.client,
            });
            const title = `${start.format('YYYYMMDD')}-${end.format('YYYYMMDD')}_streetworkrunden_export.csv`;
            this.forceFileDownload(response, title);
            this.isLoading = false;
        },
        resetDefaultDateRange() {
            this.dateRange = this.defaultDateRange;
        },
        togglePicker() {
            this.$refs.picker.togglePicker(!this.$refs.picker.open);
        },
    },
};
</script>

<style scoped>

</style>
