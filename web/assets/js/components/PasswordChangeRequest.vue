<template>
    <div class="row m-auto pt-4 mt-4">
        <div
            class="col-sm-10 offset-sm-1 col-md-8 offset-md-2 offset-lg-3 col-lg-6 border border-dark p-3 mt-3"
        >
            <h2
                class="text-center"
            >
                Passwort ändern
            </h2>
            <ul class="text-left mt-3">
                <li>
                    Um dein Passwort zu ändern, drücke bitte folgenden Knopf.
                </li>
                <li>
                    Du bekommst dann eine E-Mail mit einem Link zugeschickt.
                </li>
                <li>
                    Mit Hilfe dieses Links kannst du dir dann ein neues Passwort setzen.
                </li>
            </ul>
            <div class="">
                <div
                    class="mt-3 pb-2"
                >
                    <v-btn
                        :disabled="isLoading || isPasswordRequested"
                        block
                        color="secondary"
                        type="submit"
                        @click="requestPasswordReset()"
                    >
                        <v-progress-circular
                            v-if="isLoading"
                            :width="2"
                            :size="20"
                            indeterminate
                            class="mr-2 position-relative"
                        />
                        Neues Passwort beantragen
                    </v-btn>
                </div>
                <GeneralErrorAlert v-if="hasError" />
                <v-alert
                    v-if="isPasswordRequested && !hasError"
                    prominent
                    type="success"
                    class="mt-2"
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
            </div>
        </div>
    </div>
</template>
<script>
    "use strict";

    import GeneralErrorAlert from './Common/GeneralErrorAlert.vue';
    import { useAuthStore, useUserStore } from '../stores';
    export default {
        name: "PasswordChangeRequest",
        components: { GeneralErrorAlert },
        data: () => ({
            authStore: useAuthStore(),
            userStore: useUserStore(),
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
            user() {
                return this.authStore.currentUser;
            },
        },
        created() {

        },
        methods: {
            async requestPasswordReset() {
                await this.userStore.requestPasswordReset(
                    {username: this.user.email, email: ''}
                );
                this.isPasswordRequested = true;
            }
        },
    }
</script>

<style scoped>

</style>
