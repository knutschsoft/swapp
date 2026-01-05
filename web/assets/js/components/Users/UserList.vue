<template>
    <div>
        <v-row
            class="px-3 pt-3"
        >
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
                <filter-boolean-field
                    v-model="filter.isEnabled"
                    :default-value="true"
                    label="Nur aktivierte?"
                    :is-loading="isLoading"
                />
            </v-col>
        </v-row>
        <v-data-table-server
            :items="users"
            :headers="headers"
            class="mb-0"
            :loading="isLoading"
            loading-text="Lade Daten..."
            :items-per-page="itemsPerPage"
            :items-per-page-options="itemsPerPageOptions"
            :items-per-page-text="itemsPerPageText"
            items-length=""
            hide-default-footer
            :no-data-text="noItemsText"
            :no-results-text="noItemsText"
            small
            striped
        >
            <template #item.username="{item}">
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
            <template #item.teams="{item}">
                <template v-if="item.teams.length === 0">-</template>
                {{ item.teams.map(team => team.name).join(', ') }}
            </template>
            <template #item.roles="{item}">
                {{ rolesFormatter(item.roles)}}
            </template>
            <template #item.isEnabled="{item}">
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
            <template #item.client="{item}">
                {{ clientFormatter(item.client) }}
            </template>
            <template #item.createdAt="{item}">
                {{ formatDateTime(item.createdAt) }}
            </template>
            <template #item.updatedAt="{item}">
                {{ item.updatedAt ? formatDateTime(item.updatedAt) : 'nie' }}
            </template>
            <template #item.lastLoginAt="{item}">
                {{ item.lastLoginAt ? formatDateTime(item.lastLoginAt) : 'nie' }}
            </template>
            <template #item.actions="{item}">
                <div class="d-flex justify-content-around">
                    <v-menu
                        location="bottom end"
                        eager
                    >
                        <template #activator="{ props }">
                            <v-btn
                                color="secondary"
                                v-bind="props"
                                class="text-transform-none"
                                density="comfortable"
                                :loading="isLoading"
                                :append-icon="props['aria-expanded'] === 'true' ? 'mdi-triangle-small-up' : 'mdi-triangle-small-down'"
                            >
                                Aktionen
                            </v-btn>
                        </template>
                        <v-list density="compact">
                            <v-list-item link>
                                <v-list-item-title
                                    @click="openUserEditDialog(item)"
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
                        color="secondary"
                        density="comfortable"
                        class="ml-2 text-transform-none"
                        :data-test="`switch-user-${item.username}`"
                        prepend-icon="mdi-account-switch"
                        @click="switchUser(item)"
                    >
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
        </v-data-table-server>

        <v-dialog
            v-model="dialog"
            scrollable
        >
            <v-card>
                <v-card-title
                    v-if="editUser"
                    class="text-h5 grey lighten-2"
                >
                    Benutzer "{{ editUser.username }}" bearbeiten
                </v-card-title>
                <v-card-text>
                    <user-form
                        v-if="editUser"
                        submit-button-text="Speichern"
                        :initial-user="editUser"
                        @submitted="handleSubmit"
                    />
                </v-card-text>
            </v-card>
        </v-dialog>
    </div>
</template>

<script>
'use strict';
import UserForm from './UserForm.vue';
import {useAlertStore, useAuthStore, useClientStore, useUserStore} from '@/js/stores';
import {ClientSelect, FilterBooleanField} from "@/js/components/Common";
import {
    formatDateTime,
    itemsPerPageOptions,
    itemsPerPageText,
    loadingText,
    noItemsText,
} from '@/js/utils'

export default {
    name: 'UserList',
    components: {
        FilterBooleanField,
        ClientSelect,
        UserForm,
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
            editUser: null,
            dialog: false,

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
                    key: 'username',
                    title: 'Benutzername',
                    sortable: true,
                    class: 'text-center',
                },
                {
                    key: 'teams',
                    title: 'Teams',
                    sortable: false,
                    align: 'center',
                },
                {
                    key: 'email',
                    title: 'E-Mail',
                    align: 'center',
                },
                {
                    key: 'roles',
                    title: 'Rollen',
                    sortable: false,
                    align: 'center',
                },
                {
                    key: 'isEnabled',
                    title: 'Account aktiviert?',
                    sortable: true,
                    class: 'text-center',
                    align: 'center',
                }
            ];
            if (this.isSuperAdmin) {
                headers.push({
                    key: 'client',
                    title: 'Klient',
                    sortable: false,
                    align: 'center',
                })
            }
            headers.push(...[
                {
                    key: 'createdAt',
                    title: 'Erstellt am',
                    align: 'center',
                },
                {
                    key: 'updatedAt',
                    title: 'Geändert am',
                    align: 'center',
                }
            ]);
            if (this.isSuperAdmin) {
                headers.push(...[
                    {
                        key: 'lastLoginAt',
                        title: 'Letzter Login am:',
                        align: 'center',
                    },
                    {
                        key: 'createdBy',
                        title: 'Erstellt von:',
                        sortable: false,
                        align: 'center',
                    },
                    {
                        key: 'updatedBy',
                        title: 'Geändert von:',
                        sortable: false,
                        align: 'center',
                    },
                ]);
            }
            headers.push({ key: 'actions', title: 'Aktionen', sortable: false })

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
                .filter(user => 'null' === this.filter.isEnabled || this.filter.isEnabled === user.isEnabled)
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
        openUserEditDialog(user) {
            this.editUser = user;
            this.dialog = true;
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
        async handleSubmit(payload) {
            payload.user = this.editUser['@id'];
            const user = await this.userStore.change(payload);
            if (user) {
                this.alertStore.success(`Der Benutzer "${user.username}" wurde erfolgreich geändert.`, 'Benutzer geändert');
                this.dialog = false;
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
