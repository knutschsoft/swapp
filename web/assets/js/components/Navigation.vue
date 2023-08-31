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
                    class="navbar-logo rounded-sm"
                    alt="swapp-logo"
                >
            </b-navbar-brand>
            <div class="text-center d-none d-lg-flex justify-content-between mx-2">
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
            <b-button
                variant="link"
                class="text-right d-lg-none d-block"
                :to="{ name:'Changelog' }"
                :title="`Es gibt ${ hasNewChangelogItems ? '' : 'keine ' }Neuigkeiten für dich!`"
            >
                <mdicon
                    v-if="hasNewChangelogItems"
                    name="BellBadgeOutline"
                    size="26"
                    class="text-primary"
                />
                <mdicon
                    v-else
                    name="BellOutline"
                    size="26"
                    class="text-muted"
                />
            </b-button>
            <b-collapse
                id="nav-collapse"
                is-nav
            >
                <b-navbar-nav
                    fill
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
                    <b-button
                        variant="link"
                        class="text-right d-none d-lg-block"
                        :to="{ name: 'Changelog' }"
                        :title="`Es gibt ${ hasNewChangelogItems ? '' : 'keine ' }Neuigkeiten für dich!`"
                    >
                        <mdicon
                            v-if="hasNewChangelogItems"
                            name="BellBadgeOutline"
                            size="20"
                            class="text-primary"
                        />
                        <mdicon
                            v-else
                            name="BellOutline"
                            size="20"
                            class="text-muted"
                        />
                    </b-button>
                    <b-nav-item-dropdown
                        ref="userMenu"
                        right
                        lazy
                        :toggle-class="isUserMenuActive ? 'active router-link-active' : ''"
                        data-test="nav-user-item"
                        @show="showUserMenu"
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
                        <b-dropdown-item
                            :to="{ name: 'Changelog'}"
                            :active="$route.name === 'Changelog'"
                        >
                            Changelog
                            <b-badge
                                v-if="hasNewChangelogItems"
                                variant="primary"
                            >
                                Neue Einträge vorhanden!
                            </b-badge>
                        </b-dropdown-item>
                        <b-dropdown-item
                            :to="{ name: 'Faq'}"
                            :active="$route.name === 'Faq'"
                        >
                            FAQ - Häufig gestellte Fragen
                        </b-dropdown-item>
                        <b-dropdown-divider />
                        <b-dropdown-item
                            href="https://streetworkapp.de"
                            target="_blank"
                        >
                            Swapp-Homepage
                            <mdicon
                                name="open-in-new"
                                size="14"
                            />
                            <span class="text-muted">https://streetworkapp.de</span>
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
                                    :disabled="!user.isEnabled"
                                    @click="switchUser(user)"
                                >
                                    {{ user.username }}
                                    <span class="text-muted">{{ getAdditionalUserInfo(user) }}</span>
                                </b-dropdown-item-button>
                            </b-dropdown-group>
                        </b-dropdown-form>
                    </b-nav-item-dropdown>
                </b-navbar-nav>
            </b-collapse>
        </b-navbar>
        <b-progress
            class="d-lg-none w-100"
            height="3px"
            :variant="isLoading ? 'secondary' : 'dark'"
            :value="100"
            :animated="isLoading"
        />
        <div
            v-if="isOnDemoPage || isOnStagePage"
            class="px-2 py-0 small text-center bg-info w-full text-white"
            v-text="`Du befindest dich auf der ${isOnDemoPage ? 'Demo' : 'Stage'}-Version von Swapp.`"
        />
    </div>
</template>

<script>
    "use strict";
    // import logo from '../../images/Logo_white_bg.png';
    import logo from '../../images/Swapp_hp_logo.jpg';
    import { useClientStore } from '../stores/client';
    import { useSystemicQuestionStore } from '../stores/systemic-question';
    import { useTagStore } from '../stores/tag';
    import { useTeamStore } from '../stores/team';
    import { useChangelogStore } from '../stores/changelog';
    import { useWayPointStore } from '../stores/way-point';
    import { useWalkStore } from '../stores/walk';

    export default {
        name: "Navigation",
        data: () => ({
            changelogStore: useChangelogStore(),
            clientStore: useClientStore(),
            teamStore: useTeamStore(),
            systemicQuestionStore: useSystemicQuestionStore(),
            tagStore: useTagStore(),
            walkStore: useWalkStore(),
            wayPointStore: useWayPointStore(),
            userFilter: '',
            users: [],
            swappLogo: logo,
            linkClasses: 'text-left text-lg-center pl-2 pl-lg-0',
        }),
        computed: {
            isOnDemoPage() {
                return window.location.host.includes('swapp.demo') || this.$route.query.demo;
            },
            isOnStagePage() {
                return window.location.host.includes('swapp.stage') || this.$route.query.stage;
            },
            isLoading() {
                return this.clientStore.isLoading
                    || this.$store.getters['security/isLoading']
                    || this.systemicQuestionStore.isLoading
                    || this.tagStore.isLoading
                    || this.teamStore.isLoading
                    || this.$store.getters['user/isLoading']
                    || this.$store.getters['user/isLoadingChange']
                    || this.walkStore.isLoading
                    || this.walkStore.isLoading
                    || this.wayPointStore.isLoading;
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
            },
            hasNewChangelogItems() {
                return this.changelogStore.hasNewChangelogItems;
            },
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
        },
        methods: {
            switchUser(user) {
                this.$store.dispatch('security/switchUser', user);
            },
            exitSwitchUser() {
                this.$store.dispatch('security/exitSwitchUser');
            },
            getClientByIri(clientIri) {
                return this.clientStore.getClientByIri(clientIri);
            },
            async showUserMenu(bvEvent) {
                if (this.isSuperAdmin && this.users.length <= 1) {
                    bvEvent.preventDefault();
                    this.users = (await this.$store.dispatch('user/findAll')).slice(0).filter(user => user.isEnabled);
                    await this.clientStore.fetchClients();
                    this.$refs.userMenu.show();
                }
            },
            getAdditionalUserInfo(user) {
                let trimLength = 7;
                let usernameLength = 11;
                let doShorten = false;
                if (user.username.length > usernameLength) {
                    doShorten = true;
                }

                let additionalUserInfo = Object.values(user.roles).map((currentRole) => {
                    if ('ROLE_USER' === currentRole || 'ROLE_SUPER_ADMIN' === currentRole) {
                        return '';
                    }
                    if ('ROLE_ALLOWED_TO_SWITCH' === currentRole) {
                        return '';
                    }
                    if ('ROLE_ADMIN' === currentRole) {
                        return '👨‍💼 ';
                    }

                    return `${currentRole.substring(5)} `;
                }).join(' ');
                let teams = Object.values(user.teams).map((currentTeam) => {
                    return currentTeam.name
                }).join(', ')
                if ((doShorten || teams.length > 20) && teams !== teams.substring(0, trimLength)) {
                    teams = `${teams.substring(0, trimLength)}...`;
                }
                additionalUserInfo += teams;


                if ('' !== additionalUserInfo.trim()) {
                    additionalUserInfo = additionalUserInfo.trim() + ' - ';
                }
                let clientName = this.getClientByIri(user.client).name;
                if (doShorten && clientName && clientName !== clientName.substring(0, trimLength)) {
                    clientName = `${clientName.substring(0, trimLength)}...`;
                }
                additionalUserInfo += ` ${clientName}`;

                return `${additionalUserInfo.trim()}`;
            },
        },
    }
</script>

<style scoped>
    .navbar-logo {
        max-height: 37px !important;
        flex: 1 1 auto;
    }
    @media screen and (min-width: 992px) {
        .navbar-logo {
            border-radius: 0 !important;
        }
    }
    .progress {
        border-radius: 0;
    }
</style>
