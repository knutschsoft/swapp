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
            stacked="md"
        >
            <template v-slot:cell(enabled)="row">
                <mdicon
                    v-if="isLoadingToggleUserState"
                    name="loading"
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
                    />
                </div>
            </template>
            <template v-slot:cell(actions)="row">
                <div class="d-flex justify-content-around">
                    <b-dropdown
                        text="Aktionen"
                    >
                        <b-dropdown-item-button
                            size="sm"
                            @click="editUser(row.item)"
                        >
                            Account bearbeiten
                        </b-dropdown-item-button>
                        <b-dropdown-item-button
                            v-if="isAdmin"
                            size="sm"
                            class="mr-2"
                            @click="toggleEnabled(row.item.userId, row.item.enabled)"
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
                        v-else-if="isSuperAdmin"
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
                    class: !this.isSuperAdmin ? 'd-none' : '',
                    formatter: this.clientFormatter,
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
        isLoadingToggleUserState() {
            return this.$store.getters['user/isLoadingToggleUserState'];
        },
        error() {
            return this.$store.getters['user/error'];
        },
    },
    created() {
        this.$store.dispatch('user/findAll');
    },
    methods: {
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
