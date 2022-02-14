<template>
    <div class="row m-auto pt-4 mt-4">
        <div
            class="col-sm-10 offset-sm-1 col-md-8 offset-md-2 offset-lg-3 col-lg-6 border border-dark p-4 mt-4"
        >
            <h2
                class="text-center mb-3"
            >
                Passwort vergessen
                <br>
                oder noch kein Passwort?
            </h2>
            <ul class="text-left mt-3 pl-3">
                <li>
                    Um dein Passwort zu ändern, trage bitte deine E-Mail-Adresse ein und sende das Formular ab.
                </li>
                <li>
                    Du bekommst dann eine E-Mail mit einem Link zugeschickt.
                </li>
                <li>
                    Mit Hilfe dieses Links kannst du dann ein neues Passwort setzen.
                </li>
            </ul>
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
                            :disabled="isPasswordRequested"
                            autofocus
                            type="text"
                            class="form-control"
                            placeholder="vorname.nachname@domain.de"
                            name="username"
                            aria-label="Nutzername"
                            aria-describedby="username-help-block"
                        />
                        <b-form-text
                            v-if="usernameHelp"
                            id="username-help-block"
                            v-text="usernameHelp"
                        />
                        <b-form-invalid-feedback
                            :state="validation"
                            v-text="usernameInvalidText"
                        />
                        <b-form-valid-feedback
                            :state="validation"
                        >
                            Schaut gut aus.
                        </b-form-valid-feedback>
                    </b-input-group>
                    <b-input
                        id="email"
                        v-model="honeypotEmail"
                        type="text"
                        class="form-control"
                        style="position: absolute; left: -10000px; top: -10000px;"
                        placeholder="vorname.nachname@streetworkapp.de"
                        name="email"
                        aria-label="Email"
                        aria-describedby="email-help-block"
                    />
                    <b-input-group
                        class="form-group input-group mb-0"
                    >
                        <b-button
                            :disabled="username.length < 10 || isLoading || isPasswordRequested"
                            block
                            variant="dark"
                            type="submit"
                            @click="requestPasswordReset()"
                        >
                            <b-spinner
                                v-if="isLoading"
                                variant="secondary"
                                small
                                class="mr-auto position-relative"
                                label="Spinning"
                            />
                            Passwortänderung beantragen
                        </b-button>
                    </b-input-group>
                    <general-error-alert v-if="hasError && !validationErrors.username && !validationErrors.global" />
                </b-form>
                <div
                    v-if="isPasswordRequested && !hasError"
                    class="mt-3"
                >
                    <div
                        class="alert alert-success w-100 mb-0"
                        role="alert"
                    >
                        <p class="font-weight-bold">
                            Herzlichen Glückwunsch!
                        </p>
                        <p class="mb-0">
                            Du solltest eine Mail bekommen haben.
                            <br>
                            Bitte schaue ggfs. auch in deinem Spam-Ordner nach.
                            <br>
                            Alle weiteren Schritte stehen in der Mail.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
    "use strict";
    import GeneralErrorAlert from './Common/GeneralErrorAlert.vue';

    export default {
        name: "PasswordReset",
        components: { GeneralErrorAlert },
        data: () => ({
            username: '',
            honeypotEmail: '',
            usernameHelp: '',
            usernameInvalidText: '',
            isPasswordRequested: false,
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
                if (this.username.trim().length <= 4) {
                    return null;
                }

                return !this.hasError;
            },
            validationErrors() {
                const errors = {};
                if (!this.hasError) {
                    return errors;
                }
                this.state = false;
                const error = this.error;
                if (error && error.data.violations) {
                    error.data.violations.forEach((violation) => {
                        const key = violation.propertyPath ? violation.propertyPath : 'global';
                        errors[key] = violation.message;
                        this.usernameInvalidText = violation.message;
                    });
                    return errors;
                }
                if (error.data && error.data['hydra:description']) {
                    errors.global = error.data['hydra:description'];
                    this.usernameInvalidText = errors.global;
                }

                return errors;
            },
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
            async requestPasswordReset() {
                await this.$store.dispatch(
                    "security/requestPasswordReset",
                    {
                        email: this.username,
                        honeypotEmail: this.honeypotEmail
                    }
                );
                if (!this.hasError) {
                    this.isPasswordRequested = true;
                }
            }
        },
    }
</script>

<style scoped>

</style>
