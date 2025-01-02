<template>
    <div>
        <b-row
            v-if="!isLoading"
            class="p-2 mb-0 mt-0"
        >
            <b-col
                v-if="isSuperAdmin"
                xs="12"
                sm="6"
            >
                <b-input-group size="sm" class="mb-2">
                    <b-input-group-prepend>
                        <b-input-group-text
                            title="Nur bestimmten Klient anzeigen."
                            :class="filter.client !== null ? 'font-weight-bold' : ''"
                        >
                            Klient?
                        </b-input-group-text>
                    </b-input-group-prepend>
                    <b-form-select
                        v-model="filter.client"
                        data-test="client"
                        placeholder="Für welchen Klienten?"
                        :options="availableClients"
                        value-field="@id"
                        text-field="name"
                    >
                        <template #first>
                            <b-form-select-option :value="null">Alle Klienten</b-form-select-option>
                        </template>
                    </b-form-select>
                    <my-input-group-append
                        @click="filter.client = null"
                        :is-active="filter.client !== null"
                    />
                </b-input-group>
            </b-col>
            <b-col
                xs="12"
                :sm="isSuperAdmin ? 6 : 12"
            >
                <b-input-group size="sm" class="">
                    <b-input-group-prepend>
                        <b-input-group-text
                            title="Nur aktivierte Accounts?"
                            :class="filter.isEnabled !== true ? 'font-weight-bold' : ''"
                        >
                            Nur aktivierte?
                        </b-input-group-text>
                    </b-input-group-prepend>
                    <b-form-select
                        v-model="filter.isEnabled"
                        :options="isEnabledOptions"
                    />
                    <my-input-group-append
                        @click="filter.isEnabled = true"
                        :is-active="filter.isEnabled !== true"
                    />
                </b-input-group>
            </b-col>
        </b-row>
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
                <span :id="`questionHeaderId-${item.tagId}`">
                    <v-icon
                        class="text-muted"
                    >
                        mdi-help-circle-outline
                    </v-icon>
                </span>
                <b-popover
                    :target="`questionHeaderId-${item.tagId}`"
                    triggers="hover"
                    placement="top"
                >
                    <template #title>Wozu kann ich einen Tag aktivieren?</template>
                    <ul class="mb-0">
                        <li>Aktivierte Tags können einem Wegpunkt zugeordnet werden.</li>
                        <li>Deaktivierte Tags können einem Wegpunkt nicht zugeordnet werden. Sie sind jedoch weiterhin an bereits zugeordneten Wegpunkten vorhanden.</li>
                        <li>Deaktivierte Tags werden nicht als Filter auf dem Dashboard angezeigt, wenn sie keinem Wegpunkt zugeordnet sind.</li>
                    </ul>
                </b-popover>
            </template>
        </v-data-table>
    </div>
</template>

<script>
'use strict';
import ColorBadge from './ColorBadge.vue';
import ContentLoadingSpinner from '../ContentLoadingSpinner.vue';
import MyInputGroupAppend from '../../components/Common/MyInputGroupAppend.vue';
import {useAlertStore, useAuthStore, useClientStore, useTagStore} from '../../stores';
import {
    itemsPerPageOptions,
    itemsPerPageText,
    loadingText,
    noItemsText,
} from '../../utils'

export default {
    name: 'TagList',
    components: { ContentLoadingSpinner, ColorBadge, MyInputGroupAppend },
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
                {
                    value: 'name',
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
            ];
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
