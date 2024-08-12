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
            <navigation />

            <div
                class="pb-5"
            >
                <vue-page-transition name="fade">
                    <router-view
                        class="pb-3 absolute w-100 col-12 col-xxl-10 offset-xxl-1 px-1 px-sm-2"
                    />
                </vue-page-transition>
            </div>
        </v-app>
    </FrameError>
</template>

<script>
import Navigation from './components/Navigation.vue';
import FrameError from './components/FrameError';
import ReloadPrompt from "./components/ReloadPrompt.vue";
import dayjs from 'dayjs';
import { useChangelogStore } from './stores/changelog';
import { useAuthStore } from './stores/auth';
import apiClient from './api';

export default {
    name: 'Swapp',
    components: {ReloadPrompt, FrameError, Navigation},
    props: {},
    data() {
        return {
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
                    let options = {
                        title: 'Du wurdest automatisch abgemeldet.',
                        toaster: 'b-toaster-top-right',
                        autoHideDelay: 10000,
                        appendToast: false,
                        variant: 'info',
                    };
                    this.$bvToast.toast('Dies ist passiert, da deine letzte Anmeldung zu lange her ist. Bitte melde dich erneut an.', options);
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
                let options = {
                    title: 'Upps! Es ist ein unerwarteter Fehler aufgetreten!',
                    toaster: 'b-toaster-top-right',
                    autoHideDelay: 10000,
                    appendToast: false,
                    variant: 'danger',
                };
                if (isProd) {
                    options.href = 'mailto:info@streetworkapp.de';
                }
                this.$bvToast.toast(message, options);
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
