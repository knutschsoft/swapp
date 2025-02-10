<template>
    <v-form
        @submit.prevent="handleSubmit"
        ref="form"
        class="pa-1 pa-sm-2 pa-md-4 pa-lg-5 pa-xl-6 pa-xxl-7"
    >
        <v-textarea
            v-model="question"
            required
            variant="outlined"
            minlength="5"
            label="Fragestellung"
            maxlength="4000"
            auto-grow
            placeholder="Fragestellung"
            :state="questionState"
            data-test="question"
        />
        <template
            v-if="isSuperAdmin"
        >
            <client-select
                v-model="client"
                :is-loading="isLoading"
                :disabled="isLoading"
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
import { useAuthStore, useSystemicQuestionStore } from '../../stores';
import {ClientSelect} from "@/js/components/Common";

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
        ClientSelect,
        FormError,
        SystemicQuestionHint,
    },
    data: function () {
        return {
            authStore: useAuthStore(),
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
            this.$emit('submitted', {
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
