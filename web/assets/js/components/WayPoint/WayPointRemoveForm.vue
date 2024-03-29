<template>
    <b-form
        @submit.prevent.stop="handleRemove"
        class="p-1 p-sm-2 p-lg-3"
    >
        Wenn der Wegpunkt gelöscht wurde, kann dies nicht wieder rückgängig gemacht werden. Bitte sei dir sicher.
        <b-form-group>
            <b-button
                variant="danger"
                v-b-modal.modal-remove
                data-test="button-way-point-remove"
                :disabled="isLoading"
                block
                class="col-12"
            >
                Wegpunkt löschen und zur Runde zurückkehren
            </b-button>
        </b-form-group>
        <global-form-error
            :error="globalErrors"
        />
        <b-modal
            id="modal-remove"
            title="Bist du dir absolut sicher?"
            hide-footer
            centered
            size="lg"
        >
            <b-alert
                variant="warning"
                show
            >
                Unerwarte Dinge können passieren, wenn du dies nicht liest.
            </b-alert>
            <p>
            Diese Aktion kann <b>nicht</b> rückgängig gemacht werden.
            Dies wird permanent den Wegpunkt <b>{{ initialWayPoint.locationName }}</b> und der an ihm gespeicherten Tags löschen. Die Runde {{ initialWalk.name }} bleibt erhalten.
            </p>
            <p>
                Bitte gib <b>{{ initialWayPoint.locationName }}</b> ein um das Löschen zu bestätigen.
            </p>
            <b-form-group
                label=""
                :state="wayPointNameState"
                :invalid-feedback="invalidWayPointNameFeedback"
            >
                <b-input-group>
                    <b-input
                        v-model="wayPointName"
                        type="text"
                        data-test="wayPointName"
                        autocomplete="off"
                        :disabled="isLoading"
                    />
                </b-input-group>
            </b-form-group>
            <b-button
                type="submit"
                variant="danger"
                :disabled="isSubmitDisabled"
                data-test="button-way-point-remove-modal"
                @click="handleRemove"
                block
                class="col-12"
            >
                Ich verstehe die Auswirkungen; Wegpunkt löschen und zur Runde zurückkehren
            </b-button>
        </b-modal>
    </b-form>
</template>

<script>
'use strict';
import GlobalFormError from '../Common/GlobalFormError.vue';
import getViolationsFeedback from '../../utils/validation.js';
import { useTagStore } from '../../stores/tag';
import { useTeamStore } from '../../stores/team';
import { useWayPointStore } from '../../stores/way-point';
import { useWalkStore } from '../../stores/walk';
import { useAuthStore } from '../../stores/auth';

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
            tagStore: useTagStore(),
            teamStore: useTeamStore(),
            wayPointStore: useWayPointStore(),
            walkStore: useWalkStore(),
            wayPointName: '',
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
            return this.wayPointStore.isLoading
                || this.walkStore.isLoading
                || this.tagStore.isLoading
                || this.teamStore.isLoading;
        },
        currentUser() {
            return this.authStore.currentUser;
        },
        isSuperAdmin() {
            return this.authStore.isSuperAdmin;
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
