<script setup lang="ts">

import logo from '../../images/Swapp_hp_logo.jpg'
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
} from '../stores'
import {formatDateTimeNoSeconds, formatDateTimeNoSecondsWithDayOfWeek} from '@/js/utils'
import {computed, ref} from 'vue'
import {type Client, User} from "@/js/model";
import UserPreferencesForm from "@/js/components/Users/UserPreferencesForm.vue";

const authStore = useAuthStore()
const changelogStore = useChangelogStore()
const generalStore = useGeneralStore()
const clientStore = useClientStore()
const teamStore = useTeamStore()
const systemicQuestionStore = useSystemicQuestionStore()
const tagStore = useTagStore()
const userStore = useUserStore()
const walkStore = useWalkStore()
const wayPointStore = useWayPointStore()

const drawer = ref(false)
const users = ref<User[]>([])
const swappLogo = logo
const userMenu = ref(false)
const showUserPreferencesForm = ref(false);
const linkClasses = 'text-left text-lg-center pl-2 pl-lg-0'

const isLoading = computed(() =>
    clientStore.isLoadingFetch ||
    authStore.isLoading ||
    systemicQuestionStore.isLoading ||
    tagStore.isLoading ||
    teamStore.isLoading ||
    userStore.isLoading ||
    walkStore.isLoading ||
    wayPointStore.isLoading
)

const isAuthenticated = computed(() => authStore.isAuthenticated)
const isSuperAdmin = computed(() => authStore.isSuperAdmin)
const isAdmin = computed(() => authStore.isAdmin || isSuperAdmin.value)
const isUserSwitched = computed(() => authStore.isUserSwitched)
const currentUser = computed(() => authStore.currentUser)
const hasNewChangelogItems = computed(() => changelogStore.hasNewChangelogItems)

const displayedUserList = computed(() => {
    if (!users.value || !users.value.length) return []

    const searchString = generalStore.navUserFilter.toLowerCase()

    return users.value
        .filter((user) => {
            if (user.username.toLowerCase().includes(searchString)) return true

            for (const team of user.teams) {
                if (team.name.toLowerCase().includes(searchString)) return true
            }

            for (const role of user.roles) {
                if (role.toLowerCase().includes(searchString)) return true
            }

            const client = getClientByIri(user.client)
            return client && client.name.toLowerCase().includes(searchString)
        })
        .sort((a, b) => a.username.toLowerCase().localeCompare(b.username.toLowerCase()))
})

function getClientByIri(clientIri: Client['@id']) {
    return clientStore.getClientByIri(clientIri)
}

async function showUserMenu() {
    if (isSuperAdmin.value && users.value.length <= 1) {
        const fetchedUsers = await userStore.fetchUsers()
        users.value = fetchedUsers.filter((user: any) => user.isEnabled)
        await clientStore.fetchClients()
    }
}

function switchUser(user: any) {
    userMenu.value = false
    authStore.switchUser(user)
}

function exitSwitchUser() {
    userMenu.value = false
    authStore.exitSwitchUser()
}

function hasUserRoleAdmin(user: User) {
    return user.roles.includes('ROLE_ADMIN')
}

function getAdditionalUserInfo(user: User) {
    let additionalUserInfo = ''

    const teams = Object.values(user.teams)
        .map((team: any) => team.name)
        .sort((a: string, b: string) => a.toLowerCase().localeCompare(b.toLowerCase()))
        .join(', ')

    if (teams) {
        additionalUserInfo += teams.trim() + ' - '
    }

    const clientName = getClientByIri(user.client)?.name
    additionalUserInfo += ` ${clientName}`

    return additionalUserInfo.trim()
}
</script>


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
                v-model="userMenu"
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
                        @click="userMenu = false"
                    >
                        <v-list-item-title>Login</v-list-item-title>
                    </v-list-item>
                    <v-list-item
                        v-if="!isAuthenticated"
                        :to="{ name: 'PasswordReset' }"
                        exact
                        link
                        @click="userMenu = false"
                    >
                        <v-list-item-title>Passwort vergessen?</v-list-item-title>
                    </v-list-item>
                    <v-list-item
                        v-if="isAuthenticated"
                        :to="{ name: 'PasswordChangeRequest' }"
                        exact
                        link
                        @click="userMenu = false"
                    >
                        <v-list-item-title>Passwort ändern</v-list-item-title>
                    </v-list-item>
                    <v-list-item
                        v-if="isAuthenticated"
                        exact
                        link
                        @click=""
                    >
                        <v-list-item-title @click="showUserPreferencesForm = true">
                            Einstellungen
                            <v-dialog v-model="showUserPreferencesForm" fullscreen transition="dialog-bottom-transition">
                                <v-card>
                                    <v-toolbar flat>
                                        <v-btn icon @click="showUserPreferencesForm = false"><v-icon>mdi-close</v-icon></v-btn>
                                        <v-toolbar-title>Einstellungen</v-toolbar-title>
                                        <v-spacer />
                                    </v-toolbar>

                                    <v-card-text class="pa-6">
                                        <UserPreferencesForm />
                                    </v-card-text>
                                </v-card>
                            </v-dialog>
                        </v-list-item-title>
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
                        @click="userMenu = false"
                    >
                        <v-list-item-title>Abmelden</v-list-item-title>
                    </v-list-item>
                    <v-divider />
                    <v-list-item
                        :to="{ name: 'About' }"
                        exact
                        link
                        @click="userMenu = false"
                    >
                        <v-list-item-title>Was ist Swapp?</v-list-item-title>
                    </v-list-item>
                    <v-list-item
                        :to="{ name: 'Changelog' }"
                        exact
                        link
                        @click="userMenu = false"
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
                        @click="userMenu = false"
                    >
                        <v-list-item-title>FAQ</v-list-item-title>
                    </v-list-item>
                    <v-divider />
                    <v-list-item
                        href="https://streetworkapp.de"
                        target="_blank"
                        @click="userMenu = false"
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

<style scoped>
</style>
