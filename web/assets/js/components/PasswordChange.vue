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
            <v-alert
                v-if="!isConfirmationTokenValid"
                type="warning"
                prominent
            >
                Upps! Der von dir genutzte Link ist nicht länger gültig.
                <br>
                Bitte schaue nach, ob in deinem E-Mail-Postfach eine neuere E-Mail mit Link vorhanden ist oder beantrage
                nochmal ein neues Passwort.
                <v-btn
                    block
                    class="my-3"
                    color="secondary"
                    density="default"
                    :to="{ name: user ? 'PasswordChangeRequest' : 'PasswordReset' }"
                >
                    Passwortänderung beantragen
                </v-btn>
            </v-alert>
            <v-card
                v-else
            >
                <v-card-text>
                    <h2
                        class="text-center"
                    >
                        Passwort ändern
                    </h2>
                    <v-form
                        @submit.stop.prevent
                    >
                        <v-text-field
                            v-model="password"
                            prepend-inner-icon="mdi-lock-outline"
                            :append-inner-icon="isPasswordVisible ? 'mdi-eye-off-outline' : 'mdi-eye-outline'"
                            :type="passwordFieldType"
                            label="Passwort"
                            placeholder="Passwort"
                            name="password"
                            data-test="password"
                            autocomplete="off"
                            :disabled="isPasswordChanged || isLoading"
                            density="compact"
                            variant="outlined"
                            clearable
                            counter
                            maxlength="40"
                            persistent-counter
                            @click:append-inner="switchPasswordVisibility"
                            @click:clear="password = ''"
                        />
                        <v-text-field
                            v-model="passwordRepeat"
                            prepend-inner-icon="mdi-lock-outline"
                            :append-inner-icon="isPasswordVisible ? 'mdi-eye-off-outline' : 'mdi-eye-outline'"
                            :type="passwordFieldType"
                            label="Passwort wiederholen"
                            placeholder="Passwort"
                            name="passwordRepeat"
                            data-test="passwordRepeat"
                            autocomplete="off"
                            :disabled="isPasswordChanged || isLoading"
                            density="compact"
                            variant="outlined"
                            clearable
                            counter
                            maxlength="40"
                            persistent-counter
                            @input="passwordValidation"
                            @click:append-inner="switchPasswordVisibility"
                            @click:clear="passwordRepeat = ''"
                        />
                        <v-alert
                            v-if="false === passwordState && passwordInvalidText"
                            type="warning"
                            class="mb-3"
                        >
                            {{ passwordInvalidText }}
                        </v-alert>
                        <v-alert
                            v-if="false === passwordRepeatValidation"
                            type="warning"
                            class="mb-3"
                        >
                            {{ 'Die Passwörter sind unterschiedlich.' }}
                        </v-alert>
                        <v-btn
                            :disabled="!passwordState || !passwordRepeatValidation || isLoading || isPasswordChanged"
                            block
                            color="secondary"
                            type="submit"
                            density="default"
                            data-test="btn-change-password"
                            :loading="isLoading"
                            @click="changePassword()"
                        >
                            Passwort ändern
                        </v-btn>
                        <general-error-alert v-if="hasError && !validationErrors.password" />
                    </v-form>
                    <v-alert
                            v-if="isPasswordChanged && !hasError"
                            type="success"
                            class="mt-3"
                            prominent
                        >
                            <p class="font-weight-bold">
                                Herzlichen Glückwunsch!
                            </p>
                            <p>
                                Du hast erfolgreich dein Passwort geändert.
                                <span
                                    v-if="!user"
                                >
                                    <br>
                                    <br>
                                    Melde dich jetzt an:
                                </span>
                            </p>
                            <v-btn
                                v-if="!user"
                                :to="{ name: 'Login'}"
                                color="secondary"
                                density="default"
                                block
                            >
                                Zur Anmeldung
                            </v-btn>
                        </v-alert>
                </v-card-text>
            </v-card>
        </v-col>
    </v-row>
</template>
<script>
    "use strict";
    import GeneralErrorAlert from './Common/GeneralErrorAlert.vue';
    import SecurityAPI from '../api/security';
    import { useAuthStore, useUserStore } from '../stores';

    export default {
        name: "RequestPasswordChange",
        components: { GeneralErrorAlert },
        props: {
            'userId': {
                required: true,
                type: String
            },
            'confirmationToken': {
                required: true,
                type: String
            },
        },
        data: () => ({
            authStore: useAuthStore(),
            userStore: useUserStore(),
            password: '',
            passwordRepeat: '',
            passwordRepeatHelp: 'Bitte gib zwei Mal das gleiche Passwort ein um Tippfehler zu vermeiden.',
            isConfirmationTokenValid: true,
            passwordState: null,
            passwordInvalidText: '',
            passwordRepeatState: true,
            isPasswordChanged: false,
            passwordFieldType: 'password',
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
            user() {
                return this.authStore.currentUser['@id'] ? this.authStore.currentUser : false;
            },
            isPasswordVisible() {
                return 'text' === this.passwordFieldType;
            },
            passwordRepeatValidation() {
                if (this.password.trim().length < 12 || this.passwordRepeat.trim().length < 12) {
                    return null;
                }

                return this.password === this.passwordRepeat;
            },
            validationErrors() {
                const errors = {};
                if (!this.hasError) {
                    return errors;
                }
                this.passwordState = false;
                this.passwordRepeatState = null;
                const error = this.error;
                if (error && error.data.violations) {
                    error.data.violations.forEach((violation) => {
                        const key = violation.propertyPath ? violation.propertyPath : 'global';
                        errors[key] = violation.message;
                        this.passwordInvalidText = violation.message;
                    });
                    return errors;
                }
                if (error.data && error.data['description']) {
                    errors.global = error.data['description'];
                    this.passwordInvalidText = errors.global;
                }

                return errors;
            },
        },
        async created() {
            try {
                this.isConfirmationTokenValid = await SecurityAPI.isConfirmationTokenValid(
                    `/api/users/${this.userId}`,
                    this.confirmationToken
                );
            } catch (e) {
                this.isConfirmationTokenValid = false;
            }
        },
        methods: {
            passwordValidation() {
                const value = this.passwordRepeat;
                let regex = new RegExp('^[\\w_.\+*,:;#!?=%&{}|$@()\\-\\[\\]\/\\\\]*$');
                if (value.length < 1) {
                    this.passwordState = null;
                } else if (!regex.test(value)) {
                    this.passwordInvalidText = 'Dein Passwort darf nur aus Buchstaben, Ziffern und folgenden Sonderzeichen _.*-+:#!?%{}|@[];=&$\\/,() bestehen.';
                    this.passwordState = false;
                } else if (value.trim().length < 12 || value.trim().length > 40) {
                    this.passwordInvalidText = 'Dein Passwort muss zwischen 12 und 40 Zeichen enthalten.';
                    this.passwordState = false;
                } else {
                    this.passwordState = true;
                }
            },
            async changePassword() {
                if ('text' === this.passwordFieldType) {
                    // ensure that password can be saved via browser
                    this.switchPasswordVisibility();
                }
                let result = await this.userStore.changePassword({
                    user: `/api/users/${this.userId}`,
                    password: this.password,
                    confirmationToken: {
                        token: this.confirmationToken
                    },
                });

                if (result) {
                    this.isPasswordChanged = true;
                }
            },
            switchPasswordVisibility() {
                this.passwordFieldType = 'text' === this.passwordFieldType ? 'password' : 'text';
            },
        },
    }
</script>

<style scoped>

</style>
