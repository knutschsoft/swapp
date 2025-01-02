<template>
    <v-form
        @submit.prevent.stop="handleSubmit"
        ref="form"
        class="p-1 p-sm-2 p-lg-3"
    >
        <v-textarea
            v-model="question"
            required
            outlined
            minlength="5"
            label="Fragestellung"
            maxlength="4000"
            placeholder="Fragestellung"
            :state="questionState"
            data-test="question"
        />
        <template
            v-if="isSuperAdmin"
        >
            <v-select
                v-model="client"
                data-test="client"
                placeholder="Für welchen Klienten?"
                label="Klient"
                required
                outlined
                dense
                :items="availableClients"
                item-value="@id"
                item-text="name"
            />
        </template>
        <v-btn
            type="submit"
            color="secondary"
            :disabled="isFormInvalid"
            data-test="button-systemic-question-submit"
            block
            small
        >
            {{ submitButtonText }}
        </v-btn>
        <form-error
            :error="error"
        />
        <systemic-question-hint />
    </v-form>
</template>

<script>
'use strict';

import FormError from '../Common/FormError.vue';
import SystemicQuestionHint from './SystemicQuestionHint.vue';
import { useAuthStore, useClientStore, useSystemicQuestionStore } from '../../stores';

export default {
    name: 'SystemicQuestionForm',
    props: {
        initialClient: {
            type: String,
            required: true,
        },
        initialQuestion: {
            type: String,
            required: false,
            default: '',
        },
        submitButtonText: {
            type: String,
            required: true,
        },
    },
    components: {
        FormError,
        SystemicQuestionHint,
    },
    data: function () {
        return {
            authStore: useAuthStore(),
            clientStore: useClientStore(),
            systemicQuestionStore: useSystemicQuestionStore(),
            question: null,
            client: null,
        };
    },
    computed: {
        questionState() {
            if (null === this.question || '' === this.question) {
                return;
            }

            return this.question.length >= 5 && this.question.length <=4000;
        },
        isLoading() {
            return this.systemicQuestionStore.isLoading;
        },
        currentUser() {
            return this.authStore.currentUser;
        },
        isSuperAdmin() {
            return this.authStore.isSuperAdmin;
        },
        isFormInvalid() {
            return !this.question || !this.questionState || this.isLoading;
        },
        error() {
            return this.systemicQuestionStore.getErrors.create;
        },
        availableClients() {
            return this.clientStore.getClients;
        },
    },
    async mounted() {
        await this.setInitialValues();
    },
    watch: {
        initialQuestion: async function () {
            await this.setInitialValues();
        },
        initialClient: async function () {
            await this.setInitialValues();
        },
    },
    methods: {
        async setInitialValues() {
            this.client = this.initialClient;
            this.question = this.initialQuestion;
        },
        async handleSubmit() {
            this.$emit('submit', {
                client: this.client,
                question: this.question,
            });
        },
        resetForm() {
            this.question = null;
        },
    },
};
</script>

<style scoped lang="scss">
</style>
