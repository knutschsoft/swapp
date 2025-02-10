<template>
    <v-row>
        <v-col
            cols="12"
            sm="8"
            md="6"
            lg="6"
            xl="4"
            offset-sm="2"
            offset-md="3"
            offset-lg="3"
            offset-xl="4"
            class="mt-4"
        >
            <h2
                class="text-center mb-3"
            >
                Anmeldung
            </h2>
            <p class="text-center">
                <template
                    v-if="isOnDemoPage"
                >
                    Bitte melde dich mit einem der unten stehenden Zugangsdaten an
                    <br>
                    <span class="text-muted ">
                        oder alternativ mit deiner E-Mail-Adresse (oder deinem Benutzername) und deinem selbst gewählten Passwort an.
                   </span>
                </template>
                <template
                    v-else
                >
                    Bitte melde dich mit deiner E-Mail-Adresse (oder deinem Benutzername) und deinem selbst gewählten Passwort an.
                </template>
            </p>
            <div>
                <v-form
                    novalidate
                    @submit.stop.prevent
                >
                    <v-text-field
                        v-model="username"
                        id="username"
                        prepend-inner-icon="mdi-account-circle-outline"
                        autofocus
                        type="text"
                        :disabled="isLoading"
                        label="Benutzername oder E-Mail"
                        placeholder="vorname.nachname@domain.de"
                        name="username"
                        data-test="username"
                        autocomplete="username email"
                        density="compact"
                        variant="outlined"
                    />
                    <v-text-field
                        v-model="password"
                        prepend-inner-icon="mdi-lock-outline"
                        :append-icon="isPasswordVisible ? 'mdi-eye-off-outline' : 'mdi-eye-outline'"
                        autofocus
                        :type="passwordFieldType"
                        :disabled="isLoading"
                        label="Passwort"
                        placeholder="Passwort"
                        name="password"
                        data-test="password"
                        autocomplete="password"
                        density="compact"
                        variant="outlined"
                        @click:append="switchPasswordVisibility"
                    />
                    <v-btn
                        :disabled="username.length < 3 || password.length < -1 || isLoading"
                        block
                        color="secondary"
                        type="submit"
                        @click="performLogin()"
                    >
                        <v-progress-circular
                            v-if="isLoading"
                            :width="2"
                            :size="20"
                            indeterminate
                            class="mr-2 position-relative"
                        ></v-progress-circular>
                        Anmelden
                    </v-btn>
                    <v-alert
                        v-if="hasError"
                        type="error"
                        class="mt-2"
                    >
                        {{ 'Die Kombination aus E-Mail-Adresse und Passwort ist ungültig.' }}
                    </v-alert>
                    <v-btn
                        variant="text"
                        block
                        class="my-3"
                    >
                        <router-link
                            :to="{ name: 'PasswordReset' }"
                        >
                            Passwort vergessen oder noch kein Passwort?
                        </router-link>
                    </v-btn>
                </v-form>
            </div>
            <DemoInfo
                @credentials-select="handleCredentialsSelect($event)"
            />
        </v-col>
    </v-row>
</template>

<script>
    "use strict";
    import DemoInfo from './Demo/DemoInfo.vue';
    import { useAuthStore } from '../stores';
    import {useRoute} from "vue-router";

    export default {
        name: "Login",
        components: { DemoInfo },
        data: () => ({
            route: useRoute(),
            authStore: useAuthStore(),
            username: '',
            password: '',
            state: null,
            passwordFieldType: 'password',
            isPasswordVisible: false,
        }),
        computed: {
            isOnDemoPage() {
                return window.location.host.includes('swapp.demo') || this.route.query.demo;
            },
            isLoading() {
                return this.authStore.isLoading;
            },
            hasError() {
                return !!this.authStore.getErrors.login;
            },
            error() {
                return this.authStore.getErrors.login;
            },
        },
        created() {
            let redirect = this.route.query.redirect;

            if (this.authStore.isAuthenticated) {
                if (typeof redirect !== "undefined") {
                    this.$router.push({path: redirect});
                } else {
                    this.$router.push({name: "Dashboard"});
                }
            }
        },
        methods: {
            handleCredentialsSelect(credentials) {
                this.username = credentials.username;
                this.password = credentials.password;
            },
            async performLogin() {
                if ('text' === this.passwordFieldType) {
                    // ensure that password can be saved via browser
                    this.switchPasswordVisibility();
                }
                let payload = {username: this.$data.username, password: this.$data.password},
                    redirect = this.route.query.redirect;
                const loginResult = await this.authStore.login(payload);
                if (!this.error && loginResult) {
                    if (typeof redirect !== "undefined") {
                        this.$router.push({path: redirect});
                    } else {
                        this.$router.push({name: "Dashboard"});
                    }
                }
            },
            switchPasswordVisibility() {
                this.passwordFieldType = 'text' === this.passwordFieldType ? 'password' : 'text';
                this.isPasswordVisible = 'text' === this.passwordFieldType;
            },
        },
    }
</script>

<style scoped>

</style>
