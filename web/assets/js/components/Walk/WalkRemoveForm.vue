<template>
    <v-form
        @submit.prevent.stop="handleRemove"
        class="p-1 p-sm-2 p-lg-3"
    >
        Wenn die Runde gelöscht wurde, kann dies nicht wieder rückgängig gemacht werden. Bitte sei dir sicher.
        <v-btn
            color="error"
            v-b-modal.modal-remove
            data-test="button-walk-remove"
            :disabled="isLoading"
            block
            @click="dialog = true"
        >
            Runde löschen und zum Dashboard zurückkehren
        </v-btn>
        <global-form-error
            :error="globalErrors"
        />
        <v-dialog
            v-model="dialog"
        >
            <v-card>
                <v-card-title>
                    Bist du dir absolut sicher?
                </v-card-title>
                <v-card-text>
                    <v-alert
                        type="warning"
                    >
                        Unerwartete Dinge können passieren, wenn du dies nicht liest.
                    </v-alert>
                    <p>
                        Diese Aktion kann <b>nicht</b> rückgängig gemacht werden.
                        Dies wird permanent die Runde <b>{{ initialWalk.name }}</b> und der ihr zugeordneten Wegpunkte (inklusive deren Bilder und Tags) löschen.
                    </p>
                    <p>
                        Bitte gib <b>{{ initialWalk.name }}</b> ein um das Löschen zu bestätigen.
                    </p>
                    <v-text-field
                        v-model="walkName"
                        type="text"
                        dense
                        outlined
                        label="Name der Runde"
                        data-test="walkName"
                        autocomplete="off"
                        :disabled="isLoading"
                    />
                    <v-alert
                        v-if="false === walkNameState"
                        type="error"
                    >
                        {{ invalidWalkNameFeedback }}
                    </v-alert>
                    <v-btn
                        type="submit"
                        color="error"
                        :disabled="isSubmitDisabled"
                        data-test="button-walk-remove-modal"
                        @click="handleRemove"
                        block
                    >
                        Ich verstehe die Auswirkungen; Runde löschen und zum Dashboard zurückkehren
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
            walkStore: useWalkStore(),
            walkName: '',
            dialog: false,
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
            return this.walkStore.isLoading;
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
