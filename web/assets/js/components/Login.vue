<template>
    <div class="row m-auto pt-4 mt-4">
        <div
            class="col-sm-10 offset-sm-1 col-md-8 offset-md-2 offset-lg-3 col-lg-6 border border-dark p-4 mt-4"
        >
            <h2
                class="text-center mb-3"
            >
                Login
            </h2>
            <p class="text-center">
                Bitte melde dich mit deiner E-Mail-Adresse (oder deinem Benutzername) und deinem selbst gewählten Passwort an.
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
                                <b-icon
                                    icon="envelope-fill"
                                    class="dark"
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
                                <b-icon
                                    icon="lock-fill"
                                    class="dark"
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
                                <b-icon
                                    :icon="eyeIcon"
                                    class="dark"
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
                            :disabled="username.length < 5 || password.length < -1 || isLoading"
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
            <DemoInfo />
        </div>
    </div>
</template>

<script>
    "use strict";
    import DemoInfo from './Demo/DemoInfo.vue';

    export default {
        name: "Login",
        components: { DemoInfo },
        data: () => ({
            username: '',
            password: '',
            usernameHelp: '',
            passwordHelp: '',
            state: null,
            passwordFieldType: 'password',
            eyeIcon: 'eye-fill',
        }),
        computed: {
            isLoading() {
                return this.$store.getters["security/isLoading"];
            },
            hasError() {
                return this.$store.getters["security/hasError"];
            },
            error() {
                return this.$store.getters["security/error"];
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

            if (this.$store.getters["security/isAuthenticated"]) {
                if (typeof redirect !== "undefined") {
                    this.$router.push({path: redirect});
                } else {
                    this.$router.push({name: "Dashboard"});
                }
            }
        },
        methods: {
            async performLogin() {
                if ('text' === this.passwordFieldType) {
                    // ensure that password can be saved via browser
                    this.switchPasswordVisibility();
                }
                let payload = {username: this.$data.username, password: this.$data.password},
                    redirect = this.$route.query.redirect;
                await this.$store.dispatch("security/login", payload);
                if (!this.$store.getters["security/hasError"]) {
                    if (typeof redirect !== "undefined") {
                        this.$router.push({path: redirect});
                    } else {
                        this.$router.push({name: "Dashboard"});
                    }
                }
            },
            switchPasswordVisibility() {
                this.passwordFieldType = 'text' === this.passwordFieldType ? 'password' : 'text';
                this.eyeIcon = 'text' === this.passwordFieldType ? 'eye-slash-fill' : 'eye-fill';
            },
        },
    }
</script>

<style scoped>

</style>
