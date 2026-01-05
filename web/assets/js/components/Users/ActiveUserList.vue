<template>
    <div class="pa-2">
        <v-row>
            <v-col
                xs="12"
                :sm="isSuperAdmin ? 8 : 12"
                :md="isSuperAdmin ? 6 : 12"
            >
                <MonthRangePicker
                    v-model="dateRange"
                    data-test="termin-am-filter"
                    :is-loading="isLoadingEntries.length > 0"
                    :default-date-range="defaultDateRange"
                />
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
        <v-data-table-server
            striped
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
            items-length=""
            multi-sort
            hover
            density="compact"
            @update:options="loadItems"
            :options.sync="deprecatedOptions"
            :no-results-text="noItemsText"
        >
            <template #item.user="{item}">
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
                    class="text-muted"
                >
                    {{ clientFormatter(item.user.client) }}
                </small>
            </template>
            <template v-for="slot in valueSlots" #[`item.${slot.key}`]="{item}">
                <v-icon
                    v-if="isLoadingEntries.includes(slot.key)"
                    class="text-muted"
                    spin
                    size="18"
                >
                    mdi-loading
                </v-icon>
                <v-icon
                    v-else-if="item[slot.key]"
                    title="Benutzer hat in diesem Monat an mindestens einer Runde teilgenommen."
                    color="info"
                    size="18"
                >
                    mdi-account-check-outline
                </v-icon>
            </template>

            <template #body.append="{headers}">
                <tr>
                    <td v-for="header in headers[0]" :key="header.key" class="text-center">
                        <strong v-if="header.key === 'user'">Summe</strong>
                        <span v-else>{{ getSumOfColumn(header.key) }}</span>
                    </td>
                </tr>
            </template>
        </v-data-table-server>
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
import UserAPI from '../../api/user';
import dayjs from 'dayjs';
import { useAuthStore, useClientStore, useGeneralStore, useUserStore } from '../../stores';
import { ClientSelect, MonthRangePicker } from "@/js/components/Common";
import {WalkSystemicAnswerField} from "@/js/components/Common/Walk";
import { loadingText, noItemsText} from "@/js/utils";

export default {
    name: 'ActiveUserList',
    components: {
        MonthRangePicker,
        WalkSystemicAnswerField,
        ClientSelect,
    },
    data: function () {
        const generalStore = useGeneralStore();

        return {
            authStore: useAuthStore(),
            clientStore: useClientStore(),
            generalStore: generalStore,
            userStore: useUserStore(),
            isLoadingEntries: [],
            defaultDateRange: generalStore.defaultActiveUsersDateRange,
            dateRange: generalStore.activeUsersDateRange,
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
                    key: 'user',
                    title: 'Benutzername',
                    sortable: true,
                    align: 'center',
                },
            );

            headers.push(...this.valueSlots)

            return headers;
        },
        valueSlots() {
            let headers = [];

            if (this.dateRange[0] !== null && this.dateRange[1] === null) {
                return headers
            }
            let start = dayjs().month(this.dateRange[0].month).year(this.dateRange[0].year).startOf('month');
            let dateTo = dayjs().month(this.dateRange[1].month).year(this.dateRange[1].year).endOf('month');

            while (start.isBefore(dateTo)) {
                headers.push({
                    key: this.getKeyOfDayjs(start),
                    title: `${start.month() + 1}/${start.year()}`,
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
        async loadItems() {
            if (this.dateRange[0] !== null && this.dateRange[1] === null) {
                return
            }
            let start = dayjs().month(this.dateRange[0].month).year(this.dateRange[0].year).startOf('month');
            let end = dayjs().month(this.dateRange[1].month).year(this.dateRange[1].year).endOf('month');

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

                users.data['member'].forEach((user) => {
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
