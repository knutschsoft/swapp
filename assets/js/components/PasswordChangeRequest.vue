<template>
    <div class="row m-auto pt-4 mt-4">
        <div
            class="col-sm-10 offset-sm-1 col-md-8 offset-md-2 offset-lg-3 col-lg-6 border border-dark p-4 mt-4"
        >
            <h2
                class="text-center"
            >
                Passwort ändern
            </h2>
            <ul class="text-left mt-3">
                <li>
                    Um Ihr Passwort zu ändern, drücken Sie bitte folgenden Knopf.
                </li>
                <li>
                    Sie bekommen dann eine E-Mail mit einem Link zugeschickt.
                </li>
                <li>
                    Mit Hilfe dieses Links können Sie dann ein neues Passwort setzen.
                </li>
            </ul>
            <div>
                <div
                    class="mt-3"
                >
                    <b-button
                        :disabled="isLoading || isPasswordRequested"
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
                        Neues Passwort beantragen
                    </b-button>
                </div>
                <div
                    v-if="hasError"
                    class="mt-3"
                >
                    <div
                        class="alert alert-danger w-100 mb-0"
                        role="alert"
                    >
                        <p
                            class="font-weight-bold"
                        >
                            Upps! Da ist etwas schief gelaufen!
                        </p>
                        <p class="mb-0">
                            Bitte informieren Sie
                            <a href="mailto:robertfreigang@gmx.de">
                                robertfreigang@gmx.de
                            </a>
                            über das Problem.
                        </p>
                    </div>
                </div>
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
        name: "PasswordChangeRequest",
        data: () => ({
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
            },
            user() {
                return this.$store.getters["security/currentUser"];
            },
        },
        created() {

        },
        methods: {
            async requestPasswordReset() {
                this.isPasswordRequested = await this.$store.dispatch(
                    "security/requestPasswordReset",
                    {email: this.user.email.email, honeypotEmail: ''}
                );
            }
        },
    }
</script>

<style scoped>

</style>
