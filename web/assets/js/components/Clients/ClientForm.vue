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
            label="Name"
        >
            <b-input
                v-model="client.name"
                required
                minlength="4"
                maxlength="100"
                placeholder="Name"
                :state="nameState"
                data-test="name"
            />
        </b-form-group>
        <b-form-group
            content-cols="12"
            label-cols="12"
            content-cols-lg="8"
            label-cols-lg="2"
            label="E-Mail"
        >
            <b-input
                v-model="client.email"
                required
                minlength="4"
                maxlength="100"
                placeholder="E-Mail"
                :state="emailState"
                data-test="email"
            />
        </b-form-group>
        <b-form-group
            content-cols="12"
            label-cols="12"
            content-cols-lg="8"
            label-cols-lg="2"
            label="Beschreibung"
        >
            <b-textarea
                v-model="client.description"
                minlength="4"
                maxlength="100000"
                placeholder="Beschreibung"
                :state="descriptionState"
                data-test="description"
            />
        </b-form-group>
        <b-button
            type="submit"
            variant="secondary"
            :disabled="isFormInvalid"
            data-test="button-client-submit"
            block
            class="col-12"
            :tabindex="isFormInvalid ? '-1' : ''"
        >
            {{ submitButtonText }}
        </b-button>
        <form-error
            :error="error"
        />
    </b-form>
</template>

<script>
'use strict';
import * as EmailValidator from 'email-validator';
import FormError from '../Common/FormError.vue';

export default {
    name: 'UserForm',
    props: {
        initialClient: {
            type: Object,
            required: false,
            default: {},
        },
        submitButtonText: {
            type: String,
            required: true,
        },
    },
    components: {
        FormError,
    },
    data: function () {
        return {
            client: {
                name: null,
                email: null,
                description: '',
            },
        };
    },
    computed: {
        nameState() {
            if (null === this.client.name || '' === this.client.name || undefined === this.client.name) {
                return;
            }

            return this.client.name.length >= 3 && this.client.name.length <= 200;
        },
        emailState() {
            if (null === this.client.email || '' === this.client.email || undefined === this.client.email) {
                return;
            }

            return this.client.email.length >= 3 && this.client.email.length <= 100 && EmailValidator.validate(this.client.email);
        },
        descriptionState() {
            if (null === this.client.description || undefined === this.client.description) {
                return;
            }

            return this.client.description.length >= 0 && this.client.description.length <= 10000;
        },
        isLoading() {
            return this.$store.getters['user/isLoadingChange'];
        },
        currentUser() {
            return this.$store.getters['security/currentUser'];
        },
        isSuperAdmin() {
            return this.$store.getters['security/isSuperAdmin'];
        },
        isFormInvalid() {
            return !this.nameState || !this.emailState || !this.descriptionState || this.isLoading;
        },
        error() {
            return this.$store.getters['client/changeClientError'];
        },
    },
    async created() {
        this.client.name = this.initialClient.name;
        this.client.email = this.initialClient.email;
        this.client.description = this.initialClient.description || '';
    },
    methods: {
        async handleSubmit() {
            this.$emit('submit', this.client);
        },
        resetForm() {
            this.$refs.form.reset();
        },
    },
};
</script>

<style scoped lang="scss">
</style>
