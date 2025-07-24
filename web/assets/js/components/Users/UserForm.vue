<template>
    <v-form
        @submit.prevent="handleSubmit"
        ref="form"
        class="pa-1 pa-sm-2 pa-md-4 pa-lg-5 pa-xl-6 pa-xxl-7"
    >
        <v-text-field
            v-model="user.username"
            required
            minlength="4"
            maxlength="100"
            label="Benutzername"
            placeholder="vorname.nachname"
            :state="usernameState"
            data-test="username"
            variant="outlined"
        />
        <v-text-field
            v-model="user.email"
            required
            minlength="4"
            maxlength="100"
            label="E-Mail"
            placeholder="vorname.nachname@domain.de"
            :state="emailState"
            data-test="email"
            variant="outlined"
        />
        <v-row dense class="mb-5">
            <v-col cols="12" class="mb-0">Rollen</v-col>
            <v-col
                :cols="12"
            >
                <div class="d-flex ga-4 flex-column flex-sm-row">
                    <v-switch
                        v-for="role in availableRoles"
                        v-model="user.roles"
                        :value="role.value"
                        :key="role.value"
                        :disabled="isLoading"
                        class="ml-2"
                        density="compact"
                        color="primary"
                        hide-details
                        :label="role.text"
                    />
                </div>
            </v-col>
        </v-row>
        <client-select
            v-if="isSuperAdmin"
            v-model="user.client"
            :is-loading="isLoading"
            :disabled="isLoading"
            hint="Der Benutzer wird für diesen Klienten erstellt."
        />
        <v-btn
            type="submit"
            color="secondary"
            :disabled="isFormInvalid"
            :loading="isLoading"
            class="text-transform-none"
            density="comfortable"
            data-test="button-user-submit"
            block
        >
            {{ submitButtonText }}
        </v-btn>
        <form-error
            :error="error"
        />
    </v-form>
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
            if (this.user.username.includes(" ")) {
                return false;
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
            this.$emit('submitted', this.user);
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
