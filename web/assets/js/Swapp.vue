<template>
    <FrameError @error="showSnackbar">
        <div>
            <b-alert
                v-model="showError"
                class="position-fixed fixed-top m-0 rounded-0"
                style="z-index: 2000;"
                dismissible
            >
                {{ errorData }}
            </b-alert>
            <navigation />

            <div
                class="pb-5"
            >
                <vue-page-transition name="fade">
                    <router-view
                        class="pb-3 absolute w-100 col-12 col-lg-10 offset-lg-1 px-1 px-sm-2"
                    />
                </vue-page-transition>
            </div>

            <b-navbar
                v-if="false"
                fixed="bottom"
                type="dark"
                variant="dark"
                class="py-0 px-5"
            >
                <b-navbar-nav
                    justified
                    class="w-100"
                >
                    <b-nav-item
                        :to="{ name: 'Impressum' }"
                        link-classes=""
                        exact
                        exact-active-class="active"
                    >
                        Impressum
                    </b-nav-item>
                    <b-nav-item
                        :to="{ name: 'Datenschutz' }"
                        link-classes=""
                        exact
                        exact-active-class="active"
                    >
                        Datenschutz
                    </b-nav-item>
                </b-navbar-nav>
            </b-navbar>
        </div>
    </FrameError>
</template>

<script>
import Navigation from './components/Navigation';
import FrameError from './components/FrameError';

export default {
    name: 'Swapp',
    components: {FrameError, Navigation},
    props: {},
    data() {
        return {
            errorData: '',
            showError: false,
            oldToasterValue: '',
        }
    },
    computed: {},
    created() {
        this.axios.interceptors.response.use(undefined, (err) => {
            if (err.response && err.response.status && 403 === err.response.status && err.response.data) {
                if ('Your token is invalid, please login again to get a new one' === err.response.data.message) {
                    this.$router.push({name: "Logout"});
                    return;
                }
                if ('Switch User failed: ' === err.response.data.detail) {
                    this.$router.push({name: "Logout"});
                    return;
                }
            }

            return Promise.reject(err.response);
        });
    },
    methods: {
        showSnackbar(error) {
            let message = '';
            let isProd = process.env.NODE_ENV === 'production';
            if (isProd) {
                message = `Wende dich bitte an info@streetworkapp.de`;
            } else {
                message = `
                    Fehlermeldung: ${error.message}
                    Stacktrace: ${error.stack}
                    `;
            }
            if (message !== this.oldToasterValue) {
                let options = {
                    title: 'Es ist ein unerwarteter Fehler aufgreten!',
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
