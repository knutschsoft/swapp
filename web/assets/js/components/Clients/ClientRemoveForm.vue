<template>
    <v-form
        @submit.prevent="handleRemove"
        class="pa-1 pa-sm-2 pa-md-4 pa-lg-5 pa-xl-6 pa-xxl-7"
    >
        <p class="mb-2">
            Wenn der Klient gelöscht wurde, kann dies <b>nicht</b> wieder rückgängig gemacht werden. Bitte sei dir sicher.
        </p>
        <v-btn
            color="error"
            data-test="button-client-remove"
            :disabled="isLoading"
            block
            class="text-transform-none"
            density="comfortable"
            @click="dialog = true"
        >
            Klient und alle zugehörigen Daten löschen
        </v-btn>
        <global-form-error
            :error="globalErrors"
        />
        <v-dialog
            v-model="dialog"
            scrollable
            max-width="800px"
        >
            <v-card>
                <v-card-title>
                    Bist du dir absolut sicher?
                </v-card-title>
                <v-divider />
                <v-card-text>
                    <v-alert
                        type="warning"
                        prominent
                    >
                        Unerwartete Dinge können passieren, wenn du dies nicht liest.
                        <ul class="pl-5 mt-2">
                            <li>Diese Aktion kann <b>nicht</b> rückgängig gemacht werden.</li>
                            <li>
                                Dies wird permanent den Klienten <b>{{ initialClient.name }}</b> und
                                <b>alle</b> zugehörigen Daten löschen:
                            </li>
                        </ul>
                    </v-alert>

                    <!-- Übersicht: was genau gelöscht wird -->
                    <div class="my-3">
                        <p class="mb-1"><b>Inkl.:</b></p>
                        <ul class="pl-5">
                            <li>
                                <b>{{ usersCount }}</b> Benutzer{{ usersCount === 1 ? '' : '' }}
                                <small v-if="userPreview" class="text-muted d-block">{{ userPreview }}</small>
                            </li>
                            <li>
                                <b>{{ teamsCount }}</b> Team{{ teamsCount === 1 ? '' : 's' }}
                                <small v-if="teamPreview" class="text-muted d-block">{{ teamPreview }}</small>
                            </li>
                            <li><b>alle Runden</b> des Klienten – mit Reflexionen, Tageskonzepten, Bewertungen</li>
                            <li><b>alle Wegpunkte</b> dieser Runden – inklusive aller hochgeladenen <b>Bilder</b></li>
                            <li><b>alle Tags</b> des Klienten – inklusive der Verknüpfungen zu Wegpunkten</li>
                            <li><b>alle systemischen Fragen</b> des Klienten</li>
                            <li>das <b>Bewertungsbild</b> des Klienten</li>
                        </ul>
                    </div>

                    <p class="my-2">
                        Bitte gib <b>{{ initialClient.name }}</b> ein um das Löschen zu bestätigen.
                    </p>
                    <v-text-field
                        v-model="clientName"
                        type="text"
                        density="compact"
                        variant="outlined"
                        label="Name des Klienten"
                        data-test="clientName"
                        autocomplete="off"
                        :disabled="isLoading"
                        :error="false === clientNameState"
                        :error-messages="false === clientNameState ? invalidClientNameFeedback : null"
                    />
                    <v-btn
                        type="submit"
                        color="error"
                        :disabled="isSubmitDisabled"
                        data-test="button-client-remove-modal"
                        @click="handleRemove"
                        block
                        class="text-transform-none"
                        density="comfortable"
                        :loading="isLoading"
                    >
                        Ich verstehe die Auswirkungen; Klient endgültig löschen
                    </v-btn>
                </v-card-text>
            </v-card>
        </v-dialog>
    </v-form>
</template>

<script lang="ts">
'use strict';
import GlobalFormError from '../Common/GlobalFormError.vue';
import { getViolationsFeedback } from '../../utils';
import { useClientStore, useUserStore, useTeamStore } from '../../stores';

export default {
    name: 'ClientRemoveForm',
    props: {
        initialClient: {
            type: Object,
            required: true,
        },
    },
    components: {
        GlobalFormError,
    },
    data: function () {
        return {
            clientStore: useClientStore(),
            userStore: useUserStore(),
            teamStore: useTeamStore(),
            clientName: '',
            dialog: false,
        };
    },
    computed: {
        error() {
            return this.clientStore.getErrors.remove;
        },
        clientNameState() {
            if (!this.clientName) {
                return null;
            }

            return this.clientName === this.initialClient.name;
        },
        invalidClientNameFeedback() {
            return getViolationsFeedback(['client'], this.error)
                || 'Der eingegebene Name stimmt nicht mit dem Namen des Klienten überein';
        },
        isLoading() {
            return this.clientStore.isLoadingRemove(this.initialClient['@id']);
        },
        isSubmitDisabled() {
            return this.isLoading || !this.clientNameState;
        },
        globalErrors() {
            return getViolationsFeedback(['client'], this.error, true);
        },
        usersCount() {
            return this.initialClient.users?.length ?? 0;
        },
        teamsCount() {
            return this.initialClient.teams?.length ?? 0;
        },
        userPreview() {
            const iris = this.initialClient.users ?? [];
            const names = iris
                .slice(0, 5)
                .map((iri: string) => this.userStore.getUserByIri(iri)?.username)
                .filter((n: string | undefined): n is string => Boolean(n));
            if (!names.length) return '';
            const more = iris.length > names.length ? ` und ${iris.length - names.length} weitere` : '';
            return `${names.join(', ')}${more}`;
        },
        teamPreview() {
            const iris = this.initialClient.teams ?? [];
            const names = iris
                .slice(0, 5)
                .map((iri: string) => this.teamStore.getTeamByIri?.(iri)?.name)
                .filter((n: string | undefined): n is string => Boolean(n));
            if (!names.length) return '';
            const more = iris.length > names.length ? ` und ${iris.length - names.length} weitere` : '';
            return `${names.join(', ')}${more}`;
        },
    },
    async mounted() {
        await this.clientStore.resetRemoveError();
        this.clientName = '';
    },
    methods: {
        async handleRemove() {
            if (!this.clientNameState || this.isLoading) return;
            this.$emit('remove', { client: this.initialClient });
        },
    },
};
</script>

<style scoped lang="scss">
</style>
