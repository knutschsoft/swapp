<template>
    <v-form
        @submit.prevent="handleRemove"
        class="pa-1 pa-sm-2 pa-md-4 pa-lg-5 pa-xl-6 pa-xxl-7"
    >
        <p class="mb-2">
            Wenn die Runde gelöscht wurde, kann dies nicht wieder rückgängig gemacht werden. Bitte sei dir sicher.
        </p>
        <v-btn
            color="error"
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
                <v-divider />
                <v-card-text>
                    <v-alert
                        type="warning"
                        prominent
                    >
                        Unerwartete Dinge können passieren, wenn du dies nicht liest.
                        <ul class="pl-5 mt-2">
                            <li>Diese Aktion kann <b>nicht</b> rückgängig gemacht werden.</li>
                            <li>Dies wird permanent die Runde <b>{{ initialWalk.name }}</b> und der ihr zugeordneten Wegpunkte (inklusive deren Bilder und Tags) löschen.</li>
                        </ul>
                    </v-alert>
                    <p class="my-2">
                        Bitte gib <b>{{ initialWalk.name }}</b> ein um das Löschen zu bestätigen.
                    </p>
                    <v-text-field
                        v-model="walkName"
                        type="text"
                        density="compact"
                        variant="outlined"
                        label="Name der Runde"
                        data-test="walkName"
                        autocomplete="off"
                        :disabled="isLoading"
                        :error="false === walkNameState"
                        :error-messages="false === walkNameState ? invalidWalkNameFeedback : null"
                    />
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
import { useWalkStore } from '../../stores';

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
            return getViolationsFeedback(['walk'], this.error) || 'Der eingegebene Name stimmt nicht mit dem Namen der Runde überein';
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
