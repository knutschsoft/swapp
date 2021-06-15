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
                        v-if="isAdmin"
                        :to="{ name: 'Users' }"
                        :link-classes="linkClasses"
                        exact
                        exact-active-class="active"
                    >
                        Benutzer
                    </b-nav-item>
                    <b-nav-item
                        v-if="isSuperAdmin"
                        :to="{ name: 'Clients' }"
                        :link-classes="linkClasses"
                        exact
                        exact-active-class="active"
                    >
                        Klienten
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
                        v-if="isAdmin"
                        :to="{ name: 'SystemicQuestions' }"
                        :link-classes="linkClasses"
                        exact
                        exact-active-class="active"
                    >
                        Systemische Fragen
                    </b-nav-item>
                    <b-nav-item
                        v-if="isAdmin"
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
                        data-test="nav-user-item"
                    >
                        <!-- Using 'button-content' slot -->
                        <template v-slot:button-content >
                            <b-icon-person-fill />
                            <span
                                v-if="isAuthenticated"
                            >
                                {{ currentUser.username }}
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
                            v-if="isUserSwitched"
                            router-tag="button"
                            :title="`Nutzerwechsel beenden`"
                            data-test="exit-switch-user"
                            @click="exitSwitchUser()"
                        >
                            Nutzerwechsel beenden
                        </b-dropdown-item>
                        <b-dropdown-item
                            v-if="isAuthenticated"
                            :to="{ name: 'Logout'}"
                        >
                            Abmelden
                        </b-dropdown-item>
                        <b-dropdown-divider />
                        <b-dropdown-item
                            :to="{ name: 'About'}"
                            :active="$route.name === 'About'"
                        >
                            Was ist Swapp?
                        </b-dropdown-item>
                        <b-dropdown-divider v-if="!isUserSwitched && isSuperAdmin" />
                        <b-dropdown-form
                            v-if="!isUserSwitched && isSuperAdmin"
                        >
                            <b-form-group
                                label="Nutzerwechsel"
                                label-for="nutzerwechsel-form-email"
                                @submit.stop.prevent
                            >
                                <b-form-input
                                    id="nutzerwechsel-form-email"
                                    v-model="userFilter"
                                    type="search"
                                    trim
                                    autocomplete="off"
                                    size="sm"
                                    placeholder="Benutzername"
                                />
                            </b-form-group>
                            <b-dropdown-group
                                v-if="!isUserSwitched && isSuperAdmin"
                            >
                                <b-dropdown-item-button
                                    v-for="(user, key) in displayedUserList"
                                    :key="key"
                                    button-class="text-truncate"
                                    style="font-size: 14px;"
                                    @click="switchUser(user)"
                                >
                                    {{ user.username }}
                                    ({{ Object.values(user.roles).map((currentRole) => {
                                        return currentRole === 'ROLE_USER' ? '' : `${currentRole.substring(5)} `
                                    }).join('')
                                    }}{{ (user.teams.length && (user.roles.length > 1)) ? ' - ' : '' }}{{ Object.values(user.teams).map((currentTeam) => {
                                        return currentTeam.name
                                    }).join('') }})
                                </b-dropdown-item-button>
                            </b-dropdown-group>
                        </b-dropdown-form>
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
            userFilter: '',
            users: [],
            swappLogo: logo,
            linkClasses: 'text-left text-lg-center pl-2 pl-lg-0',
        }),
        computed: {
            isLoading() {
                return this.$store.getters['client/isLoading']
                    || this.$store.getters['security/isLoading']
                    || this.$store.getters['systemicQuestion/isLoading']
                    || this.$store.getters['tag/isLoading']
                    || this.$store.getters['team/isLoading']
                    || this.$store.getters['user/isLoading']
                    || this.$store.getters['user/isLoadingChange']
                    || this.$store.getters['walk/isLoading']
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
            isUserSwitched() {
                return this.$store.getters['security/isUserSwitched'];
            },
            displayedUserList() {
                if (!this.users || !this.users.length) {
                    return [];
                }

                return this.users.slice(0).filter((user) => {
                    return -1 !== user.username.toLowerCase().indexOf(this.userFilter.toLowerCase());
                }).sort((a, b) => {
                    return (a.username.toLowerCase() > b.username.toLowerCase()) ? 1 : -1;
                });
            },
            currentUser() {
                return this.$store.getters['security/currentUser'];
            },
            isUserMenuActive() {
                return -1 !== ['PasswordChangeRequest', 'Login', 'PasswordReset'].indexOf(this.$route.name);
            }
        },
        watch: {
            userFilter() {
                this.$localStorage.set('nav-user-filter', this.userFilter);
            },
        },
        created() {
        },
        mounted: async function () {
            this.userFilter = this.$localStorage.get('nav-user-filter', '');
            if (this.isSuperAdmin) {
                this.users = await this.$store.dispatch('user/findAll');
            }
        },
        methods: {
            switchUser(user) {
                this.$store.dispatch('security/switchUser', user);
            },
            exitSwitchUser() {
                this.$store.dispatch('security/exitSwitchUser');
            },
        },
    }
</script>

<style scoped>
    .navbar-logo {
        max-height: 37px !important;
        flex: 1 1 auto;
    }
</style>
