<template>
    <div>
        <content-loading-spinner
            :is-loading="isLoading"
        />
        <v-data-table
            v-show="!isLoading && systemicQuestions.length"
            :items="systemicQuestions"
            :headers="fields"
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
            <template v-slot:item.actions="{item}">
                <v-btn
                    small
                    color="secondary"
                    @click="editSystemicQuestion(item)"
                >
                    Systemische Frage<br>
                    bearbeiten
                    <v-icon
                        small
                    >
                        mdi-pencil-outline
                    </v-icon>
                </v-btn>
            </template>
        </v-data-table>

        <b-modal
            :id="editModalSystemicQuestion.id"
            :title="editModalSystemicQuestion.title"
            size="lg"
            @hide="resetEditModalSystemicQuestion"
            title="Systemische Frage ändern"
            hide-footer
        >
            <systemic-question-form
                v-if="editModalSystemicQuestion.selectedSystemicQuestion"
                submit-button-text="Speichern"
                :initial-client="editModalSystemicQuestion.selectedSystemicQuestion.client"
                :initial-question="editModalSystemicQuestion.selectedSystemicQuestion.question"
                @submit="handleSubmit"
            />
        </b-modal>
    </div>
</template>

<script>
'use strict';
import ContentLoadingSpinner from '../ContentLoadingSpinner.vue';
import dayjs from 'dayjs';
import SystemicQuestionForm from './SystemicQuestionForm.vue';
import { useAuthStore, useClientStore, useSystemicQuestionStore, useTeamStore } from '../../stores';

export default {
    name: 'SystemicQuestionList',
    components: {
        SystemicQuestionForm,
        ContentLoadingSpinner,
    },
    data: function () {
        return {
            authStore: useAuthStore(),
            clientStore: useClientStore(),
            teamStore: useTeamStore(),
            systemicQuestionStore: useSystemicQuestionStore(),
            editModalSystemicQuestion: {
                id: 'edit-modal-systemic-question',
                title: '',
                selectedSystemicQuestion: null,
            },
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
            if (this.isSuperAdmin) {
                headers.push({
                    value: 'client',
                    text: 'Klient',
                    sortable: true,
                    sortByFormatted: true,
                    formatter: this.clientFormatter,
                });
                headers.push({
                    value: 'createdAt',
                    text: 'Erstellt am',
                    sortable: true,
                    sortByFormatted: false,
                    formatter: (value) => {
                        return dayjs(value).format('DD.MM.YYYY HH:mm:ss');
                    },
                });
                headers.push({
                    value: 'updatedAt',
                    text: 'Geändert am',
                    sortable: true,
                    sortByFormatted: false,
                    formatter: (value) => {
                        return dayjs(value).format('DD.MM.YYYY HH:mm:ss');
                    },
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
        editSystemicQuestion(systemicQuestion) {
            this.$root.$emit('bv::show::modal', this.editModalSystemicQuestion.id);
            this.editModalSystemicQuestion.selectedSystemicQuestion = systemicQuestion;
        },
        resetEditModalSystemicQuestion() {
            this.$root.$emit('bv::hide::modal', this.editModalSystemicQuestion.id);
            this.editModalSystemicQuestion.systemicQuestion = null;
        },
        async toggleEnabled(iri, isEnabled) {
            if (isEnabled) {
                const systemicQuestion = await this.systemicQuestionStore.disable({ systemicQuestion: iri });
                const message = `Die systemische Frage "${systemicQuestion.question}" wurde erfolgreich deaktiviert. Sie wird nun nicht mehr automatisch für neue Runden verwendet.`;
                this.$bvToast.toast(message, {
                    title: 'Systemische Frage geändert',
                    toaster: 'b-toaster-top-right',
                    autoHideDelay: 10000,
                    appendToast: true,
                    variant: 'info',
                    solid: true,
                });
            } else {
                const systemicQuestion = await this.systemicQuestionStore.enable({ systemicQuestion: iri });
                const message = `Die systemische Frage "${systemicQuestion.question}" wurde erfolgreich aktiviert. Sie wird nun automatisch für neue Runden verwendet.`;
                this.$bvToast.toast(message, {
                    title: 'Systemische Frage geändert',
                    toaster: 'b-toaster-top-right',
                    autoHideDelay: 10000,
                    appendToast: true,
                    variant: 'info',
                    solid: true,
                });
            }
        },
        async handleSubmit(payload) {
            payload.systemicQuestion = this.editModalSystemicQuestion.selectedSystemicQuestion['@id'];
            const systemicQuestion = await this.systemicQuestionStore.change(payload);
            if (systemicQuestion) {
                const message = `Die systemische Frage "${systemicQuestion.question}" wurde erfolgreich geändert.`;
                this.$bvToast.toast(message, {
                    title: 'Systemische Frage geändert',
                    toaster: 'b-toaster-top-right',
                    autoHideDelay: 10000,
                    variant: 'info',
                    appendToast: true,
                    solid: true,
                });

                this.resetEditModalSystemicQuestion();
            } else {
                this.$bvToast.toast('Upps! :-(', {
                    title: 'Systemische Frage ändern fehlgeschlagen',
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
