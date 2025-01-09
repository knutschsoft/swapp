<template>
    <v-form
        @submit.prevent.stop="handleRemove"
        class="p-1 p-sm-2 p-lg-3"
    >
        Wenn der Wegpunkt gelöscht wurde, kann dies nicht wieder rückgängig gemacht werden. Bitte sei dir sicher.
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
                <v-card-text>
                    <v-alert
                        type="warning"
                    >
                        Unerwartete Dinge können passieren, wenn du dies nicht liest.
                    </v-alert>
                    <p>
                        Diese Aktion kann <b>nicht</b> rückgängig gemacht werden.
                        Dies wird permanent den Wegpunkt <b>{{ initialWayPoint.locationName }}</b> und der an ihm gespeicherten Tags löschen. Die Runde {{ initialWalk.name }} bleibt erhalten.
                    </p>
                    <p>
                        Bitte gib <b>{{ initialWayPoint.locationName }}</b> ein um das Löschen zu bestätigen.
                    </p>
                    <v-text-field
                        v-model="wayPointName"
                        type="text"
                        dense
                        outlined
                        label="Name des Wegpunktes"
                        clearable
                        data-test="wayPointName"
                        autocomplete="off"
                        :disabled="isLoading"
                    />
                    <v-alert
                        v-if="false === wayPointNameState"
                        type="error"
                    >
                        {{ invalidWayPointNameFeedback }}
                    </v-alert>
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
            return getViolationsFeedback(['wayPoint'], this.error);
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
