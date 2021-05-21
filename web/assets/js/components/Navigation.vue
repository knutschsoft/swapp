<template>
    <div>
        <b-navbar
            toggleable="lg"
            type="dark"
            variant="dark"
            class="py-lg-0 pl-lg-0"
        >
            <b-navbar-toggle target="nav-collapse" />

            <b-navbar-brand
                :to="{ name: 'Dashboard' }"
                class="py-0 d-flex mr-0"
            >
                <img
                    :src="swappLogo"
                    class="navbar-logo"
                    alt="htwk-logo"
                >
            </b-navbar-brand>
            <div class="text-center d-flex justify-content-between mx-2">
                <b-spinner
                    v-if="isLoading"
                    variant="light"
                    type="grow"
                />
                <div
                    v-else
                    style="width: 32px;"
                />
            </div>

            <b-collapse
                id="nav-collapse"
                is-nav
            >
                <b-navbar-nav
                    justified
                    class="w-100"
                >
                    <b-nav-item
                        v-if="isAuthenticated"
                        :to="{ name: 'Dashboard' }"
                        :link-classes="linkClasses"
                        exact
                        exact-active-class="active"
                    >
                        Dashboard
                    </b-nav-item>
                    <b-nav-item
                        v-if="isSuperAdmin"
                        :to="{ name: 'Users' }"
                        :link-classes="linkClasses"
                        exact
                        exact-active-class="active"
                    >
                        Benutzer
                    </b-nav-item>
                    <b-nav-item
                        v-if="isAdmin"
                        :to="{ name: 'Teams' }"
                        :link-classes="linkClasses"
                        exact
                        exact-active-class="active"
                    >
                        Teams
                    </b-nav-item>
                    <b-nav-item
                        v-if="isSuperAdmin"
                        :to="{ name: 'Tags' }"
                        :link-classes="linkClasses"
                        exact
                        exact-active-class="active"
                    >
                        Tags
                    </b-nav-item>
                </b-navbar-nav>

                <!-- Right aligned nav items -->
                <b-navbar-nav class="ml-auto pl-2 pl-lg-0">
                    <b-nav-item-dropdown
                        right
                        :toggle-class="isUserMenuActive ? 'active router-link-active' : ''"
                    >
                        <!-- Using 'button-content' slot -->
                        <template v-slot:button-content >
                            <b-icon-person-fill />
                            <span
                                v-if="isAuthenticated"
                            >
                                {{ currentUser.email }}
                            </span>
                        </template>
                        <b-dropdown-item
                            v-if="!isAuthenticated"
                            :to="{ name: 'Login'}"
                            router-tag="button"
                            :active="$route.name === 'Login'"
                        >
                            Login
                        </b-dropdown-item>
                        <b-dropdown-item
                            v-if="!isAuthenticated"
                            :to="{ name: 'PasswordReset'}"
                            :active="$route.name === 'PasswordReset'"
                        >
                            Passwort vergessen?
                        </b-dropdown-item>
                        <b-dropdown-item
                            v-if="isAuthenticated"
                            :to="{ name: 'PasswordChangeRequest'}"
                            :active="$route.name === 'PasswordChangeRequest'"
                        >
                            Passwort ändern
                        </b-dropdown-item>
                        <b-dropdown-item
                            v-if="isAuthenticated"
                            :to="{ name: 'Logout'}"
                        >
                            Abmelden
                        </b-dropdown-item>
                    </b-nav-item-dropdown>
                </b-navbar-nav>
            </b-collapse>
        </b-navbar>
    </div>
</template>

<script>
    "use strict";
    // import logo from '../../images/Logo_white_bg.png';
    import logo from '../../images/Swapp_hp_logo.jpg';

    export default {
        name: "Navigation",
        data: () => ({
            // user: false,
            swappLogo: logo,
            linkClasses: 'text-left text-lg-center pl-2 pl-lg-0',
        }),
        computed: {
            isLoading() {
                return this.$store.getters['security/isLoading']
                    || this.$store.getters['user/isLoading']
                    || this.$store.getters['walk/isLoading']
                    || this.$store.getters['team/isLoading']
                    || this.$store.getters['wayPoint/isLoading'];
            },
            isAuthenticated() {
                return this.$store.getters['security/isAuthenticated'];
            },
            isAdmin() {
                return this.$store.getters['security/isAdmin'] || this.isSuperAdmin;
            },
            isSuperAdmin() {
                return this.$store.getters['security/isSuperAdmin'];
            },
            currentUser() {
                return this.$store.getters['security/currentUser'];
            },
            isUserMenuActive() {
                return -1 !== ['PasswordChangeRequest', 'Login', 'PasswordReset'].indexOf(this.$route.name);
            }
        },
        created() {
        },
        mounted: function () {
        }
    }
</script>

<style scoped>
    .navbar-logo {
        max-height: 37px !important;
        flex: 1 1 auto;
    }
</style>
