<template>
    <v-form
        @submit.prevent="handleRemove"
        class="pa-1 pa-sm-2 pa-md-4 pa-lg-5 pa-xl-6 pa-xxl-7"
    >
        <p class="mb-2">
            Wenn der Wegpunkt gelöscht wurde, kann dies nicht wieder rückgängig gemacht werden. Bitte sei dir sicher.
        </p>
        <v-btn
            color="error"
            data-test="button-way-point-remove"
            :disabled="isLoading"
            block
            @click="dialog = true"
        >
            Wegpunkt löschen und zur Runde zurückkehren
        </v-btn>
        <global-form-error
            :error="globalErrors"
        />
        <v-dialog
            v-model="dialog"
        >
            <v-card>
                <v-card-title>Bist du dir absolut sicher?</v-card-title>
                <v-divider />
                <v-card-text>
                    <v-alert
                        type="warning"
                        prominent
                    >
                        Unerwartete Dinge können passieren, wenn du dies nicht liest.
                        <ul class="pl-5 mt-2">
                            <li>Diese Aktion kann <b>nicht</b> rückgängig gemacht werden.</li>
                            <li>Dies wird permanent den Wegpunkt <b>{{ initialWayPoint.locationName }}</b> und der an ihm gespeicherten Tags löschen.</li>
                            <li> Die Runde {{ initialWalk.name }} bleibt erhalten.</li>
                        </ul>
                    </v-alert>
                    <p class="my-2">
                        Bitte gib <b>{{ initialWayPoint.locationName }}</b> ein um das Löschen zu bestätigen.
                    </p>
                    <v-text-field
                        v-model="wayPointName"
                        type="text"
                        density="compact"
                        variant="outlined"
                        label="Name des Wegpunktes"
                        clearable
                        data-test="wayPointName"
                        autocomplete="off"
                        :disabled="isLoading"
                        :error="false === wayPointNameState"
                        :error-messages="false === wayPointNameState ? invalidWayPointNameFeedback : null"
                    />
                    <v-btn
                        type="submit"
                        color="error"
                        :disabled="isSubmitDisabled"
                        data-test="button-way-point-remove-modal"
                        @click="handleRemove"
                        block
                    >
                        Ich verstehe die Auswirkungen; Wegpunkt löschen und zur Runde zurückkehren
                    </v-btn>
                </v-card-text>
            </v-card>
        </v-dialog>
    </v-form>
</template>

<script>
'use strict';
import GlobalFormError from '../Common/GlobalFormError.vue';
import { getViolationsFeedback } from '../../utils';
import { useAuthStore, useWalkStore, useWayPointStore } from '../../stores';

export default {
    name: 'WayPointRemoveForm',
    props: {
        initialWayPoint: {
            type: Object,
            required: true,
        },
        initialWalk: {
            type: Object,
            required: true,
        },
        submitButtonText: {
            type: String,
            required: true,
        },
    },
    components: {
        GlobalFormError,
    },
    data: function () {
        return {
            authStore: useAuthStore(),
            wayPointStore: useWayPointStore(),
            walkStore: useWalkStore(),
            wayPointName: '',
            dialog: false,
        };
    },
    computed: {
        error() {
            return this.wayPointStore.getErrors.change;
        },
        walk() {
            return this.initialWalk ? this.initialWalk : this.walkStore.getWalkByIri(this.initialWayPoint.walk);
        },
        wayPointNameState() {
            if (!this.wayPointName) {
                return null;
            }

            return this.wayPointName === this.initialWayPoint.locationName;
        },
        invalidWayPointNameFeedback() {
            return getViolationsFeedback(['wayPoint'], this.error) || 'Der eingegebene Name stimmt nicht mit dem Namen des Wegpunktes überein';
        },
        isLoading() {
            return this.wayPointStore.isLoading;
        },
        isSubmitDisabled() {
            return this.isLoading || !this.wayPointNameState;
        },
        globalErrors() {
            let keys = ['wayPoint'];

            return getViolationsFeedback(keys, this.error, true);
        },
    },
    watch: {
    },
    async mounted() {
        await this.wayPointStore.resetChangeError();
        this.wayPointName = '';
        if (!this.walk && this.initialWayPoint) {
            await this.walkStore.fetchByIri(this.initialWayPoint.walk);
        }
    },
    methods: {
        async handleRemove() {
            this.$emit('remove', { wayPoint: this.initialWayPoint });
        },
    },
};
</script>

<style scoped lang="scss">
</style>
