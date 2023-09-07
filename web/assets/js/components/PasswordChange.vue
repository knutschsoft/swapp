<template>
    <div class="row m-auto pt-4 mt-4">
        <div
            v-if="!isConfirmationTokenValid"
            class="col-sm-10 offset-sm-1 col-md-8 offset-md-2 offset-lg-3 col-lg-6 border border-dark p-4 mt-4"
        >
            Upps! Der von dir genutzte Link ist nicht länger gültig.
            <br>
            Bitte schaue nach, ob in deinem E-Mail-Postfach eine neuere E-Mail mit Link vorhanden ist oder beantrage
            nochmal ein neues Passwort.
            <b-input-group
                class="form-group input-group mb-0 mt-3"
            >
                <router-link
                    class="btn btn-block btn-dark"
                    :to="{ name: user ? 'PasswordChangeRequest' : 'PasswordReset' }"
                >
                    Passwortänderung beantragen
                </router-link>
            </b-input-group>
        </div>
        <div
            v-else
            class="col-sm-10 offset-sm-1 col-md-8 offset-md-2 offset-lg-3 col-lg-6 border border-dark p-4 mt-4"
        >
            <h2
                class="text-center"
            >
                Passwort ändern
            </h2>
            <div>
                <b-form
                    novalidate
                    @submit.stop.prevent
                >
                    <b-input-group
                        class="form-group input-group"
                    >
                        <b-input-group-prepend>
                            <b-input-group-text>
                                <b-icon
                                    icon="lock-fill"
                                    class="dark"
                                />
                            </b-input-group-text>
                            <b-input-group-text>
                                <b-icon
                                    :icon="eyeIcon"
                                    class="dark"
                                    @click="switchPasswordVisibility"
                                />
                            </b-input-group-text>
                        </b-input-group-prepend>
                        <b-form-input
                            id="password"
                            v-model="password"
                            :state="passwordState"
                            :disabled="isPasswordChanged || isLoading"
                            placeholder="Passwort"
                            name="password"
                            :type="passwordFieldType"
                            class="form-control"
                            aria-label="Passwort"
                            aria-describedby="password-help-block password-valid-feedback password-invalid-feedback"
                            @input="passwordValidation"
                        />
                        <b-form-text
                            v-if="passwordHelp"
                            id="password-help-block"
                            v-text="passwordHelp"
                        />
                        <b-form-invalid-feedback
                            id="password-invalid-feedback"
                            v-text="passwordInvalidText"
                        />
                        <b-form-valid-feedback
                            id="password-valid-feedback"
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
                        <b-input-group-prepend
                            @click="switchPasswordVisibility"
                        >
                            <b-input-group-text>
                                <b-icon
                                    :icon="eyeIcon"
                                    class="dark"
                                />
                            </b-input-group-text>
                        </b-input-group-prepend>
                        <b-form-input
                            v-model="passwordRepeat"
                            :state="passwordRepeatValidation"
                            :disabled="isPasswordChanged || isLoading"
                            placeholder="Passwort wiederholen"
                            name="passwordRepeat"
                            :type="passwordFieldType"
                            class="form-control"
                            aria-label="Passwort wiederholen"
                            aria-describedby="password-repeat-help-block"
                        />
                        <b-form-invalid-feedback>
                            {{ 'Die Passwörter sind unterschiedlich.' }}
                        </b-form-invalid-feedback>
                        <b-form-valid-feedback>
                            Schaut gut aus.
                        </b-form-valid-feedback>
                    </b-input-group>
                    <b-input-group
                        class="form-group input-group"
                    >
                        <b-button
                            :disabled="!passwordState || !passwordRepeatValidation || isLoading || isPasswordChanged"
                            block
                            variant="dark"
                            type="submit"
                            data-test="btn-change-password"
                            @click="changePassword()"
                        >
                            <b-spinner
                                v-if="isLoading"
                                variant="secondary"
                                small
                                class="mr-auto position-relative"
                                label="Spinning"
                            />
                            Passwort ändern
                        </b-button>
                    </b-input-group>
                    <general-error-alert v-if="hasError && !validationErrors.password" />
                </b-form>
                <div
                    v-if="isPasswordChanged && !hasError"
                    class="mt-3"
                >
                    <div
                        class="alert alert-success w-100 mb-0"
                        role="alert"
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
                        <router-link
                            v-if="!user"
                            :to="{ name: 'Login'}"
                            class="btn btn-dark btn-block text-white"
                        >
                            Zur Anmeldung
                        </router-link>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
    "use strict";
    import GeneralErrorAlert from './Common/GeneralErrorAlert.vue';
    import SecurityAPI from '../api/security';
    import { useAuthStore } from '../stores/auth';
    import { useUserStore } from '../stores/user';

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
            passwordHelp: '',
            passwordRepeatHelp: 'Bitte gib zwei Mal das gleiche Passwort ein um Tippfehler zu vermeiden.',
            isConfirmationTokenValid: true,
            passwordState: null,
            passwordInvalidText: '',
            passwordRepeatState: true,
            isPasswordChanged: false,
            passwordFieldType: 'password',
            eyeIcon: 'eye-fill',
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
            passwordRepeatValidation() {
                if (this.password.trim().length < 7 || this.passwordRepeat.trim().length < 7) {
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
                if (error.data && error.data['hydra:description']) {
                    errors.global = error.data['hydra:description'];
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
            passwordValidation(value) {
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
                this.eyeIcon = 'text' === this.passwordFieldType ? 'eye-slash-fill' : 'eye-fill';
            },
        },
    }
</script>

<style scoped>

</style>
