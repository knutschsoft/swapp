<template>
    <div class="p-2">
        <v-row class="my-2">
            <v-col
                xs="12"
                :sm="isSuperAdmin ? 8 : 12"
                :md="isSuperAdmin ? 6 : 12"
            >
                <v-input
                    @click:append="resetDefaultDateRange"
                    @click:prepend="togglePicker"
                >
                    <template v-slot:prepend>
                        <div
                            :class="(dateRange.startDate.getTime() !== defaultDateRange.startDate.getTime() || dateRange.endDate.getTime() !== defaultDateRange.endDate.getTime()) ? 'font-weight-bold' : ''"
                        >
                            Zeitraum
                        </div>
                    </template>
                    <date-range-picker
                        ref="picker"
                        class="form-control"
                        v-model="dateRange"
                        :ranges="ranges"
                        :locale-data="locale"
                        auto-apply
                        show-dropdowns
                        opens="right"
                        :readonly="isLoadingEntries.length > 0"
                        :disabled="isLoadingEntries.length > 0"
                    />
                    <template v-slot:append>
                        <v-progress-circular
                            v-if="isLoading || isLoadingEntries.length > 0"
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
                            :color="!((dateRange.startDate.getTime() === defaultDateRange.startDate.getTime() && dateRange.endDate.getTime() === defaultDateRange.endDate.getTime()) || isLoading || isLoadingEntries.length > 0) ? 'blue darken-2' : 'secondary lighten-4'"
                            x-small
                            fab
                        >
                            <v-icon
                                color="white"
                                @click="resetDefaultDateRange"
                            >
                                mdi-filter-remove-outline
                            </v-icon>
                        </v-btn>
                    </template>
                </v-input>
            </v-col>
            <v-col
                v-if="isSuperAdmin"
                xs="12"
                sm="4"
                md="6"
                class="mb-1"
            >
                <client-select
                    v-model="client"
                    :is-loading="isLoading"
                    :disabled="isLoading"
                />
            </v-col>
        </v-row>
        <b-table
            v-show="!isLoading"
            :items="tableData"
            :fields="fields"
            small
            striped
            hover
            outlined
            foot-clone
            class="mb-0"
            :stacked="isTableStacked"
        >
            <template v-slot:cell()="row">
                <template v-if="row.field.key === 'user'">
                    <span
                        :class="{ 'text-muted': !row.value.isEnabled }"
                        :title="!row.value.isEnabled ? 'Account ist aktuell nicht aktiviert.' : ''"
                    >
                        {{ row.value.username }}
                        <v-icon
                            v-if="!row.value.isEnabled"
                            class="text-muted"
                            size="16"
                        >
                            mdi-account-off
                        </v-icon>
                    </span>
                    <small
                        v-if="isSuperAdmin && !client"
                        class="text-muted or-text-step"
                    >
                        {{ clientFormatter(row.value.client) }}
                    </small>
                </template>
                <mdicon
                    v-else-if="isLoadingEntries.includes(row.field.key)"
                    name="loading"
                    class="text-muted"
                    spin
                    size="18"
                />
                <v-icon
                    v-else-if="row.value"
                    title="Benutzer hat in diesem Monat an mindestens einer Runde teilgenommen."
                    color="info"
                    size="18"
                >
                    mdi-account-check-outline
                </v-icon>
            </template>

            <template #foot(user)="data">
                Summe
            </template>

            <template #foot()="data">
                {{ getSumOfColumn(data.column)}}
            </template>
        </b-table>
        <v-alert
            class="w-100 text-muted mt-2 mb-0"
        >
            <b>
                Hinweis:
            </b>
            <ul class="mb-0">
                <li>
                    Ein Nutzer gilt als aktiv, wenn er in einem Monat an mindestens einer Runde teilgenommen hat.
                </li>
            </ul>
        </v-alert>
    </div>
</template>

<script>
'use strict';
import DateRangePicker from 'vue2-daterange-picker';
import 'vue2-daterange-picker/dist/vue2-daterange-picker.css';
import MyInputGroupAppend from '../Common/MyInputGroupAppend.vue';
import UserAPI from '../../api/user';
import dayjs from 'dayjs';
import dateRangePicker from '../../utils/date-range-picker'
import { useAuthStore, useClientStore, useGeneralStore, useUserStore } from '../../stores';
import { ClientSelect } from "@/js/components/Common";
import {WalkSystemicAnswerField} from "@/js/components/Common/Walk";

export default {
    name: 'ActiveUserList',
    components: {
        WalkSystemicAnswerField,
        ClientSelect,
        DateRangePicker,
        MyInputGroupAppend,
    },
    data: function () {
        const generalStore = useGeneralStore();

        return {
            authStore: useAuthStore(),
            clientStore: useClientStore(),
            generalStore: generalStore,
            userStore: useUserStore(),
            isLoadingEntries: [],
            locale: dateRangePicker.locale,
            defaultDateRange: {
                startDate: generalStore.defaultActiveUsersDateRange.startDate.toDate(),
                endDate: generalStore.defaultActiveUsersDateRange.endDate.toDate(),
            },
            dateRange: {
                startDate: new Date(generalStore.activeUsersDateRange.startDate),
                endDate: new Date(generalStore.activeUsersDateRange.endDate),
            },
            ranges: dateRangePicker.ranges,
            entries: [],
            client: '',
        };
    },
    computed: {
        isTableStacked() {
            if (this.fields.length > 11) {
                return 'lg';
            } else if (this.fields.length < 4) {
                return 'xs';
            }

            return 'sm';
        },
        isSuperAdmin() {
            return this.authStore.isSuperAdmin;
        },
        currentUser() {
            return this.authStore.currentUser;
        },
        tableData() {
            return this.entries.filter(entry =>  !this.client || this.client === entry.user.client);
        },
        fields() {
            let fields = [];
            let start = dayjs(this.dateRange.startDate);
            let dateTo = dayjs(this.dateRange.endDate);

            fields.push(
                {
                    key: 'user',
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
            return this.userStore.getUsers.slice().sort((userA, userB) => {
                if (userA.isEnabled === userB.isEnabled) {
                    if (userA.username.toUpperCase() < userB.username.toUpperCase()) {
                        return -1;
                    }
                } else if (userA.isEnabled && !userB.isEnabled) {
                    return -1;
                } else if (!userA.isEnabled && userB.isEnabled) {
                    return 1;
                }
            });
        },
        isLoading() {
            return this.userStore.isLoading;
        },
    },
    watch: {
        dateRange: async function (dateRange) {
            this.generalStore.updateActiveUsersDateRange(dateRange);
            await this.updateEntries();
        },
        client: async function () {
            await this.updateEntries();
        },
    },
    async created() {
        await this.userStore.fetchUsers();

        this.users.forEach((user) => {
            let item = {
                user: user,
            };
            this.entries.push(item);
        });

        this.client = this.isSuperAdmin ? '' : this.currentUser.client;
        await this.updateEntries();
    },
    methods: {
        clientFormatter(clientIri) {
            return this.clientStore.getClientByIri(clientIri)?.name;
        },
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
                        'walks.timeRange': `${start.toISOString()}..${start.endOf('month').toISOString()}`,
                        'client': this.client,
                    },
                });

                users.data['hydra:member'].forEach((user) => {
                    this.entries.forEach((oldObject, key) => {
                        if (oldObject.user['@id'] === user['@id']) {
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

<style lang="scss" scoped>
</style>
