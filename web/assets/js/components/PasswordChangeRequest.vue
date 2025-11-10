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
                        Passwort ändern
                    </h2>
                    <v-alert prominent class="mb-5">
                        <ul class="text-left mt-3 pl-5">
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
                    </v-alert>
                    <div
                        class="mt-3 pb-2"
                    >
                        <v-btn
                            :disabled="isLoading || isPasswordRequested"
                            block
                            color="secondary"
                            type="submit"
                            density="default"
                            class="text-transform-none"
                            :loading="isLoading"
                            @click="requestPasswordReset()"
                        >
                            Neues Passwort beantragen
                        </v-btn>
                    </div>
                    <GeneralErrorAlert v-if="hasError"/>
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
                </v-card-text>
            </v-card>
        </v-col>
    </v-row>
</template>
<script>
"use strict";

import GeneralErrorAlert from './Common/GeneralErrorAlert.vue';
import {useAuthStore, useUserStore} from '../stores';

export default {
    name: "PasswordChangeRequest",
    components: {GeneralErrorAlert},
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
