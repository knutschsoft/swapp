<template>
    <div>
        <content-loading-spinner
            :is-loading="isLoading"
        />
        <b-row
            v-if="!isLoading"
            class="p-2"
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
        <b-table
            v-show="!isLoading && tags.length"
            :items="tags"
            :fields="fields"
            sort-by="name"
            small
            striped
            class="mb-0"
            stacked="sm"
        >
            <template v-slot:cell(isEnabled)="row">
                <mdicon
                    v-if="isLoadingToggleTagState(row.item['@id'])"
                    name="loading"
                    class="text-muted"
                    spin
                />
                <div
                    v-else
                    @click="toggleEnabled(row.item, row.item.isEnabled)"
                    :title="`Tag ${ row.item.isEnabled ? 'de' : '' }aktivieren`"
                    class="cursor-pointer"
                >
                    <mdicon
                        v-if="row.item.isEnabled"
                        name="check"
                        class="text-success"
                    />
                    <mdicon
                        v-else
                        name="TagOff"
                        class="text-info"
                        disabled
                    />
                </div>
            </template>
            <template v-slot:cell(color)="data">
                <color-badge
                    :color="data.item.color"
                />
            </template>

            <template v-slot:cell(actions)="row">
                <b-button
                    size="sm"
                    @click="toggleEnabled(row.item, row.item.isEnabled)"
                >
                    {{ row.item.isEnabled ? 'deaktivieren' : 'aktivieren' }}
                </b-button>
                <span :id="`questionHeaderId-${row.item.id}`">
                    <mdicon
                        name="help-circle-outline"
                        class="text-muted"
                    />
                </span>
                <b-popover
                    :target="`questionHeaderId-${row.item.id}`"
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
        </b-table>
    </div>
</template>

<script>
'use strict';
import ColorBadge from './ColorBadge.vue';
import ContentLoadingSpinner from '../ContentLoadingSpinner.vue';
import MyInputGroupAppend from '../../components/Common/MyInputGroupAppend.vue';
import { useClientStore } from '../../stores/client';

export default {
    name: 'TagList',
    components: { ContentLoadingSpinner, ColorBadge, MyInputGroupAppend },
    data: function () {
        return {
            clientStore: useClientStore(),
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
            return [
                {
                    key: 'name',
                    sortable: true,
                },
                {
                    key: 'color',
                    label: 'Farbe',
                    sortable: true,
                },
                {
                    key: 'isEnabled',
                    label: 'Tag aktiviert?',
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
                { key: 'actions', label: 'Aktionen' },
            ];
        },
        availableClients() {
            return this.clientStore.getClients;
        },
        tags() {
            return this.$store.getters['tag/tags']
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
            return this.$store.getters['tag/isLoading'] || this.clientStore.isLoading;
        },
        error() {
            return this.$store.getters['tag/error'];
        },
        isSuperAdmin() {
            return this.$store.getters['security/isSuperAdmin'];
        },
    },
    async created() {
        await Promise.all([
            this.$store.dispatch('tag/findAll'),
            this.clientStore.fetchClients(),
        ]);
    },
    methods: {
        isLoadingToggleTagState(userUri) {
            return this.$store.getters['tag/isLoadingToggleTagState'](userUri);
        },
        clientFormatter(clientIri) {
            return this.clientStore.getClientByIri(clientIri).name;
        },
        toggleEnabled: function (tag, isEnabled) {
            let changedTag, message, title;
            if (isEnabled) {
                changedTag = this.$store.dispatch('tag/disable', { tag: tag['@id'] });
                message = `Der Tag "${tag.name}" wurde erfolgreich deaktiviert. Er kann nun nicht mehr zu einem Wegpunkt hinzugefügt verwendet.`;
                title = `Tag deaktiviert`;
            } else {
                changedTag = this.$store.dispatch('tag/enable', { tag: tag['@id'] });
                message = `Der Tag "${tag.name}" wurde erfolgreich aktiviert. Er kann nun zu einem Wegpunkt hinzugefügt verwendet.`;
                title = `Tag aktiviert`;
            }
            if (changedTag) {
                this.$bvToast.toast(message, {
                    title: title,
                    toaster: 'b-toaster-top-right',
                    autoHideDelay: 10000,
                    appendToast: true,
                    variant: 'info',
                    solid: true,
                });
            } else {
                this.$bvToast.toast('Upps! :-(', {
                    title: `Tag ${ isEnabled ? 'deaktivieren' : 'aktivieren'} fehlgeschlagen`,
                    toaster: 'b-toaster-top-right',
                    autoHideDelay: 10000,
                    appendToast: true,
                    variant: 'danger',
                    solid: true,
                });
            }
        },
    },
};
</script>

<style scoped lang="scss">
</style>
