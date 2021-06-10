<template>
    <div class="p-2">
        <date-range-picker
            v-if="false"
            ref="picker"
            :locale-data="{ firstDay: 1, format: 'dd.mm.yyyy HH:mm:ss' }"
            showWeekNumbers
            showDropdowns
            autoApply
            v-model="dateRange"
            linkedCalendars
        >
            <template v-slot:input="picker" style="min-width: 350px;">
                {{ picker.startDate | date }} - {{ picker.endDate | date }}
            </template>
        </date-range-picker>
        <b-button
            size="sm"
            block
            :disabled="isLoading"
            @click="exportWalks"
        >
            Alle Runden als .csv-Datei exportieren
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
        return {
            isLoading: false,
            dateRange: false,
        };
    },
    computed: {
        currentUser() {
            return this.$store.getters['security/currentUser'];
        },
    },
    watch: {},
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
            const response = await WalkAPI.export({
                // startTimeFrom: '20.12.2020',
                // startTimeTo: '20.12.2021',
                client: this.currentUser.client
            });
            const title = `${dayjs().format('YYYYMMDD_HHmmss')}_streetworkrunden_export.csv`;
            this.forceFileDownload(response, title);
            this.isLoading = false;
        },
    },
};
</script>

<style scoped>

</style>
