<template>
    <div class="row m-auto pt-4 mt-4">
        <div
            class="col-sm-10 offset-sm-1 col-md-8 offset-md-2 offset-lg-3 col-lg-6 border border-dark p-4 mt-4"
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
                <b-form
                    novalidate
                    @submit.stop.prevent
                >
                    <b-input-group
                        class="form-group input-group"
                    >
                        <template v-slot:prepend>
                            <b-input-group-text>
                                <mdicon
                                    name="AccountCircleOutline"
                                    size="22"
                                    title="Benutzername oder E-Mail"
                                />
                            </b-input-group-text>
                        </template>
                        <b-input
                            id="username"
                            v-model="username"
                            :state="validation"
                            autofocus
                            type="text"
                            class="form-control"
                            placeholder="vorname.nachname@domain.de"
                            name="username"
                            data-test="username"
                            autocomplete="username email"
                            aria-label="Nutzername"
                            aria-describedby="username-help-block"
                        />
                        <b-form-text
                            v-if="usernameHelp"
                            id="username-help-block"
                            v-text="usernameHelp"
                        />
                        <b-form-valid-feedback
                            :state="validation"
                        >
                            Schaut gut aus.
                        </b-form-valid-feedback>
                    </b-input-group>
                    <b-input-group
                        class="form-group input-group"
                    >
                        <template v-slot:prepend>
                            <b-input-group-text>
                                <mdicon
                                    name="LockOutline"
                                    size="22"
                                    tit !le="Passwort"
                                />
                            </b-input-group-text>
                        </template>
                        <b-input
                            v-model="password"
                            placeholder="Passwort"
                            name="password"
                            data-test="password"
                            :type="passwordFieldType"
                            class="form-control"
                            aria-label="Passwort"
                            autocomplete="password"
                            aria-describedby="password-help-block"
                        />
                        <template v-slot:append>
                            <b-input-group-text
                                @click="switchPasswordVisibility"
                            >
                                <mdicon
                                    v-if="isPasswordVisible"
                                    name="EyeOffOutline"
                                    size="22"
                                    title="Passwort verstecken"
                                />
                                <mdicon
                                    v-else
                                    name="EyeOutline"
                                    size="22"
                                    title="Passwort anzeigen"
                                />
                            </b-input-group-text>
                        </template>
                        <b-form-text
                            v-if="passwordHelp"
                            id="password-help-block"
                            v-text="passwordHelp"
                        />
                    </b-input-group>
                    <b-input-group
                        class="form-group input-group"
                    >
                        <b-button
                            :disabled="username.length < 3 || password.length < -1 || isLoading"
                            block
                            variant="dark"
                            type="submit"
                            @click="performLogin()"
                        >
                            <b-spinner
                                v-if="isLoading"
                                variant="secondary"
                                small
                                class="mr-auto position-relative"
                                label="Spinning"
                            />
                            Anmelden
                        </b-button>
                    </b-input-group>
                    <b-input-group
                        v-if="hasError"
                        class="form-group input-group"
                    >
                        <b-form-text
                            class="alert alert-danger w-100 mb-0"
                            role="alert"
                        >
                            {{ 'Die Kombination aus E-Mail-Adresse und Passwort ist ungültig.' }}
                        </b-form-text>
                    </b-input-group>
                    <b-input-group class="form-group input-group mb-0">
                        <router-link
                            class="btn btn-block btn-link"
                            :to="{ name: 'PasswordReset' }"
                        >
                            Passwort vergessen oder noch kein Passwort?
                        </router-link>
                    </b-input-group>
                </b-form>
            </div>
            <DemoInfo
                @credentials-select="handleCredentialsSelect($event)"
            />
        </div>
    </div>
</template>

<script>
    "use strict";
    import DemoInfo from './Demo/DemoInfo.vue';
    import SecurityApi from '../api/security';
    import { useAuthStore } from '../stores/auth';

    export default {
        name: "Login",
        components: { DemoInfo },
        data: () => ({
            authStore: useAuthStore(),
            username: '',
            password: '',
            usernameHelp: '',
            passwordHelp: '',
            state: null,
            passwordFieldType: 'password',
            isPasswordVisible: false,
        }),
        computed: {
            isOnDemoPage() {
                return window.location.host.includes('swapp.demo') || this.$route.query.demo;
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
            validation() {
                if (this.username.trim().length <= 2) {
                    return null;
                }

                return true;
            }
        },
        created() {
            let redirect = this.$route.query.redirect;

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
                    redirect = this.$route.query.redirect;
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
