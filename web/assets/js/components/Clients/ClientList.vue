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
            v-show="!isLoading && clients.length"
            :items="clients"
            :fields="fields"
            small
            striped
            class="mb-0"
            stacked="md"
        >
            <template v-slot:cell(users)="row">
                <div class="d-flex justify-content-between">
                    <ul>
                        <li v-for="userIri in row.item.users">
                            {{ getUserByIri(userIri).username }}
                            <mdicon
                                v-if="getUserByIri(userIri).isEnabled"
                                name="Account"
                                size="16"
                                class="text-success"
                            />
                            <mdicon
                                v-else
                                name="AccountOff"
                                size="16"
                                class="text-info"
                            />
                            <mdicon
                                v-if="getUserByIri(userIri).roles.indexOf('ROLE_ADMIN') !== -1"
                                name="AccountSupervisor"
                                size="16"
                                class="text-primary"
                            />
                            <mdicon
                                v-if="getUserByIri(userIri).isSuperAdmin"
                                name="AccountSupervisor"
                                size="16"
                                class="text-danger"
                            />
                            <small class="text-muted">{{ getUserByIri(userIri).email }}</small>
                        </li>
                    </ul>
                </div>
            </template>
            <template v-slot:cell(actions)="row">
                <div class="d-flex justify-content-around">
                    <b-button
                        size="sm"
                        class="mr-2"
                        @click=""
                    >
                        Nutzer hinzufügen
                    </b-button>
                </div>
            </template>
        </b-table>
    </div>
</template>

<script>
'use strict';
import dayjs from 'dayjs';

export default {
    name: 'ClientList',
    data: function () {
        return {
            fields: [
                {
                    key: 'name',
                    label: 'Name',
                    sortable: true,
                    class: 'text-center',
                },
                {
                    key: 'email',
                    label: 'E-Mail',
                    sortable: true,
                    class: 'text-center',
                },
                {
                    key: 'description',
                    label: 'Beschreibung',
                    sortable: true,
                    class: 'text-center',
                },
                {
                    key: 'users',
                    label: 'Benutzer',
                    class: 'text-left',
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
                    class: 'text-center',
                },
                // { key: 'actions', label: 'Aktionen', class: 'text-center' },
            ],
            editModalRolle: {
                id: 'edit-modal-rolle',
                title: '',
            },
        };
    },
    computed: {
        clients() {
            return this.$store.getters['client/clients'];
        },
        isLoading() {
            return this.$store.getters['client/isLoading'];
        },
        error() {
            return this.$store.getters['client/error'];
        },
    },
    created() {
        this.$store.dispatch('client/findAll');
        this.$store.dispatch('user/findAll');
    },
    methods: {
        getUserByIri(userIri) {
            return this.$store.getters['user/getUserByIri'](userIri);
        },
        editRolle(rolle, client) {
            this.$root.$emit('bv::show::modal', this.editModalRolle.id);
        },
        resetEditModalRolle() {
            this.editModalRolle.title = '';
        },
    },
};
</script>

<style scoped lang="scss">
</style>
