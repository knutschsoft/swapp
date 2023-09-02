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
            <template v-slot:cell(name)="row">
                {{ row.item.name }}
                <br>
                <small class="text-muted">{{ row.item.email }}</small>
            </template>
            <template v-slot:cell(users)="row">
                <div class="d-flex justify-content-between">
                    <ul>
                        <template v-for="(userIri, i) in row.item.users">
                            <li v-if="i < 5">
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
                                    v-if="isUserAdminByIri(userIri)"
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
                        </template>
                        <li
                            v-if="row.item.users.length >= 5"
                        >{{ row.item.users.length - 5 }} weitere Benutzer</li>
                    </ul>
                </div>
            </template>
            <template v-slot:cell(actions)="row">
                <div class="d-flex justify-content-around">
                    <b-button
                        size="sm"
                        @click="editClient(row.item)"
                    >
                        Klient bearbeiten
                        <b-icon-pencil />
                    </b-button>
                </div>
            </template>
        </b-table>
        <b-modal
            :id="editModalClient.id"
            :title="editModalClient.title"
            size="lg"
            @hide="resetEditModalClient"
            title="Klient ändern"
            hide-footer
        >
            <client-form
                v-if="editModalClient.selectedClient"
                submit-button-text="Speichern"
                :initial-client="editModalClient.selectedClient"
                @submit="handleSubmit"
            />
        </b-modal>
    </div>
</template>

<script>
'use strict';
import dayjs from 'dayjs';
import ClientForm from './ClientForm.vue';
import { useClientStore } from '../../stores/client';
import { useUserStore } from '../../stores/user';

export default {
    name: 'ClientList',
    components: {
        ClientForm,
    },
    data: function () {
        return {
            clientStore: useClientStore(),
            userStore: useUserStore(),
            fields: [
                {
                    key: 'name',
                    label: 'Name',
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
                { key: 'actions', label: 'Aktionen', class: 'text-center' },
            ],
            editModalClient: {
                id: 'edit-modal-client',
                title: '',
                selectedClient: null,
            },
        };
    },
    computed: {
        clients() {
            return this.clientStore.getClients;
        },
        isLoading() {
            return this.clientStore.isLoading
        },
        error() {
            return this.clientStore.getErrors;
        },
    },
    async created() {
        await Promise.all([
            this.userStore.fetchUsers(),
            this.clientStore.fetchClients(),
        ]);
    },
    mounted() {
    },
    methods: {
        getUserByIri(userIri) {
            return this.userStore.getUserByIri(userIri);
        },
        isUserAdminByIri(userIri) {
            const user = this.getUserByIri(userIri);
            if (!user) {
                return false;
            }

            return user.roles.indexOf('ROLE_ADMIN') !== -1;
        },
        editClient(client) {
            this.$root.$emit('bv::show::modal', this.editModalClient.id);
            this.editModalClient.selectedClient = client;
        },
        resetEditModalClient() {
            this.$root.$emit('bv::hide::modal', this.editModalClient.id);
            this.editModalClient.selectedClient = '';
        },
        async handleSubmit(payload) {
            payload.client = this.editModalClient.selectedClient['@id'];
            const client = await this.clientStore.changeClient(payload);
            if (client) {
                const message = `Der Klient "${client.name}" wurde erfolgreich geändert.`;
                this.$bvToast.toast(message, {
                    title: 'Klient geändert',
                    toaster: 'b-toaster-top-right',
                    autoHideDelay: 10000,
                    variant: 'info',
                    appendToast: true,
                    solid: true,
                });

                this.resetEditModalClient();
            } else {
                this.$bvToast.toast('Upps! :-(', {
                    title: 'Klient ändern fehlgeschlagen',
                    toaster: 'b-toaster-top-right',
                    autoHideDelay: 10000,
                    variant: 'danger',
                    appendToast: true,
                    solid: true,
                });
            }
        },
    },
};
</script>

<style scoped lang="scss">
</style>
