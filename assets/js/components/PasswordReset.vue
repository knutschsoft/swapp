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
            <ul class="text-left mt-3">
                <li>
                    Um Ihr Passwort zu ändern, tragen Sie bitte Ihre E-Mail-Adresse ein und senden das Formular ab.
                </li>
                <li>
                    Sie bekommen dann eine E-Mail mit einem Link zugeschickt.
                </li>
                <li>
                    Mit Hilfe dieses Links können Sie dann ein neues Passwort setzen.
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
                        >
                            Die E-Mail-Adresse muss eine valide E-Mail-Adresse sein.
                        </b-form-invalid-feedback>
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
                        placeholder="vorname.nachname@htwk-leipzig.de"
                        name="email"
                        aria-label="Email"
                        aria-describedby="email-help-block"
                    />
                    <b-input-group
                        class="form-group input-group"
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
                    <b-input-group
                        v-if="hasError"
                        class="form-group input-group"
                    >
                        <b-form-text
                            class="alert alert-danger w-100 mb-0"
                            role="alert"
                        >
                            <p
                                class="font-weight-bold"
                            >
                                Upps! Da ist etwas schief gelaufen!
                            </p>
                            <p class="mb-0">
                                Bitte informieren Sie den Administrator über das Problem.
                            </p>
                        </b-form-text>
                    </b-input-group>
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
                            Sie sollten eine Mail bekommen haben.
                            <br>
                            Bitte schauen Sie ggfs. auch in ihrem Spam-Ordner nach.
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
    import * as EmailValidator from 'email-validator';

    export default {
        name: "PasswordReset",
        data: () => ({
            username: '',
            honeypotEmail: '',
            usernameHelp: '',
            state: null,
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
                if (this.username.trim().length <= 20) {
                    return null;
                }

                return EmailValidator.validate(this.username.trim());
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
            async requestPasswordReset() {
                this.isPasswordRequested = await this.$store.dispatch(
                    "security/requestPasswordReset",
                    {
                        email: this.username,
                        honeypotEmail: this.honeypotEmail
                    }
                );
            }
        },
    }
</script>

<style scoped>

</style>
