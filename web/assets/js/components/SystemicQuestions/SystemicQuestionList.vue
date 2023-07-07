<template>
    <div>
        <content-loading-spinner
            :is-loading="isLoading"
        />
        <b-table
            v-show="!isLoading && systemicQuestions.length"
            :items="systemicQuestions"
            :fields="fields"
            small
            striped
            class="mb-0"
            stacked="sm"
        >
            <template v-slot:cell(isEnabled)="row">
                <span
                    @click="toggleEnabled(row.item['@id'], row.item.isEnabled)"
                    class="cursor-pointer"
                >
                    <mdicon
                        v-if="row.item.isEnabled"
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
            <template v-slot:cell(actions)="row">
                <b-button
                    size="sm"
                    @click="editSystemicQuestion(row.item)"
                >
                    Systemische Frage<br>
                    bearbeiten
                    <b-icon-pencil />
                </b-button>
            </template>
        </b-table>

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

export default {
    name: 'SystemicQuestionList',
    components: {
        SystemicQuestionForm,
        ContentLoadingSpinner,
    },
    data: function () {
        return {
            editModalSystemicQuestion: {
                id: 'edit-modal-systemic-question',
                title: '',
                selectedSystemicQuestion: null,
            },
        };
    },
    computed: {
        fields() {
            return [
                {
                    key: 'question',
                    label: 'Fragestellung',
                    sortable: true,
                },
                {
                    key: 'isEnabled',
                    label: 'Ist aktiv?',
                    sortable: true,
                },
                {
                    key: 'client',
                    label: 'Klient',
                    sortable: true,
                    sortByFormatted: true,
                    class: !this.isSuperAdmin ? 'd-none' : '',
                    formatter: this.clientFormatter,
                },
                {
                    key: 'createdAt',
                    label: 'Erstellt am',
                    sortable: true,
                    sortByFormatted: false,
                    class: !this.isSuperAdmin ? 'd-none' : '',
                    formatter: (value) => {
                        return dayjs(value).format('DD.MM.YYYY HH:mm:ss');
                    },
                },
                {
                    key: 'updatedAt',
                    label: 'Geändert am',
                    sortable: true,
                    sortByFormatted: false,
                    class: !this.isSuperAdmin ? 'd-none' : '',
                    formatter: (value) => {
                        return dayjs(value).format('DD.MM.YYYY HH:mm:ss');
                    },
                },
                { key: 'actions', label: 'Aktionen' },
            ];
        },
        systemicQuestions() {
            return this.$store.getters['systemicQuestion/systemicQuestions'];
        },
        isLoading() {
            return this.$store.getters['systemicQuestion/isLoading'];
        },
        error() {
            return this.$store.getters['systemicQuestion//error'];
        },
        isSuperAdmin() {
            return this.$store.getters['security/isSuperAdmin'];
        },
    },
    async created() {
        await Promise.all([
            this.$store.dispatch('systemicQuestion/findAll'),
            this.$store.dispatch('team/findAll'),
        ]);
    },
    methods: {
        clientFormatter(clientIri) {
            return this.$store.getters['client/getClientByIri'](clientIri).name;
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
                const systemicQuestion = await this.$store.dispatch('systemicQuestion/disable', { systemicQuestion: iri });
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
                const systemicQuestion = await this.$store.dispatch('systemicQuestion/enable', { systemicQuestion: iri });
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
            const systemicQuestion = await this.$store.dispatch('systemicQuestion/change', payload);
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
