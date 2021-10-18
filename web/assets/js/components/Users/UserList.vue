<template>
    <div>
        <div
            v-if="isLoading"
            class="d-flex justify-content-center my-3"
        >
            <b-spinner
                v-show="isLoading"
                style="width: 3rem; height: 3rem;"
                label="is Loading Spinner"
            />
        </div>
        <b-table
            v-show="!isLoading && users.length"
            :items="users"
            :fields="fields"
            small
            striped
            class="mb-0"
            :stacked="this.isSuperAdmin ? 'xl' : 'lg'"
        >
            <template v-slot:cell(enabled)="row">
                <mdicon
                    v-if="isLoadingToggleUserState(row.item['@id'])"
                    name="loading"
                    class="text-muted"
                    spin
                />
                <div
                    v-else
                    @click="toggleEnabled(row.item, row.item.enabled)"
                    :title="`Account ${ row.item.enabled ? 'de' : '' }aktivieren`"
                    class="cursor-pointer"
                >
                    <mdicon
                        v-if="row.item.enabled"
                        name="check"
                        class="text-success"
                    />
                    <mdicon
                        v-else
                        name="AccountOff"
                        class="text-info"
                        disabled
                    />
                </div>
            </template>
            <template v-slot:cell(actions)="row">
                <div class="d-flex justify-content-around">
                    <b-dropdown
                        text="Aktionen"
                        size="sm"
                    >
                        <b-dropdown-item-button
                            size="sm"
                            @click="editUser(row.item)"
                            :disabled="isLoadingToggleUserState(row.item['@id'])"
                        >
                            Account bearbeiten
                        </b-dropdown-item-button>
                        <b-dropdown-item-button
                            v-if="isAdmin"
                            size="sm"
                            class="mr-2"
                            @click="toggleEnabled(row.item, row.item.enabled)"
                            :disabled="isLoadingToggleUserState(row.item['@id'])"
                        >
                            Account {{ row.item.enabled ? 'de' : '' }}aktivieren
                        </b-dropdown-item-button>
                        <b-dropdown-divider
                            v-if="isSuperAdmin && false"
                        />
                        <b-dropdown-item-button
                            v-if="isSuperAdmin && false"
                            @click="editUser(row.item)"
                            size="sm"
                            variant="danger"
                            :disabled="isLoadingToggleUserState(row.item['@id'])"
                        >
                            <mdicon
                                name="AlertDecagramOutline"
                            />
                            Löschen
                        </b-dropdown-item-button>
                    </b-dropdown>
                    <b-button
                        v-if="isSuperAdmin && !isUserSwitched"
                        size="sm"
                        class="flex-item d-flex align-items-center ml-2"
                        :data-test="`switch-user-${row.item.username}`"
                        @click="switchUser(row.item)"
                    >
                        <b-icon
                            icon="people-fill"
                            class="rounded-circle bg-secondary p-1 mr-1 cursor-pointer flex-item"
                            font-scale="1.5"
                        />
                        Nutzer wechseln
                    </b-button>
                    <b-button
                        v-else-if="isUserSwitched"
                        size="sm"
                        class="flex-item d-flex align-items-center ml-2"
                        data-test="exit-switch-user"
                        @click="exitSwitchUser()"
                    >
                        <b-icon
                            icon="person-fill"
                            class="rounded-circle bg-secondary p-1 mr-1 cursor-pointer flex-item"
                            font-scale="1.5"
                        />
                        <b-icon
                            icon="box-arrow-left"
                            class="rounded-circle bg-secondary p-1 mr-1 cursor-pointer flex-item"
                            font-scale="1.5"
                        />
                        <b-icon
                            icon="person-square"
                            class="rounded-circle bg-secondary p-1 mr-1 cursor-pointer flex-item"
                            font-scale="1.5"
                        />
                        Nutzerwechsel beenden
                    </b-button>
                </div>
            </template>
        </b-table>

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
import dayjs from 'dayjs';

export default {
    name: 'UserList',
    components: { UserForm },
    data: function () {
        return {
            editModal: {
                selectedUser: {},
                id: 'edit-modal-user',
                title: '',
            },
        };
    },
    computed: {
        fields() {
            return [
                {
                    key: 'username',
                    label: 'Benutzername',
                    sortable: true,
                    class: 'text-center',
                },
                {
                    key: 'teams',
                    sortable: true,
                    formatter: (value, key, item) => {
                        let teamNames = [];
                        value.forEach(team => {
                            teamNames.push(team.name);
                        });

                        if (teamNames.length) {
                            return teamNames.join(', ');
                        }

                        return '-';
                    },
                    sortByFormatted: true,
                    class: 'text-center',
                },
                {
                    key: 'email',
                    label: 'E-Mail',
                    sortable: true,
                    class: 'text-center',
                },
                {
                    key: 'roles',
                    label: 'Rollen',
                    formatter: (value, key, item) => {
                        return value
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
                    sortable: true,
                    sortByFormatted: true,
                    class: 'text-center',
                },
                {
                    key: 'enabled',
                    label: 'Account aktiviert?',
                    sortable: true,
                    class: 'text-center',
                },
                {
                    key: 'client',
                    label: 'Klient',
                    sortable: true,
                    sortByFormatted: true,
                    class: !this.isSuperAdmin ? 'd-none text-center' : 'text-center',
                    formatter: this.clientFormatter,
                },
                {
                    key: 'createdAt',
                    label: 'Erstellt am',
                    sortable: true,
                    sortByFormatted: false,
                    formatter: (value, key, item) => {
                        return dayjs(value).format('DD.MM.YYYY HH:mm:ss');
                    },
                    class: 'text-center',
                },
                {
                    key: 'updatedAt',
                    label: 'Geändert am',
                    sortable: true,
                    sortByFormatted: false,
                    formatter: (value, key, item) => {
                        return dayjs(value).format('DD.MM.YYYY HH:mm:ss');
                    },
                    class: !this.isSuperAdmin ? 'd-none text-center' : 'text-center',
                },
                {
                    key: 'createdBy',
                    label: 'Erstellt von:',
                    sortable: true,
                    sortByFormatted: true,
                    class: !this.isSuperAdmin ? 'd-none text-center' : 'text-center',
                },
                {
                    key: 'updatedBy',
                    label: 'Geändert von:',
                    sortable: true,
                    sortByFormatted: true,
                    class: !this.isSuperAdmin ? 'd-none text-center' : 'text-center',
                },
                { key: 'actions', label: 'Aktionen', class: 'text-center' },
            ];
        },
        isUserSwitched() {
            return this.$store.getters['security/isUserSwitched'];
        },
        isSuperAdmin() {
            return this.$store.getters['security/isSuperAdmin'];
        },
        isAdmin() {
            return this.$store.getters['security/isAdmin'];
        },
        users() {
            return this.$store.getters['user/users'];
        },
        isLoading() {
            return this.$store.getters['user/isLoading'];
        },
        error() {
            return this.$store.getters['user/error'];
        },
    },
    created() {
        this.$store.dispatch('user/findAll');
    },
    methods: {
        isLoadingToggleUserState(userUri) {
            return this.$store.getters['user/isLoadingToggleUserState'](userUri);
        },
        clientFormatter(value) {
            return this.getClientByIri(value).name;
        },
        getClientByIri(iri) {
            const id = iri.replace('/api/clients/', '');

            return this.$store.getters['client/getClientById'](id);
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
            const user = await this.$store.dispatch('user/change', payload);
            if (user) {
                const message = `Der Benutzer "${user.username}" wurde erfolgreich geändert.`;
                this.$bvToast.toast(message, {
                    title: 'Benutzer geändert',
                    toaster: 'b-toaster-top-right',
                    autoHideDelay: 10000,
                    appendToast: true,
                    solid: true,
                });

                this.resetEditModal();
            } else {
                this.$bvToast.toast('Upps! :-(', {
                    title: 'Benutzer ändern fehlgeschlagen',
                    toaster: 'b-toaster-top-right',
                    autoHideDelay: 10000,
                    variant: 'danger',
                    appendToast: true,
                    solid: true,
                });
            }
        },
        toggleEnabled: function (user, isEnabled) {
            if (isEnabled) {
                this.$store.dispatch('user/disable', user['@id']);
            } else {
                this.$store.dispatch('user/enable', user['@id']);
            }
        },
        switchUser(user) {
            this.$store.dispatch('security/switchUser', user);
        },
        exitSwitchUser() {
            this.$store.dispatch('security/exitSwitchUser');
        },
    },
};
</script>

<style scoped lang="scss">
</style>
