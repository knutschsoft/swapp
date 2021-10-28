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
                        <b-input-group-text>
                            Zeitraum
                        </b-input-group-text>
                    </b-input-group-prepend>
                    <date-range-picker
                        ref="picker"
                        class="form-control form-control-m"
                        v-model="dateRange"
                        :ranges="ranges"
                        :locale-data="locale"
                        auto-apply
                        show-dropdowns
                        opens="right"
                        :readonly="isLoadingEntries.length > 0"
                        :disabled="isLoadingEntries.length > 0"
                    >
                    </date-range-picker>
                    <b-input-group-append
                        @click.stop="togglePicker"
                    >
                        <b-input-group-text>
                            <mdicon
                                v-if="isLoading || isLoadingEntries.length > 0"
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
                            :disabled="(dateRange.startDate.getTime() === defaultDateRange.startDate.getTime() && dateRange.endDate.getTime() === defaultDateRange.endDate.getTime()) || isLoading || isLoadingEntries.length > 0"
                        >
                            <mdicon name="CloseCircleOutline" size="18" />
                        </b-button>
                    </b-input-group-append>
                </b-input-group>
            </b-col>
        </b-row>
        <b-table
            v-show="!isLoading"
            :items="tableData"
            :fields="fields"
            small
            striped
            foot-clone
            class="mb-0"
            :stacked="this.fields.length > 11 ? 'lg' : 'sm'"
        >
            <template v-slot:cell()="row">
                <template v-if="row.field.key === 'username'">
                    {{ row.value }}
                </template>
                <mdicon
                    v-else-if="isLoadingEntries.includes(row.field.key)"
                    name="loading"
                    class="text-muted"
                    spin
                    size="18"
                />
                <mdicon
                    v-else-if="row.value"
                    name="account-check-outline"
                    title="Benutzer hat in diesem Monat an mindestens einer Runde teilgenommen."
                    class="text-info"
                    size="18"
                />
            </template>

            <template #foot(username)="data">
                Summe
            </template>

            <template #foot()="data">
                {{ getSumOfColumn(data.column)}}
            </template>
        </b-table>
    </div>
</template>

<script>
'use strict';
import DateRangePicker from 'vue2-daterange-picker';
import 'vue2-daterange-picker/dist/vue2-daterange-picker.css';
import UserAPI from '../../api/user';
import dayjs from 'dayjs';

export default {
    name: 'ActiveUserList',
    components: {
        DateRangePicker,
    },
    data: function () {
        let now = dayjs();
        let defaultStartDate = now.subtract(5, 'month').startOf('month').toDate();
        let defaultEndDate = now.endOf('month').toDate();
        return {
            isLoadingEntries: [],
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
                startDate: new Date(this.$localStorage.get('aktive-benutzer-startDate', defaultStartDate)),
                endDate: new Date(this.$localStorage.get('aktive-benutzer-endDate', defaultEndDate)),
            },
            ranges: {
                'Dieser Monat': [dayjs().startOf('month').toDate(), dayjs().endOf('month').toDate()],
                'Letzter Monat': [dayjs().subtract(1, 'month').startOf('month').toDate(), dayjs().subtract(1, 'month').endOf('month').toDate()],
                'Letzte 6 Monate': [dayjs().subtract(5, 'month').startOf('month').toDate(), dayjs().endOf('month').toDate()],
                'Dieses Jahr': [dayjs().startOf('year').toDate(), dayjs().endOf('year').toDate()],
                // 'Letztes Jahr': [dayjs().subtract(1, 'year').startOf('year').toDate(), dayjs().subtract(1, 'year').endOf('year').toDate()],
                // 'Vorletztes Jahr': [dayjs().subtract(2, 'year').startOf('year').toDate(), dayjs().subtract(2, 'year').endOf('year').toDate()],
            },
            dateFrom: now.subtract(6, 'month').startOf('month'),
            dateTo: now.add(1, 'month').endOf('month'),
            entries: [],
        };
    },
    computed: {
        tableData() {
            return this.entries;
        },
        fields() {
            let fields = [];
            let start = dayjs(this.dateRange.startDate);
            let dateTo = dayjs(this.dateRange.endDate);

            fields.push(
                {
                    key: 'username',
                    label: 'Benutzername',
                    sortable: true,
                    class: 'text-center',
                },
            );
            while (start.isBefore(dateTo)) {
                fields.push({
                    key: this.getKeyOfDayjs(start),
                    label: `${start.month() + 1}/${start.year()}`,
                    sortable: true,
                    class: 'text-center',
                });
                start = start.startOf('month').add(1, 'month');
            }

            return fields;
        },
        users() {
            return this.$store.getters['user/users'];
        },
        isLoading() {
            return this.$store.getters['user/isLoading'];
        },
    },
    watch: {
        dateRange: async function (dateRange) {
            this.$localStorage.set('aktive-benutzer-startDate', dateRange.startDate);
            this.$localStorage.set('aktive-benutzer-endDate', dateRange.endDate);
            await this.updateEntries();
        }
    },
    async created() {
        await this.$store.dispatch('user/findAll');

        this.users.forEach((user) => {
            let item = {
                username: user.username,
            };
            this.entries.push(item);
        });

        await this.updateEntries();
    },
    methods: {
        getKeyOfDayjs(date) {
            return `${date.month()}-${date.year()}`;
        },
        getSumOfColumn(columnKey) {
            let sum = 0;
            this.tableData.forEach((tableDataValue) => {
                if (tableDataValue[columnKey] === true) {
                    sum++;
                }
            });

            return sum;
        },
        resetDefaultDateRange() {
            this.dateRange = this.defaultDateRange;
        },
        togglePicker() {
            this.$refs.picker.togglePicker(!this.$refs.picker.open);
        },
        async updateEntries() {
            let start = dayjs(this.dateRange.startDate);
            const end = dayjs(this.dateRange.endDate);
            while (start.isBefore(end)) {
                let key = this.getKeyOfDayjs(start);
                this.isLoadingEntries.push(key);
                let users = await UserAPI.findAll({
                    page: 1,
                    itemsPerPage: 1000,
                    filter: {
                        'walks.timeRange': `${start.toISOString()}..${start.endOf('month').toISOString()}`
                    }
                });

                users.data['hydra:member'].forEach((user) => {
                    this.entries.forEach((oldObject, key) => {
                        if (oldObject['username'] === user.username) {
                            oldObject[this.getKeyOfDayjs(start)] = true;
                            // see: https://vuejs.org/v2/guide/reactivity.html#For-Arrays
                            this.entries.splice(key, 1, oldObject);
                        }
                    });
                });
                this.isLoadingEntries = this.isLoadingEntries.filter(function(value){
                    return value !== key;
                });
                start = start.add(1, 'month');
            }
        },
    },
};
</script>

<style lang="scss">
.vue-daterange-picker {
    .form-control {
        padding: 0;
        border: 0;
        height: auto;
        width: 100%;
    }
}
</style>
