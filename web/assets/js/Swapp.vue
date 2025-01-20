<template>
    <FrameError @error="showSnackbar">
        <v-app>
            <ReloadPrompt />
            <v-alert
                v-if="showError"
                type="error"
                prominent
                class="position-fixed fixed-top m-0 rounded-0"
                style="z-index: 2000;"
                dismissible
            >
                {{ errorData }}
            </v-alert>
            <UseNetwork v-slot="{ isOnline }">
                <v-system-bar
                    v-if="!isOnline && isOnDemoOrStagePage"
                    app
                    fixed
                    class="m-0 p-0"
                    height="36"
                >
                    <v-row>
                        <v-col
                            v-if="!isOnline"
                            cols="12"
                            class="px-2 py-0 small text-center bg-danger w-full text-white font-weight-bold"
                        >
                            <mdicon name="WifiOff" size="18"/>
                            Keine Internetverbindung
                        </v-col>
                        <v-col
                            v-if="isOnDemoOrStagePage"
                            cols="12"
                            class="px-2 py-0 small text-center bg-info w-full text-white"
                            v-text="`Du befindest dich auf der ${isOnDemoPage ? 'Demo' : 'Stage'}-Version von Swapp.`"
                        >
                        </v-col>
                    </v-row>
                </v-system-bar>
                <v-system-bar
                    v-else-if="!isOnline || isOnDemoOrStagePage"
                    app
                    fixed
                    class="m-0 p-0"
                    height="18"
                >
                    <v-row>
                        <v-col
                            v-if="!isOnline"
                            cols="12"
                            class="px-2 py-0 small text-center bg-danger w-full text-white font-weight-bold"
                        >
                            <mdicon name="WifiOff" size="18"/>
                            Keine Internetverbindung
                        </v-col>
                        <v-col
                            v-if="isOnDemoOrStagePage"
                            cols="12"
                            class="px-2 py-0 small text-center bg-info w-full text-white"
                            v-text="`Du befindest dich auf der ${isOnDemoPage ? 'Demo' : 'Stage'}-Version von Swapp.`"
                        >
                        </v-col>
                    </v-row>
                </v-system-bar>
            </UseNetwork>

            <navigation />
            <v-main>
                <v-container fluid>
                    <router-view />
                </v-container>
            </v-main>
            <v-snackbar
                v-model="alertStore.showAlert"
                multi-line
                timeout="6000"
                outlined
                variant="outlined"
                top
                right
                :color="alertStore.alert?.type ?? undefined"
            >
                <div v-if="alertStore.alert?.title" class="pb-2 text-overline">{{ alertStore.alert?.title }}</div>
                {{ alertStore.alert?.message }}
                <template v-slot:actions>
                    <v-btn variant="text" @click="alertStore.clear()">
                        <v-icon icon="mdi-close"></v-icon>
                    </v-btn>
                </template>
            </v-snackbar>
        </v-app>
    </FrameError>
</template>

<script>
import Navigation from './components/Navigation.vue';
import FrameError from './components/FrameError';
import ReloadPrompt from "./components/ReloadPrompt.vue"
import dayjs from 'dayjs';
import { UseNetwork } from '@vueuse/components';
import { useAlertStore, useAuthStore, useChangelogStore } from './stores';
import apiClient from './api';

export default {
    name: 'Swapp',
    components: {ReloadPrompt, FrameError, Navigation, UseNetwork},
    props: {},
    data() {
        return {
            alertStore: useAlertStore(),
            authStore: useAuthStore(),
            changelogStore: useChangelogStore(),
            errorData: '',
            isUpdateLoading: false,
            showUpdateUI: false,
            showError: false,
            oldToasterValue: '',
        }
    },
    computed: {
        currentUser() {
            return this.authStore.user;
        },
        isOnDemoPage() {
            return window.location.host.includes('swapp.demo') || this.$route.query.demo;
        },
        isOnStagePage() {
            return window.location.host.includes('swapp.stage') || this.$route.query.stage;
        },
        isOnDemoOrStagePage() {
            return this.isOnDemoPage || this.isOnStagePage;
        }
    },
    mounted() {
        if (!this.currentUser) {
            return;
        }
        const lastVisitedAtOfUserLogin = dayjs(this.currentUser.lastLoginAt);
        if (lastVisitedAtOfUserLogin.isBefore(this.changelogStore.getLastVisitedAt)) {
            return;
        }
        this.changelogStore.updateLastVisitedAt(lastVisitedAtOfUserLogin);
    },
    created() {
        const that = this;
        apiClient.interceptors.response.use(undefined, (err) => {
            if (this.$route.name === 'Logout') {
                return Promise.reject(err);
            }
            if (this.$route.name === 'Login') {
                return Promise.reject(err);
            }
            if (err.response && err.response.status && err.response.data) {
                if (403 === err.response.status && 'Your token is invalid, please login again to get a new one' === err.response.data.message && this.$route.name !== 'Logout'
                    || 401 === err.response.status && 'Expired JWT Token' === err.response.data.message) {
                    that.alertStore.info('Dies ist passiert, da deine letzte Anmeldung zu lange her ist. Bitte melde dich erneut an.', 'Du wurdest automatisch abgemeldet.');
                    this.$router.push({ name: 'Logout' });
                    return;
                }
                if ('Switch User failed: ' === err.response.data.detail) {
                    this.$router.push({name: "Logout"});
                    return;
                }
            }

            return Promise.reject(err);
        });
    },
    methods: {
        showSnackbar(error) {
            if (this.$route.name === 'Logout') {
                return;
            }
            let message = '';
            let title = '';
            let isProd = process.env.NODE_ENV === 'production';
            if (isProd) {
                message = `Das hätte nicht passieren dürfen. Wende dich bitte mit einer Beschreibung zur Reproduktion des Fehlers an info@streetworkapp.de`;
            } else {
                message = `
                    Fehlermeldung: ${error.message}
                    Stacktrace: ${error.stack}
                    `;
            }
            if (message !== this.oldToasterValue) {
                title = 'Upps! Es ist ein unerwarteter Fehler aufgetreten!'
                this.alertStore.error(message, title);
            }
            this.oldToasterValue = message;
        },
    },
}
</script>

<style lang="scss" scoped>
.slide-fade-enter-active {
    transition: all .3s ease;
}
.slide-fade-leave-active {
    transition: all .8s cubic-bezier(1.0, 0.5, 0.8, 1.0);
}
.slide-fade-enter, .slide-fade-leave-to
    /* .slide-fade-leave-active below version 2.1.8 */ {
    transform: translateX(10px);
    opacity: 0;
}

.fade-enter-active, .fade-leave-active {
    transition-property: opacity;
    transition-duration: .25s;
}

.fade-enter-active {
    transition-delay: .25s;
}

.fade-enter, .fade-leave-active {
    opacity: 0
}
</style>
