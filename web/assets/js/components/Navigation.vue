<template>
    <v-navigation-drawer
        v-model="drawer"
        app
        disable-resize-watcher
        mobile
    >
        <v-list density="compact">
            <v-list-item
                v-if="isAuthenticated"
                :to="{ name: 'Dashboard' }"
                exact
            >
                <v-list-item-title>Dashboard</v-list-item-title>
            </v-list-item>
            <v-list-item
                v-if="isAdmin"
                :to="{ name: 'Users' }"
                exact
            >
                <v-list-item-title>Benutzer</v-list-item-title>
            </v-list-item>
            <v-list-item
                v-if="isSuperAdmin"
                :to="{ name: 'Clients' }"
                exact
            >
                <v-list-item-title>Klienten</v-list-item-title>
            </v-list-item>
            <v-list-item
                v-if="isAdmin"
                :to="{ name: 'Teams' }"
                exact
            >
                <v-list-item-title>Teams</v-list-item-title>
            </v-list-item>
            <v-list-item
                v-if="isAdmin"
                :to="{ name: 'SystemicQuestions' }"
                exact
            >
                <v-list-item-title>Systemische Fragen</v-list-item-title>
            </v-list-item>
            <v-list-item
                v-if="isAdmin"
                :to="{ name: 'Tags' }"
                exact
            >
                <v-list-item-title>Tags</v-list-item-title>
            </v-list-item>
        </v-list>
    </v-navigation-drawer>
    <v-app-bar
        app
        fixed
        density="compact"
    >
        <v-toolbar
            density="compact"
            :elevation="5"
        >
            <v-app-bar-nav-icon
                v-if="isAuthenticated"
                @click="drawer = !drawer"
                class="d-md-none"
                density="comfortable"
            />
            <v-spacer class="d-md-none" />
            <v-btn :to="{ name: 'Dashboard' }"
                   variant="text"
                   style="min-width: 200px;max-width: 220px;"
                   class="pr-0 pl-0 pl-md-0 pl-lg-4 d-inline-block mt-0 pt-0"
                   height="48"
            >
                <v-img
                   :src="swappLogo"
                    class="rounded"
                    height="48"
                />
            </v-btn>
            <div class="d-none d-md-block mx-1 mx-lg-3" style="width: 34px;">
                <v-progress-circular v-if="isLoading" indeterminate color="white" size="24" />
            </div>
            <v-spacer class="d-none d-md-block" />
            <v-tabs v-if="isSuperAdmin" grow class="d-none d-md-block d-lg-none" mobile density="compact">
                <v-tab v-if="isAuthenticated" :to="{ name: 'Dashboard' }" exact slim size="small" class="text-transform-none">Dashboard</v-tab>
                <v-tab v-if="isAdmin" :to="{ name: 'Users' }" exact slim size="small" class="text-transform-none">Benutzer</v-tab>
                <v-tab v-if="isSuperAdmin" :to="{ name: 'Clients' }" exact slim size="small" class="text-transform-none">Klienten</v-tab>
                <v-tab v-if="isAdmin" :to="{ name: 'Teams' }" exact slim size="small" class="text-transform-none">Teams</v-tab>
                <v-tab v-if="isAdmin" :to="{ name: 'SystemicQuestions' }" exact slim size="small" class="text-transform-none">Systemische Fragen</v-tab>
                <v-tab v-if="isAdmin" :to="{ name: 'Tags' }" exact slim size="small" class="text-transform-none">Tags</v-tab>
            </v-tabs>
            <v-tabs grow :class="`${isSuperAdmin ? 'd-none d-lg-block' : 'd-none d-md-block'}`" mobile  density="compact">
                <v-tab v-if="isAuthenticated" :to="{ name: 'Dashboard' }" exact slim class="text-transform-none">Dashboard</v-tab>
                <v-tab v-if="isAdmin" :to="{ name: 'Users' }" exact slim class="text-transform-none">Benutzer</v-tab>
                <v-tab v-if="isSuperAdmin" :to="{ name: 'Clients' }" exact slim class="text-transform-none">Klienten</v-tab>
                <v-tab v-if="isAdmin" :to="{ name: 'Teams' }" exact slim class="text-transform-none">Teams</v-tab>
                <v-tab v-if="isAdmin" :to="{ name: 'SystemicQuestions' }" exact slim class="text-transform-none">Systemische Fragen</v-tab>
                <v-tab v-if="isAdmin" :to="{ name: 'Tags' }" exact slim class="text-transform-none">Tags</v-tab>
            </v-tabs>
            <v-spacer />
            <v-btn
                icon
                :to="{ name: 'Changelog' }"
                :title="`Es gibt ${hasNewChangelogItems ? '' : 'keine '}Neuigkeiten für dich!`"
                class="d-flex"
                density="comfortable"
            >
                <v-icon color="primary" v-if="hasNewChangelogItems">mdi-bell-badge-outline</v-icon>
                <v-icon color="grey lighten-1" v-else>mdi-bell-outline</v-icon>
            </v-btn>
            <v-menu
                v-model="menu"
                location="bottom"
                eager
                :close-on-content-click="false"
                @update:modelValue="showUserMenu"
                allow-overflow
                width="400"
            >
                <template v-slot:activator="{ props }">
                    <v-btn data-test="nav-user-item" variant="text" v-bind="props" class="text-transform-none" @click="">
                        <v-icon>mdi-account</v-icon>
                        <span v-if="isAuthenticated" class="d-none d-sm-block">{{ currentUser?.username }}</span>
                    </v-btn>
                </template>
                <v-list>
                    <v-list-item
                        v-if="!isAuthenticated"
                        :to="{ name: 'Login' }"
                        exact
                        link
                        @click="menu = false"
                    >
                        <v-list-item-title>Login</v-list-item-title>
                    </v-list-item>
                    <v-list-item
                        v-if="!isAuthenticated"
                        :to="{ name: 'PasswordReset' }"
                        exact
                        link
                        @click="menu = false"
                    >
                        <v-list-item-title>Passwort vergessen?</v-list-item-title>
                    </v-list-item>
                    <v-list-item
                        v-if="isAuthenticated"
                        :to="{ name: 'PasswordChangeRequest' }"
                        exact
                        link
                        @click="menu = false"
                    >
                        <v-list-item-title>Passwort ändern</v-list-item-title>
                    </v-list-item>
                    <v-list-item
                        v-if="isUserSwitched"
                        data-test="exit-switch-user"
                        @click="exitSwitchUser"
                    >
                        <v-list-item-title>Nutzerwechsel beenden</v-list-item-title>
                    </v-list-item>
                    <v-list-item
                        v-if="isAuthenticated"
                        :to="{ name: 'Logout' }"
                        exact
                        link
                        data-test="nav-user-logout"
                        @click="menu = false"
                    >
                        <v-list-item-title>Abmelden</v-list-item-title>
                    </v-list-item>
                    <v-divider />
                    <v-list-item
                        :to="{ name: 'About' }"
                        exact
                        link
                        @click="menu = false"
                    >
                        <v-list-item-title>Was ist Swapp?</v-list-item-title>
                    </v-list-item>
                    <v-list-item
                        :to="{ name: 'Changelog' }"
                        exact
                        link
                        @click="menu = false"
                    >
                        <v-list-item-title>
                            Changelog
                            <v-badge v-if="hasNewChangelogItems" color="primary" content="Neu" floating class="ml-2" />
                        </v-list-item-title>
                    </v-list-item>
                    <v-list-item
                        :to="{ name: 'Faq' }"
                        exact
                        link
                        @click="menu = false"
                    >
                        <v-list-item-title>FAQ</v-list-item-title>
                    </v-list-item>
                    <v-divider />
                    <v-list-item
                        href="https://streetworkapp.de"
                        target="_blank"
                        @click="menu = false"
                    >
                        <v-list-item-title>Swapp-Homepage <v-icon small>mdi-open-in-new</v-icon></v-list-item-title>
                    </v-list-item>
                    <v-divider v-if="!isUserSwitched && isSuperAdmin" />
                    <v-list-item v-if="!isUserSwitched && isSuperAdmin">
                        <v-list-item-title>Nutzerwechsel</v-list-item-title>
                        <v-text-field
                            v-model="generalStore.navUserFilter"
                            label="Benutzer eingrenzen"
                            type="search"
                            clearable
                            variant="outlined"
                            density="compact"
                            class="mt-2 mb-2"
                            hide-details
                            placeholder="Benutzer eingrenzen"
                        />
                    </v-list-item>
                    <v-divider v-if="!isUserSwitched && isSuperAdmin" />
                    <v-list v-if="!isUserSwitched && isSuperAdmin && displayedUserList.length" density="compact" nav>
                        <v-list-item
                            v-for="(user, key) in displayedUserList"
                            :key="key"
                            density="compact"
                            @click="switchUser(user);"
                            :disabled="!user.isEnabled"
                        >
                            <v-list-item-title>
                                {{ hasUserRoleAdmin(user) ? '👨‍💼 ' : '' }}{{ user.username }}
                                <span class="text-disabled font-weight-regular ml-2">
                                    <v-icon icon="mdi-eye-outline" size="x-small" /> {{ user.lastLoginAt ? formatDateTimeNoSeconds(user.lastLoginAt) : 'nie' }}
                                </span>
                            </v-list-item-title>
                            <v-list-item-subtitle
                                :title="getAdditionalUserInfo(user)"
                            >{{ getAdditionalUserInfo(user) }}</v-list-item-subtitle>
                        </v-list-item>
                        <v-divider v-if="!isUserSwitched && isSuperAdmin && displayedUserList.length" />
                    </v-list>
                </v-list>
            </v-menu>
        </v-toolbar>
    </v-app-bar>
</template>

<script>
    "use strict";
    // import logo from '../../images/Logo_white_bg.png';
    import logo from '../../images/Swapp_hp_logo.jpg';
    import {
        useAuthStore,
        useChangelogStore,
        useClientStore,
        useGeneralStore,
        useSystemicQuestionStore,
        useTagStore,
        useTeamStore,
        useUserStore,
        useWalkStore,
        useWayPointStore,
    } from '../stores';
    import {formatDateTimeNoSeconds, formatDateTimeNoSecondsWithDayOfWeek} from "@/js/utils";


    export default {
        name: "Navigation",
        components: {
        },
        data: () => ({
            authStore: useAuthStore(),
            changelogStore: useChangelogStore(),
            generalStore: useGeneralStore(),
            clientStore: useClientStore(),
            teamStore: useTeamStore(),
            systemicQuestionStore: useSystemicQuestionStore(),
            tagStore: useTagStore(),
            userStore: useUserStore(),
            walkStore: useWalkStore(),
            wayPointStore: useWayPointStore(),
            drawer: false,
            users: [],
            swappLogo: logo,
            menu: false,
            linkClasses: 'text-left text-lg-center pl-2 pl-lg-0',
        }),
        computed: {
            isLoading() {
                return this.clientStore.isLoadingFetch
                    || this.authStore.isLoading
                    || this.systemicQuestionStore.isLoading
                    || this.tagStore.isLoading
                    || this.teamStore.isLoading
                    || this.userStore.isLoading
                    || this.walkStore.isLoading
                    || this.walkStore.isLoading
                    || this.wayPointStore.isLoading;
            },
            isAuthenticated() {
                return this.authStore.isAuthenticated;
            },
            isAdmin() {
                return this.authStore.isAdmin || this.isSuperAdmin;
            },
            isSuperAdmin() {
                return this.authStore.isSuperAdmin;
            },
            isUserSwitched() {
                return this.authStore.isUserSwitched;
            },
            displayedUserList() {
                if (!this.users || !this.users.length) {
                    return [];
                }

                const searchString = this.generalStore.navUserFilter.toLowerCase();
                return this.users.slice(0).filter((user) => {
                    if (-1 !== user.username.toLowerCase().indexOf(searchString)) {
                        return true;
                    }
                    for (const team of user.teams) {
                        if (-1 !== team.name.toLowerCase().indexOf(searchString)) {
                            return true;
                        }
                    }
                    for (const role of user.roles) {
                        if (-1 !== role.toLowerCase().indexOf(searchString)) {
                            return true;
                        }
                    }
                    const client = this.getClientByIri(user.client);

                    return client && -1 !== client.name.toLowerCase().indexOf(searchString);
                }).sort((a, b) => {
                    return (a.username.toLowerCase() > b.username.toLowerCase()) ? 1 : -1;
                });
            },
            currentUser() {
                return this.authStore.currentUser;
            },
            hasNewChangelogItems() {
                return this.changelogStore.hasNewChangelogItems;
            },
        },
        methods: {
            formatDateTimeNoSeconds,
            formatDateTimeNoSecondsWithDayOfWeek,
            switchUser(user) {
                this.menu = false;
                this.authStore.switchUser(user);
            },
            exitSwitchUser() {
                this.menu = false;
                this.authStore.exitSwitchUser();
            },
            getClientByIri(clientIri) {
                return this.clientStore.getClientByIri(clientIri);
            },
            async showUserMenu() {
                if (this.isSuperAdmin && this.users.length <= 1) {
                    this.users = (await this.userStore.fetchUsers()).slice(0).filter(user => user.isEnabled);
                    await this.clientStore.fetchClients();
                }
            },
            hasUserRoleAdmin(user) {
                return user.roles.includes('ROLE_ADMIN');
            },
            getAdditionalUserInfo(user) {
                let additionalUserInfo = '';
                let teams = Object.values(user.teams)
                    .map((team) => {
                        return team.name
                    })
                    .sort((teamNameA, teamNameB) => teamNameA.toLowerCase().localeCompare(teamNameB.toLowerCase()))
                    .join(', ')
                additionalUserInfo += teams;


                if ('' !== additionalUserInfo.trim()) {
                    additionalUserInfo = additionalUserInfo.trim() + ' - ';
                }
                let clientName = this.getClientByIri(user.client)?.name;
                additionalUserInfo += ` ${clientName}`;

                return `${additionalUserInfo.trim()}`;
            },
        },
    }
</script>

<style scoped>
</style>
