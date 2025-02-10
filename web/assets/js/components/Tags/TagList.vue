<template>
    <div>
        <v-row
            v-if="!isLoading"
            class="px-3 pt-3"
        >
            <v-col
                v-if="isSuperAdmin"
                cols="12"
                sm="6"
            >
                <client-select
                    v-model="filter.client"
                    :is-loading="isLoading"
                    :disabled="isLoading"
                />
            </v-col>
            <v-col
                :sm="isSuperAdmin ? 6 : 12"
            >
                <v-select
                    v-model="filter.isEnabled"
                    :items="isEnabledOptions"
                    item-value="value"
                    item-title="text"
                    label="Nur aktivierte?"
                    density="compact"
                    :clearable="filter.isEnabled"
                    variant="outlined"
                    :success="filter.isEnabled === true || filter.isEnabled === false"
                    class="flex-grow-1"
                    hint="Nur aktivierte Accounts?"
                    :persistent-hint="filter.isEnabled === true || filter.isEnabled === false"
                    persistent-placeholder
                >
                </v-select>
            </v-col>
        </v-row>
        <v-data-table-server
            :items="tags"
            :is-loading="isLoading"
            :headers="headers"
            class="mb-0"
            multi-sort
            density="compact"
            hide-default-footer
            items-length=""
            :items-per-page="itemsPerPage"
            :items-per-page-options="itemsPerPageOptions"
            :items-per-page-text="itemsPerPageText"
            :no-data-text="noItemsText"
            :loading-text="loadingText"
            mobile-breakpoint="md"
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
                    v-if="item.color"
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
                    location="bottom"
                    open-on-hover
                    open-on-click
                >
                    <template v-slot:activator="{ props }">
                        <v-icon
                            class="text-muted ml-2"
                            v-bind="props"
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
                            <ul class="mb-0 pl-5">
                                <li>Aktivierte Tags können einem Wegpunkt zugeordnet werden.</li>
                                <li>Deaktivierte Tags können einem Wegpunkt nicht zugeordnet werden. Sie sind jedoch weiterhin an bereits zugeordneten Wegpunkten vorhanden.</li>
                                <li>Deaktivierte Tags werden nicht als Filter auf dem Dashboard angezeigt, wenn sie keinem Wegpunkt zugeordnet sind.</li>
                            </ul>
                        </v-card-text>
                    </v-card>
                </v-menu>
            </template>
        </v-data-table-server>
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
import {ClientSelect} from "@/js/components/Common";

export default {
    name: 'TagList',
    components: {ClientSelect, ColorBadge },
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
        headers() {
            let headers = [
            ];

            if (this.isSuperAdmin) {
                headers.push({
                    key: 'tagId',
                    title: 'ID',
                });
            }
            headers.push(...[
                {
                    key: 'name',
                    title: 'Name',
                },
                {
                    key: 'color',
                    title: 'Farbe',
                    align: 'center',
                },
                {
                    key: 'isEnabled',
                    title: 'Tag aktiviert?',
                    align: 'center',
                }
            ]);
            if (this.isSuperAdmin) {
                headers.push({
                    key: 'client',
                    title: 'Klient',
                    sortable: false,
                    align: 'center',
                });
            }
            headers.push({ key: 'actions', title: 'Aktionen', sortable: false, align: 'center' });

            return headers;
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
