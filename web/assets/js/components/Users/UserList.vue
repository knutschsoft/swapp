<template>
    <div>
        <v-row class="p-2 mt-0 mb-1">
            <v-col
                v-if="isSuperAdmin"
                xs="12"
                sm="6"
            >
                <client-select
                    v-model="filter.client"
                    :is-loading="isLoading"
                    :disabled="isLoading"
                />
            </v-col>
            <v-col
                xs="12"
                :sm="isSuperAdmin ? 6 : 12"
            >
                <v-select
                    v-model="filter.isEnabled"
                    :items="isEnabledOptions"
                    :clearable="filter.isEnabled !== true"
                    outlined
                    :success="filter.isEnabled !== true"
                    @click:clear="filter.isEnabled = true"
                    label="Nur aktivierte?"
                    dense
                />
            </v-col>
        </v-row>
        <v-data-table
            :items="users"
            :headers="headers"
            class="mb-0"
            :loading="isLoading"
            loading-text="Lade Daten..."
            :items-per-page="itemsPerPage"
            :items-per-page-options="itemsPerPageOptions"
            :items-per-page-text="itemsPerPageText"
            :no-data-text="noItemsText"
            :no-results-text="noItemsText"
            small
            striped
        >
            <template v-slot:item.username="{item}">
                <span
                    :class="{ 'text-muted': !item.isEnabled }"
                    :title="!item.isEnabled ? 'Account ist aktuell nicht aktiviert.' : ''"
                >
                    {{ item.username }}
                    <v-icon
                        v-if="!item.isEnabled"
                        class="text-muted"
                        size="16"
                    >
                        mdi-account-off
                    </v-icon>
                </span>
            </template>
            <template v-slot:item.teams="{item}">
                <template v-if="item.teams.length === 0">-</template>
                {{ item.teams.map(team => team.name).join(', ') }}
            </template>
            <template v-slot:item.roles="{item}">
                {{ rolesFormatter(item.roles)}}
            </template>
            <template v-slot:item.isEnabled="{item}">
                <v-progress-circular
                    v-if="isLoadingToggleUserState(item['@id'])"
                    indeterminate
                    color="secondary"
                />
                <div
                    v-else
                    @click="toggleEnabled(item, item.isEnabled)"
                    :title="`Account ${ item.isEnabled ? 'de' : '' }aktivieren`"
                    class="cursor-pointer"
                >
                    <v-icon
                        v-if="item.isEnabled"
                        color="success"
                    >
                        mdi-account-check-outline
                    </v-icon>
                    <v-icon
                        v-else
                        color="info"
                    >
                        mdi-account-off-outline
                    </v-icon>
                </div>
            </template>
            <template v-slot:item.client="{item}">
                {{ clientFormatter(item.client) }}
            </template>
            <template v-slot:item.createdAt="{item}">
                {{ formatDateTime(item.createdAt) }}
            </template>
            <template v-slot:item.updatedAt="{item}">
                {{ formatDateTime(item.updatedAt) }}
            </template>
            <template v-slot:item.actions="{item}">
                <div class="d-flex justify-content-around">
                    <v-menu
                        dense
                        offset-y
                    >
                        <template v-slot:activator="{ attrs, on }">
                            <v-btn
                                color="secondary"
                                small
                                v-bind="attrs"
                                v-on="on"
                            >
                                Aktionen
                                <v-icon
                                    color="grey lighten-2"
                                    small
                                    class="ml-1"
                                >
                                    mdi-triangle-small-down
                                </v-icon>
                            </v-btn>
                        </template>
                        <v-list dense>
                            <v-list-item link>
                                <v-list-item-title
                                    @click="editUser(item)"
                                    :disabled="isLoadingToggleUserState(item['@id'])"
                                >
                                        Account bearbeiten
                                </v-list-item-title>
                            </v-list-item>
                            <v-list-item v-if="isAdmin" link>
                                <v-list-item-title
                                    @click="toggleEnabled(item, item.isEnabled)"
                                    :disabled="isLoadingToggleUserState(item['@id'])"
                                >
                                    Account {{ item.isEnabled ? 'de' : '' }}aktivieren
                                </v-list-item-title>
                            </v-list-item>
                        </v-list>
                    </v-menu>
                    <v-btn
                        v-if="isSuperAdmin && !isUserSwitched"
                        small
                        color="secondary"
                        class="flex-item d-flex align-items-center ml-2"
                        :data-test="`switch-user-${item.username}`"
                        @click="switchUser(item)"
                    >
                        <v-icon
                            color="grey lighten-2"
                            small
                            class="p-1 mr-1 cursor-pointer flex-item"
                        >
                            mdi-account-switch
                        </v-icon>
                        Nutzer wechseln
                    </v-btn>
                    <v-btn
                        v-else-if="isUserSwitched"
                        small
                        color="secondary"
                        class="flex-item d-flex align-items-center ml-2"
                        data-test="exit-switch-user"
                        @click="exitSwitchUser()"
                    >
                        <v-icon
                            small
                            class="p-1 mr-1 cursor-pointer flex-item"
                        >
                            mdi-account-switch-outline
                        </v-icon>
                        Nutzerwechsel beenden
                    </v-btn>
                </div>
            </template>
        </v-data-table>

        <b-modal
            :id="editModal.id"
            :title="editModal.title"
            size="lg"
            @hide="resetEditModal"
            title="Benutzer ändern"
            hide-footer
        >
            <user-form
                v-if="editModal.selectedUser"
                submit-button-text="Speichern"
                :initial-user="editModal.selectedUser"
                @submit="handleSubmit"
            />
        </b-modal>
    </div>
</template>

<script>
'use strict';
import UserForm from './UserForm.vue';
import MyInputGroupAppend from '../Common/MyInputGroupAppend.vue';
import {useAlertStore, useAuthStore, useClientStore, useUserStore} from '../../stores';
import {ClientSelect} from "@/js/components/Common";
import {
    formatDateTime,
    itemsPerPageOptions,
    itemsPerPageText,
    loadingText,
    noItemsText,
} from '../../utils'

export default {
    name: 'UserList',
    components: {
        ClientSelect,
        UserForm,
        MyInputGroupAppend,
    },
    data: function () {
        return {
            alertStore: useAlertStore(),
            authStore: useAuthStore(),
            clientStore: useClientStore(),
            userStore: useUserStore(),
            itemsPerPageText,
            itemsPerPageOptions,
            itemsPerPage: -1,
            loadingText,
            noItemsText,
            editModal: {
                selectedUser: {},
                id: 'edit-modal-user',
                title: '',
            },
            isEnabledOptions: [
                { value: null, text: 'egal' },
                { value: true, text: 'ja' },
                { value: false, text: 'nur deaktivierte Accounts' },
            ],
            filter: {
                client: null,
                isEnabled: true,
            },
        };
    },
    computed: {
        headers() {
            const headers = [
                {
                    value: 'username',
                    text: 'Benutzername',
                    sortable: true,
                    class: 'text-center',
                },
                {
                    value: 'teams',
                    text: 'Teams',
                    sortable: false,
                },
                {
                    value: 'email',
                    text: 'E-Mail',
                },
                {
                    value: 'roles',
                    text: 'Rollen',
                    sortable: false,
                },
                {
                    value: 'isEnabled',
                    text: 'Account aktiviert?',
                    sortable: true,
                    class: 'text-center',
                }
            ];
            if (this.isSuperAdmin) {
                headers.push({
                    value: 'client',
                    text: 'Klient',
                    sortable: true,
                    sortByFormatted: true,
                    class: !this.isSuperAdmin ? 'd-none text-center' : 'text-center',
                    formatter: this.clientFormatter,
                })
            }
            headers.push(...[
                {
                    value: 'createdAt',
                    text: 'Erstellt am',
                },
                {
                    value: 'updatedAt',
                    text: 'Geändert am',
                }
            ]);
            if (this.isSuperAdmin) {
                headers.push(...[
                    {
                        value: 'createdBy',
                        text: 'Erstellt von:',
                        sortable: false,
                    },
                    {
                        value: 'updatedBy',
                        text: 'Geändert von:',
                        sortable: false,
                    },
                ]);
            }
            headers.push({ value: 'actions', text: 'Aktionen' })

            return headers;
        },
        isUserSwitched() {
            return this.authStore.isUserSwitched;
        },
        isSuperAdmin() {
            return this.authStore.isSuperAdmin;
        },
        isAdmin() {
            return this.authStore.isAdmin;
        },
        users() {
            return this.userStore.getUsers
                .filter(user => !this.filter.client || this.filter.client === user.client)
                .filter(user => null === this.filter.isEnabled || this.filter.isEnabled === user.isEnabled)
                .slice()
                .sort((userA, userB) => {
                    if (userA.isEnabled === userB.isEnabled) {
                        if (userA.username.toUpperCase() < userB.username.toUpperCase()) {
                            return -1;
                        }
                    } else if (userA.isEnabled && !userB.isEnabled) {
                        return -1;
                    } else if (!userA.isEnabled && userB.isEnabled) {
                        return 1;
                    }
                })
                ;
        },
        isLoading() {
            return this.userStore.isLoading || this.clientStore.isLoadingFetch;
        },
    },
    created() {
        this.userStore.fetchUsers();
    },
    methods: {
        isLoadingToggleUserState(userUri) {
            return this.userStore.isLoadingChange(userUri);
        },
        rolesFormatter(roles) {
            return roles
                .map(value => {
                    switch (value) {
                        case 'ROLE_USER':
                            return 'Benutzer';
                        case 'ROLE_ADMIN':
                            return 'Administrator';
                        case 'ROLE_SUPER_ADMIN':
                            return 'Super-Administrator';
                        case 'ROLE_ALLOWED_TO_SWITCH':
                            return 'Impersonator';
                    }

                    return value;
                })
                .sort((a, b) => a > b ? 1 : -1)
                .join(', ');
        },
        formatDateTime(dateTime) {
            return formatDateTime(dateTime);
        },
        clientFormatter(clientIri) {
            return this.clientStore.getClientByIri(clientIri)?.name;
        },
        editUser(user) {
            this.editModal.title = `Benutzer "${user.username}" bearbeiten`;
            this.$root.$emit('bv::show::modal', this.editModal.id);
            this.editModal.selectedUser = user;
        },
        resetEditModal() {
            this.editModal.user = {};
            this.$root.$emit('bv::hide::modal', this.editModal.id);
        },
        async handleSubmit(payload) {
            payload.user = this.editModal.selectedUser['@id'];
            const user = await this.userStore.change(payload);
            if (user) {
                this.alertStore.success(`Der Benutzer "${user.username}" wurde erfolgreich geändert.`, 'Benutzer geändert');
                this.resetEditModal();
            } else {
                this.alertStore.error('Benutzer ändern fehlgeschlagen', 'Upps! :-(');
            }
        },
        toggleEnabled: function (user, isEnabled) {
            if (isEnabled) {
                this.userStore.disable({user: user['@id']});
            } else {
                this.userStore.enable({user: user['@id']});
            }
        },
        switchUser(user) {
            this.authStore.switchUser(user);
        },
        exitSwitchUser() {
            this.authStore.exitSwitchUser();
        },
    },
};
</script>

<style scoped lang="scss">
</style>
