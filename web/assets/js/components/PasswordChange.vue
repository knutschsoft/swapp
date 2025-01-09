<template>
    <div class="">
        <v-alert
            v-if="!isConfirmationTokenValid"
            type="warning"
        >
            Upps! Der von dir genutzte Link ist nicht länger gültig.
            <br>
            Bitte schaue nach, ob in deinem E-Mail-Postfach eine neuere E-Mail mit Link vorhanden ist oder beantrage
            nochmal ein neues Passwort.
            <v-btn
                block
                class="my-3"
                color="secondary"
                :to="{ name: user ? 'PasswordChangeRequest' : 'PasswordReset' }"
            >
                Passwortänderung beantragen
            </v-btn>
        </v-alert>
        <div
            v-else
            class="col-sm-10 offset-sm-1 col-md-8 offset-md-2 offset-lg-3 col-lg-6 border border-dark p-4 mt-4"
        >
            <h2
                class="text-center"
            >
                Passwort ändern
            </h2>
            <v-form
                novalidate
                @submit.stop.prevent
            >
                <v-text-field
                    v-model="password"
                    prepend-inner-icon="mdi-lock-outline"
                    :append-icon="isPasswordVisible ? 'mdi-eye-off-outline' : 'mdi-eye-outline'"
                    autofocus
                    :type="passwordFieldType"
                    label="Passwort"
                    placeholder="Passwort"
                    name="password"
                    data-test="username"
                    autocomplete="username email"
                    :disabled="isPasswordChanged || isLoading"
                    dense
                    outlined
                    @click:append="switchPasswordVisibility"
                />
                <v-text-field
                    v-model="passwordRepeat"
                    prepend-inner-icon="mdi-lock-outline"
                    :append-icon="isPasswordVisible ? 'mdi-eye-off-outline' : 'mdi-eye-outline'"
                    autofocus
                    :type="passwordFieldType"
                    label="Passwort wiederholen"
                    placeholder="Passwort"
                    name="password"
                    data-test="username"
                    autocomplete="username email"
                    hide
                    :disabled="isPasswordChanged || isLoading"
                    dense
                    outlined
                    @input="passwordValidation"
                    @click:append="switchPasswordVisibility"
                />
                <v-alert
                    v-if="false === passwordState && passwordInvalidText"
                    type="warning"
                >
                    {{ passwordInvalidText }}
                </v-alert>
                <v-alert
                    v-if="false === passwordRepeatValidation"
                    type="warning"
                >
                    {{ 'Die Passwörter sind unterschiedlich.' }}
                </v-alert>
                <v-btn
                    :disabled="!passwordState || !passwordRepeatValidation || isLoading || isPasswordChanged"
                    block
                    color="secondary"
                    type="submit"
                    data-test="btn-change-password"
                    @click="changePassword()"
                    class="mb-3"
                >
                    <v-progress-circular
                        v-if="isLoading"
                        :width="2"
                        :size="20"
                        indeterminate
                        class="mr-2 position-relative"
                    />
                    Passwort ändern
                </v-btn>
                <general-error-alert v-if="hasError && !validationErrors.password" />
            </v-form>
            <div
                v-if="isPasswordChanged && !hasError"
                class="mb-3"
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
                    <v-btn
                        v-if="!user"
                        :to="{ name: 'Login'}"
                        color="secondary"
                        block
                    >
                        Zur Anmeldung
                    </v-btn>
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
