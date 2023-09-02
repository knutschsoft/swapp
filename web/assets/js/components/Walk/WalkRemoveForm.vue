<template>
    <b-form
        @submit.prevent.stop="handleRemove"
        class="p-1 p-sm-2 p-lg-3"
    >
        Wenn die Runde gelöscht wurde, kann dies nicht wieder rückgängig gemacht werden. Bitte sei dir sicher.
        <b-form-group>
            <b-button
                variant="danger"
                v-b-modal.modal-remove
                data-test="button-walk-remove"
                :disabled="isLoading"
                block
                class="col-12"
            >
                Runde löschen und zum Dashboard zurückkehren
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
                Dies wird permanent die Runde <b>{{ initialWalk.name }}</b> und der ihr zugeordneten Wegpunkte (inklusive deren Bilder und Tags) löschen.
            </p>
            <p>
                Bitte gib <b>{{ initialWalk.name }}</b> ein um das Löschen zu bestätigen.
            </p>
            <b-form-group
                label=""
                :state="walkNameState"
                :invalid-feedback="invalidWalkNameFeedback"
            >
                <b-input-group>
                    <b-input
                        v-model="walkName"
                        type="text"
                        data-test="walkName"
                        autocomplete="off"
                        :disabled="isLoading"
                    />
                </b-input-group>
            </b-form-group>
            <b-button
                type="submit"
                variant="danger"
                :disabled="isSubmitDisabled"
                data-test="button-walk-remove-modal"
                @click="handleRemove"
                block
                class="col-12"
            >
                Ich verstehe die Auswirkungen; Runde löschen und zum Dashboard zurückkehren
            </b-button>
        </b-modal>
    </b-form>
</template>

<script>
'use strict';
import GlobalFormError from '../Common/GlobalFormError.vue';
import getViolationsFeedback from '../../utils/validation.js';
import { useTagStore } from '../../stores/tag';
import { useWayPointStore } from '../../stores/way-point';
import { useTeamStore } from '../../stores/team';
import { useWalkStore } from '../../stores/walk';

export default {
    name: 'WalkRemoveForm',
    props: {
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
            tagStore: useTagStore(),
            teamStore: useTeamStore(),
            walkStore: useWalkStore(),
            wayPointStore: useWayPointStore(),
            walkName: '',
        };
    },
    computed: {
        error() {
            return this.walkStore.getErrors.change;
        },
        walkNameState() {
            if (!this.walkName) {
                return null;
            }

            return this.walkName === this.initialWalk.name;
        },
        invalidWalkNameFeedback() {
            return getViolationsFeedback(['walk'], this.error);
        },
        isLoading() {
            return this.walkStore.isLoading
                || this.wayPointStore.isLoading
                || this.tagStore.isLoading
                || this.teamStore.isLoading;
        },
        isSubmitDisabled() {
            return this.isLoading || !this.walkNameState;
        },
        globalErrors() {
            let keys = ['walk'];

            return getViolationsFeedback(keys, this.error, true);
        },
    },
    watch: {
    },
    async mounted() {
        await this.walkStore.resetChangeError();
        this.walkName = '';
    },
    methods: {
        async handleRemove() {
            this.$emit('remove', { walk: this.initialWalk });
        },
    },
};
</script>

<style scoped lang="scss">
</style>
