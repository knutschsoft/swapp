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
                            class="mt-2"
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
        <v-data-table
            striped
            dense
            class="mb-0"
            :headers="headers"
            :items="serverItems"
            :loading="isLoading"
            :search="search"
            item-value="name"
            fixed-header
            hide-default-footer
            disable-pagination
            :no-data-text="noItemsText"
            :loading-text="loadingText"
            multi-sort
            hover
            density="compact"
            @update:options="loadItems"
            :options.sync="deprecatedOptions"
            :no-results-text="noItemsText"
        >
            <template v-slot:item.user="{item}">
                <span
                    :class="{ 'text-muted': !item.user.isEnabled }"
                    :title="!item.user.isEnabled ? 'Account ist aktuell nicht aktiviert.' : ''"
                >
                    {{ item.user.username }}
                    <v-icon
                        v-if="!item.user.isEnabled"
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
                    {{ clientFormatter(item.user.client) }}
                </small>
            </template>
            <template v-for="slot in valueSlots" v-slot:[`item.${slot.value}`]="{item}">
                <v-icon
                    v-if="isLoadingEntries.includes(slot.value)"
                    class="text-muted"
                    spin
                    size="18"
                >
                    mdi-loading
                </v-icon>
                <v-icon
                    v-else-if="item[slot.value]"
                    title="Benutzer hat in diesem Monat an mindestens einer Runde teilgenommen."
                    color="info"
                    size="18"
                >
                    mdi-account-check-outline
                </v-icon>
            </template>

            <template #body.append="{headers}">
                <tr>
                    <td v-for="header in headers" :key="header.value" class="text-center">
                        <strong v-if="header.value === 'user'">Summe</strong>
                        <span v-else>{{ getSumOfColumn(header.value) }}</span>
                    </td>
                </tr>
            </template>

<!--            <template #foot()="data">-->
<!--                {{ getSumOfColumn(data.column)}}-->
<!--            </template>-->
        </v-data-table>
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
import UserAPI from '../../api/user';
import dayjs from 'dayjs';
import dateRangePicker from '../../utils/date-range-picker'
import { useAuthStore, useClientStore, useGeneralStore, useUserStore } from '../../stores';
import { ClientSelect } from "@/js/components/Common";
import {WalkSystemicAnswerField} from "@/js/components/Common/Walk";
import { loadingText, noItemsText} from "@/js/utils";

export default {
    name: 'ActiveUserList',
    components: {
        WalkSystemicAnswerField,
        ClientSelect,
        DateRangePicker,
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
            client: generalStore.clientFilter,
            loadingText,
            noItemsText,
            deprecatedOptions: {},
            search: '',
            tableOptions: [],
        };
    },
    computed: {
        isSuperAdmin() {
            return this.authStore.isSuperAdmin;
        },
        currentUser() {
            return this.authStore.currentUser;
        },
        serverItems() {
            return this.entries
                .filter(entry =>  !this.client || this.client === entry.user.client)
        },
        headers() {
            let headers = [];

            headers.push(
                {
                    value: 'user',
                    text: 'Benutzername',
                    sortable: true,
                    align: 'center',
                },
            );

            headers.push(...this.valueSlots)

            return headers;
        },
        valueSlots() {
            let headers = [];
            let start = dayjs(this.dateRange.startDate);
            let dateTo = dayjs(this.dateRange.endDate);

            while (start.isBefore(dateTo)) {
                headers.push({
                    value: this.getKeyOfDayjs(start),
                    text: `${start.month() + 1}/${start.year()}`,
                    align: 'center',
                    sortable: true,
                });
                start = start.startOf('month').add(1, 'month');
            }

            return headers;
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
            await this.loadItems();
        },
        client: async function (client) {
            this.generalStore.updateClientFilter(client);
            await this.loadItems();
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

        this.client = !this.isSuperAdmin ? '' : this.client;
        await this.loadItems();
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
            this.serverItems.forEach((serverItemsValue) => {
                if (serverItemsValue[columnKey] === true) {
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
        async loadItems() {
            let start = dayjs(this.dateRange.startDate);
            let end = dayjs(this.dateRange.endDate);
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
