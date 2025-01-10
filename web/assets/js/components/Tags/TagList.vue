<template>
    <div>
        <v-row
            v-if="!isLoading"
            class="p-2 mb-0 mt-0"
        >
            <v-col
                v-if="isSuperAdmin"
                cols="12"
                sm="6"
            >
                <v-select
                    v-model="filter.client"
                    :items="availableClients"
                    item-value="@id"
                    item-text="name"
                    label="Für welchen Klienten?"
                    dense
                    clearable
                    outlined
                    :success="filter.client !== null"
                    class="flex-grow-1"
                    hint="Nur bestimmten Klient anzeigen."
                    :persistent-hint="filter.client !== null"
                    persistent-placeholder
                >
                </v-select>
            </v-col>
            <v-col
                :sm="isSuperAdmin ? 6 : 12"
            >
                <v-select
                    v-model="filter.isEnabled"
                    :items="isEnabledOptions"
                    item-value="value"
                    item-text="text"
                    label="Nur aktivierte?"
                    dense
                    :clearable="filter.isEnabled"
                    outlined
                    :success="filter.isEnabled === true || filter.isEnabled === false"
                    class="flex-grow-1"
                    hint="Nur aktivierte Accounts?"
                    :persistent-hint="filter.isEnabled === true || filter.isEnabled === false"
                    persistent-placeholder
                >
                </v-select>
            </v-col>
        </v-row>
        <v-data-table
            :items="tags"
            :is-loading="isLoading"
            :headers="fields"
            small
            striped
            class="mb-0"
            multi-sort
            dense
            :items-per-page="itemsPerPage"
            :items-per-page-options="itemsPerPageOptions"
            :items-per-page-text="itemsPerPageText"
            :no-data-text="noItemsText"
            :loading-text="loadingText"
        >
            <template v-slot:item.isEnabled="{item}">
                <v-progress-circular
                    v-if="isLoadingToggleTagState(item['@id'])"
                    indeterminate
                    :size="20"
                    :width="2"
                    color="secondary"
                ></v-progress-circular>
                <div
                    v-else
                    @click="toggleEnabled(item, item.isEnabled)"
                    :title="`Tag ${ item.isEnabled ? 'de' : '' }aktivieren`"
                    class="cursor-pointer"
                >
                    <v-icon
                        v-if="item.isEnabled"
                        color="success"
                    >
                        mdi-check
                    </v-icon>
                    <v-icon
                        v-else
                        color="info"
                        disabled
                    >
                        mdi-tag-off
                    </v-icon>
                </div>
            </template>
            <template v-slot:item.color="{item}">
                <color-badge
                    :color="item.color"
                />
            </template>
            <template v-slot:item.client="{item}">
                {{ clientFormatter(item.client) }}
            </template>

            <template v-slot:item.actions="{item}">
                <v-btn
                    small
                    color="secondary"
                    @click="toggleEnabled(item, item.isEnabled)"
                >
                    {{ item.isEnabled ? 'deaktivieren' : 'aktivieren' }}
                </v-btn>
                <v-menu
                    top
                    open-on-hover
                    :nudge-top="7"
                    offset-y
                >
                    <template v-slot:activator="{ on, attrs }">
                        <v-icon
                            class="text-muted"
                            v-bind="attrs"
                            v-on="on"
                        >
                            mdi-help-circle-outline
                        </v-icon>
                    </template>
                    <v-card>
                        <v-card-text class="font-weight-bold">
                            Wozu kann ich einen Tag aktivieren?
                        </v-card-text>
                        <v-divider class="my-0"></v-divider>
                        <v-card-text>
                            <ul class="mb-0">
                                <li>Aktivierte Tags können einem Wegpunkt zugeordnet werden.</li>
                                <li>Deaktivierte Tags können einem Wegpunkt nicht zugeordnet werden. Sie sind jedoch weiterhin an bereits zugeordneten Wegpunkten vorhanden.</li>
                                <li>Deaktivierte Tags werden nicht als Filter auf dem Dashboard angezeigt, wenn sie keinem Wegpunkt zugeordnet sind.</li>
                            </ul>
                        </v-card-text>
                    </v-card>
                </v-menu>
            </template>
        </v-data-table>
    </div>
</template>

<script>
'use strict';
import ColorBadge from './ColorBadge.vue';
import {useAlertStore, useAuthStore, useClientStore, useTagStore} from '../../stores';
import {
    itemsPerPageOptions,
    itemsPerPageText,
    loadingText,
    noItemsText,
} from '../../utils'

export default {
    name: 'TagList',
    components: { ColorBadge },
    data: function () {
        return {
            itemsPerPageOptions,
            itemsPerPageText,
            noItemsText,
            loadingText,
            itemsPerPage: -1,
            alertStore: useAlertStore(),
            authStore: useAuthStore(),
            clientStore: useClientStore(),
            tagStore: useTagStore(),
            isEnabledOptions: [
                { value: null, text: 'egal' },
                { value: true, text: 'ja' },
                { value: false, text: 'nein' },
            ],
            filter: {
                client: null,
                isEnabled: true,
            },
        };
    },
    computed: {
        fields() {
            let headers = [
            ];

            if (this.isSuperAdmin) {
                headers.push({
                    value: 'id',
                    text: 'ID',
                    sortable: true,
                });
            }
            headers.push(...[
                {
                    value: 'name',
                    text: 'Name',
                    sortable: true,
                },
                {
                    value: 'color',
                    text: 'Farbe',
                    sortable: true,
                },
                {
                    value: 'isEnabled',
                    text: 'Tag aktiviert?',
                    sortable: true,
                }
            ]);
            if (this.isSuperAdmin) {
                headers.push({
                    value: 'client',
                    text: 'Klient',
                    sortable: false,
                });
            }
            headers.push({ value: 'actions', text: 'Aktionen' });
            return headers;
        },
        availableClients() {
            return this.clientStore.getClients;
        },
        tags() {
            return this.tagStore.getTags
                .filter(tag => !this.filter.client || this.filter.client === tag.client)
                .filter(tag => null === this.filter.isEnabled || this.filter.isEnabled === tag.isEnabled)
                .slice()
                .sort((tagA, tagB) => {
                    if (tagA.isEnabled === tagB.isEnabled) {
                        if (tagA.name.toUpperCase() < tagB.name.toUpperCase()) {
                            return -1;
                        }
                    } else if (tagA.isEnabled && !tagB.isEnabled) {
                        return -1;
                    } else if (!tagA.isEnabled && tagB.isEnabled) {
                        return 1;
                    }
                });
        },
        isLoading() {
            return this.tagStore.loadingArray.includes('fetch') || this.clientStore.isLoading;
        },
        error() {
            return this.tagStore.getErrors;
        },
        isSuperAdmin() {
            return this.authStore.isSuperAdmin;
        },
    },
    async created() {
        await Promise.all([
            this.tagStore.fetchTags(),
            this.clientStore.fetchClients(),
        ]);
    },
    methods: {
        isLoadingToggleTagState(tagIri) {
            return this.tagStore.isLoadingToggleTagState(tagIri);
        },
        clientFormatter(clientIri) {
            return this.clientStore.getClientByIri(clientIri)?.name;
        },
        toggleEnabled: function (tag, isEnabled) {
            let changedTag, message, title;
            if (isEnabled) {
                changedTag = this.tagStore.disable({ tag: tag['@id'] });
                message = `Der Tag "${tag.name}" wurde erfolgreich deaktiviert. Er kann nun nicht mehr zu einem Wegpunkt hinzugefügt verwendet.`;
                title = `Tag deaktiviert`;
            } else {
                changedTag = this.tagStore.enable({ tag: tag['@id'] });
                message = `Der Tag "${tag.name}" wurde erfolgreich aktiviert. Er kann nun zu einem Wegpunkt hinzugefügt verwendet.`;
                title = `Tag aktiviert`;
            }
            if (changedTag) {
                this.alertStore.success(message, title);
            } else {
                this.alertStore.error(`Tag ${ isEnabled ? 'deaktivieren' : 'aktivieren'} fehlgeschlagen`, 'Upps! :-(');
            }
        },
    },
};
</script>

<style scoped lang="scss">
</style>
