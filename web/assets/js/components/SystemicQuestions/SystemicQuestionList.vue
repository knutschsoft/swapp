<template>
    <div>
        <v-data-table
            :items="systemicQuestions"
            :headers="fields"
            :loading="isLoading"
            :loading-text="loadingText"
            :items-per-page="itemsPerPage"
            :items-per-page-options="itemsPerPageOptions"
            :items-per-page-text="itemsPerPageText"
            :no-data-text="noItemsText"
            :no-results-text="noItemsText"
            small
            striped
            class="mb-0"
            stacked="sm"
        >
            <template v-slot:item.isEnabled="{item}">
                <span
                    @click="toggleEnabled(item['@id'], item.isEnabled)"
                    class="cursor-pointer"
                >
                    <mdicon
                        v-if="item.isEnabled"
                        name="TagOutline"
                        class="text-success"
                    />
                    <mdicon
                        v-else
                        name="TagOffOutline"
                        class="text-warning"
                    />
                </span>
            </template>
            <template v-slot:item.client="{item}">
                {{ clientFormatter(item.client) }}
            </template>
            <template v-slot:item.createdAt="{item}">
                {{ formatDateTime(item.createdAt) }}
            </template>
            <template v-slot:item.updatedAt="{item}">
                {{ formatDateTime(item.updatedAt) }}
            </template>
            <template v-slot:item.actions="{item}">
                <v-row justify="center">
                    <v-btn
                        small
                        color="secondary"
                        @click.stop="openSystemicQuestionEditDialog(item)"
                    >
                        Systemische Frage<br>
                        bearbeiten
                        <v-icon
                            small
                            class="ml-2"
                        >
                            mdi-pencil-outline
                        </v-icon>
                    </v-btn>
                </v-row>
            </template>
        </v-data-table>

        <v-dialog
            v-model="dialog"
            scrollable
            max-width="800px"
        >
            <v-card>
                <v-card-title class="text-h5 grey lighten-2">
                    Systemische Frage ändern
                </v-card-title>
                <v-card-text>
                    <systemic-question-form
                        v-if="editSystemicQuestion"
                        submit-button-text="Speichern"
                        :initial-client="editSystemicQuestion.client"
                        :initial-question="editSystemicQuestion.question"
                        @submit="handleSubmit"
                    />
                </v-card-text>
            </v-card>
        </v-dialog>
    </div>
</template>

<script>
'use strict';
import SystemicQuestionForm from './SystemicQuestionForm.vue';
import { useAlertStore, useAuthStore, useClientStore, useSystemicQuestionStore, useTeamStore } from '../../stores';
import {
    formatDateTime,
    itemsPerPageOptions,
    itemsPerPageText,
    loadingText,
    noItemsText,
} from '../../utils'

export default {
    name: 'SystemicQuestionList',
    components: {
        SystemicQuestionForm,
    },
    data: function () {
        return {
            alertStore: useAlertStore(),
            authStore: useAuthStore(),
            clientStore: useClientStore(),
            teamStore: useTeamStore(),
            systemicQuestionStore: useSystemicQuestionStore(),
            editSystemicQuestion: {},
            dialog: false,
            itemsPerPageText,
            itemsPerPageOptions,
            itemsPerPage: -1,
            loadingText,
            noItemsText,
        };
    },
    computed: {
        fields() {
            let headers = [
                {
                    value: 'question',
                    text: 'Fragestellung',
                    sortable: true,
                },
                {
                    value: 'isEnabled',
                    text: 'Ist aktiv?',
                    sortable: true,
                },
            ];
            if (!this.isSuperAdmin) {
                headers.push({
                    value: 'client',
                    text: 'Klient',
                    sortable: false,
                });
                headers.push({
                    value: 'createdAt',
                    text: 'Erstellt am',
                    sortable: true,
                });
                headers.push({
                    value: 'updatedAt',
                    text: 'Geändert am',
                    sortable: true,
                })
            }
            headers.push({ value: 'actions', text: 'Aktionen' });

            return headers;
        },
        systemicQuestions() {
            return this.systemicQuestionStore.systemicQuestions;
        },
        isLoading() {
            return this.systemicQuestionStore.isLoading;
        },
        error() {
            return this.systemicQuestionStore.getErrors;
        },
        isSuperAdmin() {
            return this.authStore.isSuperAdmin;
        },
    },
    async created() {
        await Promise.all([
            this.systemicQuestionStore.fetchSystemicQuestions(),
            this.teamStore.fetchTeams(),
        ]);
    },
    methods: {
        clientFormatter(clientIri) {
            return this.clientStore.getClientByIri(clientIri)?.name;
        },
        formatDateTime(dateTime) {
            return formatDateTime(dateTime);
        },
        openSystemicQuestionEditDialog(systemicQuestion) {
            this.editSystemicQuestion = systemicQuestion;
            this.dialog = true;
        },
        resetEditModalSystemicQuestion() {
            this.dialog = false;
        },
        async toggleEnabled(iri, isEnabled) {
            if (isEnabled) {
                const systemicQuestion = await this.systemicQuestionStore.disable({ systemicQuestion: iri });
                const message = `Die systemische Frage "${systemicQuestion.question}" wurde erfolgreich deaktiviert. Sie wird nun nicht mehr automatisch für neue Runden verwendet.`;
                this.alertStore.info(message, 'Systemische Frage geändert');
            } else {
                const systemicQuestion = await this.systemicQuestionStore.enable({ systemicQuestion: iri });
                const message = `Die systemische Frage "${systemicQuestion.question}" wurde erfolgreich aktiviert. Sie wird nun automatisch für neue Runden verwendet.`;
                this.alertStore.info(message, 'Systemische Frage geändert');
            }
        },
        async handleSubmit(payload) {
            payload.systemicQuestion = this.editSystemicQuestion['@id'];
            const systemicQuestion = await this.systemicQuestionStore.change(payload);
            if (systemicQuestion) {
                const message = `Die systemische Frage "${systemicQuestion.question}" wurde erfolgreich geändert.`;
                this.alertStore.success(message, 'Systemische Frage geändert');

                this.resetEditModalSystemicQuestion();
            } else {
                this.alertStore.error('Systemische Frage ändern fehlgeschlagen', 'Upps! :-(');
            }
        },
    },
};
</script>

<style scoped lang="scss">
</style>
