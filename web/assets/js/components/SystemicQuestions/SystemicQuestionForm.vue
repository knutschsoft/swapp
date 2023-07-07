<template>
    <b-form
        @submit.prevent.stop="handleSubmit"
        ref="form"
        class="p-1 p-sm-2 p-lg-3"
    >
        <b-form-group
            content-cols="12"
            label-cols="12"
            content-cols-lg="8"
            label-cols-lg="2"
            label="Fragestellung"
        >
            <b-textarea
                v-model="question"
                required
                minlength="3"
                maxlength="4000"
                placeholder="Fragestellung"
                :state="questionState"
                data-test="question"
            />
        </b-form-group>
        <b-form-group
            v-if="isSuperAdmin"
            content-cols="12"
            label-cols="12"
            content-cols-lg="8"
            label-cols-lg="2"
        >
            <template slot="label" slot-scope="{ }">
                Klient
            </template>
            <b-form-select
                v-model="client"
                data-test="client"
                placeholder="Für welchen Klienten?"
                :options="availableClients"
                value-field="@id"
                text-field="name"
            />
        </b-form-group>
        <b-button
            type="submit"
            variant="secondary"
            :disabled="isFormInvalid"
            data-test="button-systemic-question-submit"
            block
            class="col-12"
            :tabindex="isFormInvalid ? '-1' : ''"
        >
            {{ submitButtonText }}
        </b-button>
        <form-error
            :error="error"
        />
        <systemic-question-hint />
    </b-form>
</template>

<script>
'use strict';

import FormError from '../Common/FormError.vue';
import SystemicQuestionHint from './SystemicQuestionHint.vue';

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
            question: null,
            client: null,
        };
    },
    computed: {
        questionState() {
            if (null === this.question || '' === this.question) {
                return;
            }

            return this.question.length >= 3 && this.question.length <=4000;
        },
        isLoading() {
            return this.$store.getters['systemicQuestion/isLoading'];
        },
        currentUser() {
            return this.$store.getters['security/currentUser'];
        },
        isSuperAdmin() {
            return this.$store.getters['security/isSuperAdmin'];
        },
        isFormInvalid() {
            return !this.question || !this.questionState || this.isLoading;
        },
        error() {
            return this.$store.getters['systemicQuestion/createSystemicQuestionError'];
        },
        availableClients() {
            return this.$store.getters['client/clients'];
        },
    },
    async created() {
        this.client = this.initialClient;
        this.question = this.initialQuestion;
    },
    methods: {
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
