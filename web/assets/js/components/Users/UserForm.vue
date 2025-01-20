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
            label="Benutzername"
        >
            <b-input
                v-model="user.username"
                required
                minlength="4"
                maxlength="100"
                placeholder="vorname.nachname"
                :state="usernameState"
                data-test="username"
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
                v-model="user.email"
                required
                minlength="4"
                maxlength="100"
                placeholder="vorname.nachname@domain.de"
                :state="emailState"
                data-test="email"
            />
        </b-form-group>
        <b-form-group
            content-cols="12"
            label-cols="12"
            content-cols-lg="8"
            label-cols-lg="2"
            label="Rollen"
            v-slot="{ ariaDescribedby }"
        >
            <b-form-checkbox-group
                v-model="user.roles"
                data-test="roles"
                :options="availableRoles"
                switches
                :aria-describedby="ariaDescribedby"
            />
        </b-form-group>
        <client-select
            v-if="isSuperAdmin"
            v-model="user.client"
            :is-loading="isLoading"
            :disabled="isLoading"
        />
        <b-button
            type="submit"
            variant="secondary"
            :disabled="isFormInvalid"
            data-test="button-user-submit"
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
import { useClientStore, useAuthStore, useUserStore } from '../../stores';
import {ClientSelect} from "@/js/components/Common";

export default {
    name: 'UserForm',
    props: {
        initialUser: {
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
        ClientSelect,
        FormError,
    },
    data: function () {
        return {
            authStore: useAuthStore(),
            clientStore: useClientStore(),
            userStore: useUserStore(),
            user: {
                username: null,
                email: null,
                roles: null,
                client: null,
            },
        };
    },
    computed: {
        usernameState() {
            if (null === this.user.username || '' === this.user.username || undefined === this.user.username) {
                return;
            }

            return this.user.username.length >= 3 && this.user.username.length <= 100;
        },
        emailState() {
            if (null === this.user.email || '' === this.user.email || undefined === this.user.email) {
                return;
            }

            return this.user.email.length >= 4 && this.user.email.length <= 100 && EmailValidator.validate(this.user.email);
        },
        isLoading() {
            if (this.initialUser && this.initialUser['@id']) {
                return this.userStore.isLoadingChange(this.initialUser['@id'])
            }

            return this.userStore.isLoadingCreate || this.clientStore.isLoadingFetch;
        },
        currentUser() {
            return this.authStore.currentUser;
        },
        isSuperAdmin() {
            return this.authStore.isSuperAdmin;
        },
        isFormInvalid() {
            return !this.usernameState || !this.emailState || this.isLoading;
        },
        error() {
            if (this.initialUser?.userId) {
                return this.userStore.getErrors.change;
            }

            return this.userStore.getErrors.create;
        },
        availableRoles() {
            const roles = [{ text: 'Administrator', value: 'ROLE_ADMIN' }];
            if (this.isSuperAdmin) {
                roles.push({ text: 'Super-Administrator', value: 'ROLE_SUPER_ADMIN' });
                roles.push({ text: 'Impersonator', value: 'ROLE_ALLOWED_TO_SWITCH' });
            }

            return roles;
        },
    },
    async created() {
        this.user.username = this.initialUser.username;
        this.user.email = this.initialUser.email;
        this.user.roles = this.initialUser.roles || ['ROLE_USER'];
        this.user.client = this.initialUser.client;
    },
    methods: {
        async handleSubmit() {
            this.$emit('submit', this.user);
        },
        resetForm() {
            this.user.username = this.initialUser.username;
            this.user.email = this.initialUser.email;
            this.user.roles = this.initialUser.roles || ['ROLE_USER'];
            this.user.client = this.initialUser.client;
        },
    },
};
</script>

<style scoped lang="scss">
</style>
