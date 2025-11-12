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
            <v-card>
                <v-card-text>
                    <h2
                        class="text-center mb-3"
                    >
                        Passwort vergessen
                        <br>
                        oder noch kein Passwort?
                    </h2>
                    <v-alert prominent class="mb-5">
                        <ul class="text-left mt-3 pl-5">
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
                    </v-alert>
                    <v-form
                        novalidate
                        @submit.stop.prevent
                    >
                        <v-text-field
                            v-model="username"
                            :disabled="isPasswordRequested"
                            prepend-inner-icon="mdi-email"
                            autofocus
                            type="text"
                            autocomplete="email"
                            placeholder="vorname.nachname@domain.de"
                            name="username"
                            label="E-Mail-Adresse"
                            density="compact"
                            variant="outlined"
                        />
                        <v-alert
                            v-if="usernameInvalidText && hasError"
                            type="error"
                            class="mb-2"
                        >{{ usernameInvalidText }}
                        </v-alert>
                        <v-text-field
                            id="email"
                            v-model="honeypotEmail"
                            type="text"
                            style="position: absolute; left: -10000px; top: -10000px;"
                            placeholder="vorname.nachname@streetworkapp.de"
                            name="email"
                            aria-label="Email"
                            aria-describedby="email-help-block"
                        />
                        <v-btn
                            :disabled="username.length < 10 || isLoading || isPasswordRequested"
                            block
                            :loading="isLoading"
                            density="default"
                            color="secondary"
                            type="submit"
                            @click="requestPasswordReset()"
                        >
                            Passwortänderung beantragen
                        </v-btn>
                        <general-error-alert v-if="hasError && !validationErrors.username && !validationErrors.global"/>
                        <router-link
                            class="mt-3 d-block"
                            :to="{ name: 'Login' }"
                        >
                            Zurück zur Anmeldung
                        </router-link>
                    </v-form>
                    <v-alert
                        v-if="isPasswordRequested && !hasError"
                        prominent
                        type="success"
                        class="mt-3"
                    >
                        <p class="font-weight-bold">
                            Herzlichen Glückwunsch!
                        </p>
                        <p class="mb-0">
                            Du solltest eine E-Mail bekommen haben.
                            <br>
                            Bitte schaue ggfs. auch in deinem Spam-Ordner nach.
                            <br>
                            Alle weiteren Schritte stehen in der E-Mail.
                        </p>
                    </v-alert>
                </v-card-text>
            </v-card>
        </v-col>
    </v-row>
</template>
<script>
"use strict";
import GeneralErrorAlert from './Common/GeneralErrorAlert.vue';
import {useAuthStore, useUserStore} from '../stores';
import {useRoute} from "vue-router";

export default {
    name: "PasswordReset",
    components: {GeneralErrorAlert},
    data: () => ({
        route: useRoute(),
        authStore: useAuthStore(),
        userStore: useUserStore(),
        username: '',
        honeypotEmail: '',
        usernameInvalidText: '',
        isPasswordRequested: false,
    }),
    computed: {
        isLoading() {
            return this.userStore.isLoading;
        },
        hasError() {
            return this.userStore.hasError;
        },
        error() {
            return this.userStore.getErrors.change;
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
            if (error.data && error.data['description']) {
                errors.global = error.data['description'];
                this.usernameInvalidText = errors.global;
            }

            return errors;
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
        async requestPasswordReset() {
            await this.userStore.requestPasswordReset(
                {
                    username: this.username,
                    email: this.honeypotEmail
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
