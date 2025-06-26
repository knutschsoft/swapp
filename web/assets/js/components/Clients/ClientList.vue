<template>
    <div>
        <v-data-table-server
            :items-per-page="itemsPerPage"
            :headers="headers"
            :items="serverItems"
            :items-length="totalItems"
            :items-per-page-options="itemsPerPageOptions"
            :items-per-page-text="itemsPerPageText"
            :loading="isLoading()"
            :search="search"
            item-value="name"
            :no-data-text="noItemsText"
            :loading-text="loadingText"
            multi-sort
            hover
            density="compact"
            items-length=""
            hide-default-footer
            @update:options="loadItems"
            :options.sync="deprecatedOptions"
            :no-results-text="noItemsText"
        >
            <template v-slot:item.name="{item}">
                {{ item.name }}
                <br>
                <small class="text-muted">{{ item.email }}</small>
            </template>
            <template v-slot:item.users="{item}">
                <div class="d-flex justify-content-between">
                    <ul>
                        <template v-for="(userIri, i) in item.users">
                            <li v-if="i < 5">
                                {{ getUserByIri(userIri)?.username }}
                                <mdicon
                                    v-if="getUserByIri(userIri)?.isEnabled"
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
                                    v-if="getUserByIri(userIri)?.isSuperAdmin"
                                    name="AccountSupervisor"
                                    size="16"
                                    class="text-danger"
                                />
                                <small class="text-muted">{{ getUserByIri(userIri)?.email }}</small>
                            </li>
                        </template>
                        <li
                            v-if="item.users.length >= 5"
                        >{{ item.users.length - 5 }} weitere Benutzer
                        </li>
                    </ul>
                </div>
            </template>
            <template v-slot:item.createdAt="{item}">
                {{ formatDateTime(item.createdAt) }}
            </template>
            <template v-slot:item.updatedAt="{item}">
                {{ item.updatedAt ? formatDateTime(item.updatedAt) : 'nie' }}
            </template>
            <template v-slot:item.actions="{item}">
                <v-row justify="center">
                    <v-btn
                        color="secondary"
                        @click.stop="openClientEditDialog(item)"
                    >
                        Klient bearbeiten
                        <v-icon
                            small
                            class="ml-2"
                        >
                            mdi-pencil-outline
                        </v-icon>
                    </v-btn>
                </v-row>
            </template>
        </v-data-table-server>

        <v-dialog
            v-model="dialog"
            scrollable
            max-width="800px"
        >
            <v-card>
                <v-card-title class="text-h5 grey lighten-2">
                    Klient ändern
                </v-card-title>
                <v-card-text>
                    <client-form
                        v-if="editClient"
                        submit-button-text="Speichern"
                        :initial-client="editClient"
                        @submitted="handleSubmit"
                    />
                </v-card-text>
            </v-card>
        </v-dialog>
    </div>
</template>

<script lang="ts">
'use strict';
import {ref, computed, onMounted, watch} from 'vue';
import ClientForm from './ClientForm.vue';
import { type Client} from '../../model';
import {useAlertStore, useClientStore, useUserStore} from '../../stores';
import ClientApi from '../../api/client.js';
import {
    formatDateTime,
    itemsPerPageOptions,
    itemsPerPageText,
    loadingText,
    noItemsText,
    type SortItem,
    type TableHeaders
} from '../../utils'

export default {
    name: 'ClientList',
    components: {
        ClientForm,
    },
    setup() {
        const alertStore = useAlertStore();
        const clientStore = useClientStore();
        const userStore = useUserStore();

        const deprecatedOptions = ref({});
        const totalItems = ref(0);
        const search = ref('');
        const dialog = ref<boolean>(false);
        const itemsPerPage = ref(itemsPerPageOptions[itemsPerPageOptions.length - 1].value);
        const serverItems = ref([]);
        const tableOptions = ref<{ sortBy: SortItem[]; page?: number; itemsPerPage?: number }>({
            sortBy: [],
        });
        const headers = ref<TableHeaders>([
            {
                key: 'name',
                title: 'Name',
                sortable: true,
                align: 'center',
            },
            {
                key: 'description',
                title: 'Beschreibung',
                sortable: true,
                align: 'center',
            },
            {
                key: 'users',
                title: 'Benutzer',
                align: 'start',
            },
            {
                key: 'createdAt',
                title: 'Erstellt am',
                sortable: true,
                align: 'center',
            },
            {
                key: 'updatedAt',
                title: 'Geändert am',
                sortable: true,
                align: 'center',
            },
            {key: 'actions', title: 'Aktionen', align: 'center', sortable: false},
        ]);

        const editClient = ref<Client|null>(null);

        const clients = computed(() => clientStore.getClients);
        const isLoading = computed(() => clientStore.isLoadingChange || clientStore.isLoadingCreate);
        const error = computed(() => clientStore.getErrors);

        onMounted(async () => {
            await Promise.all([
                userStore.fetchUsers({}),
                clientStore.fetchClients(),
            ]);
        });

        const getUserByIri = (userIri: string) => userStore.getUserByIri(userIri);

        const isUserAdminByIri = (userIri: string) => {
            const user = getUserByIri(userIri);
            return user ? user.roles.includes('ROLE_ADMIN') : false;
        };

        const openClientEditDialog = (client: Client) => {
            editClient.value = client;
            dialog.value = true;
        };

        const resetEditModalClient = () => {
            editClient.value = null;
            dialog.value = false;
        };

        const handleSubmit = async (payload: Client) => {
            if (!editClient.value) return;
            payload.client = editClient.value['@id'];
            const client = await clientStore.changeClient(payload);
            if (client) {
                alertStore.success(`Der Klient "${client.name}" wurde erfolgreich geändert.`, 'Klient geändert');
                resetEditModalClient();
            } else {
                alertStore.error('Klient ändern fehlgeschlagen', 'Upps! :-(');
            }
        };

        watch(
            () => deprecatedOptions.value,
            async () => {
                await loadItems(deprecatedOptions.value)
            },
            {deep: true},
        );

        const loadItems = async ({ page, itemsPerPage, sortBy }) => {
            if (-1 === itemsPerPage) {
                itemsPerPage = 1000;
            }
            tableOptions.value = {page, itemsPerPage, sortBy};
            const data: Record<string, string | number | boolean> = {
                page,
                itemsPerPage,
            };
            sortBy.forEach((val: SortItem) => {
                data[`sortBy[${val.key}]`] = val.order;
            });
            try {
                const clients = await ClientApi.find(data);
                const items = clients.data['member'];
                const total = clients.data['totalItems'];

                serverItems.value = items;
                totalItems.value = total ?? 0;
            } catch (e: unknown) {
                console.error(e);
            }
        };

        // Return the reactive references and methods
        return {
            deprecatedOptions,
            totalItems,
            search,
            itemsPerPage,
                itemsPerPageText,
            itemsPerPageOptions,
            noItemsText,
            loadingText,
            serverItems,
            tableOptions,
            headers,
            alertStore,
            clientStore,
            userStore,
            openClientEditDialog,
            clients,
            isLoading,
            error,
            getUserByIri,
            isUserAdminByIri,
            editClient,
            resetEditModalClient,
            handleSubmit,
            loadItems,
            dialog,
            formatDateTime,
        };
    },
};
</script>

<style scoped lang="scss">
</style>
