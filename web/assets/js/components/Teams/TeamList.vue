<template>
    <div>
        <v-data-table
            :items="teams"
            :headers="fields"
            :items-per-page="itemsPerPage"
            :items-per-page-options="itemsPerPageOptions"
            :items-per-page-text="itemsPerPageText"
            :loading="isLoading"
            :no-data-text="noItemsText"
            :loading-text="loadingText"
            :no-results-text="noItemsText"
            multi-sort
            density="compact"
            class="mb-0"
        >
            <template v-slot:item.users="{item}">
                <v-progress-circular
                    v-if="isUserLoading"
                    rotate
                    indeterminate
                />
                <div
                    v-else-if="0 === item.users.length"
                >
                    -
                </div>
                <div
                    v-else
                >
                    <user-item
                        v-for="(user,key) in getSortedUsers(item.users)"
                        :user="user"
                        :append-comma="key < item.users.length - 1"
                        :key="key"
                    />
                </div>
            </template>
            <template v-slot:item.walkNames="{item}">
                {{item.walkNames.join(', ') || '-'}}
            </template>
            <template v-slot:item.conceptOfDaySuggestions="{item}">
                {{item.conceptOfDaySuggestions.join(', ') || '-'}}
            </template>
            <template v-slot:item.locationNames="{item}">
                {{item.locationNames.join(', ') || '-'}}
            </template>
            <template v-slot:item.additionalWayPointFields="{item}">
                {{ getAdditionalWayPointFieldsByTeam(item).join(', ') || '-' }}
            </template>
            <template v-slot:item.ageRanges="{item}">
                {{ getFormattedAgeRangesByTeam(item).join(', ') || '-' }}
            </template>
            <template v-slot:item.additionalWalkFields="{item}">
                {{ getAdditionalWalkFieldsByTeam(item) || '-' }}
            </template>
            <template v-slot:item.guestNames="{item}">
                {{ item.guestNames.join(', ') || '-' }}
            </template>
            <template v-slot:item.client="{item}">
                {{ clientFormatter(item.client) }}
            </template>
            <template v-slot:item.actions="{item}">
                <v-btn
                    small
                    color="secondary"
                    @click="openTeamEditDialog(item)"
                >
                    Team bearbeiten
                    <v-icon
                        small
                        class="ml-2"
                    >
                        mdi-pencil-outline
                    </v-icon>
                </v-btn>
            </template>
        </v-data-table>

        <v-dialog
            v-model="dialog"
            scrollable
        >
            <v-card>
                <v-card-title class="text-h5 grey lighten-2">
                    Team {{ editTeam ? editTeam.name : '' }} bearbeiten
                </v-card-title>
                <v-card-text>
                    <team-form
                        v-if="editTeam"
                        :initial-team="editTeam"
                        @submit="handleSubmit"
                        button-label="Team speichern"
                    />
                </v-card-text>
            </v-card>
        </v-dialog>
    </div>
</template>

<script>
"use strict";
import TeamForm from './TeamForm.vue';
import UserItem from './UserItem.vue';
import {useAlertStore, useAuthStore, useClientStore, useTeamStore, useUserStore} from '../../stores';
import {
    itemsPerPageOptions,
    itemsPerPageText,
    loadingText,
} from '../../utils'

export default {
    name: "TeamList",
    components: {TeamForm, UserItem},
    data: function () {
        return {
            alertStore: useAlertStore(),
            authStore: useAuthStore(),
            clientStore: useClientStore(),
            teamStore: useTeamStore(),
            userStore: useUserStore(),
            editTeam: null,
            dialog: false,
            itemsPerPageOptions,
            itemsPerPageText,
            noItemsText: 'Noch keine Teams erstellt. Bitte erstelle zuerst ein Team.',
            loadingText,
            itemsPerPage: -1,
        };
    },
    computed: {
        fields() {
            let headers = [
                {
                    value: 'name',
                    sortable: true,
                },
                {
                    value: 'users',
                    text: 'Benutzer',
                    sortable: false,
                }
            ];

            if (this.hasAtLeastOneTeamWithWalkNameSuggestions) {
                headers.push({
                    value: 'walkNames',
                    text: 'Namen für Runden',
                    sortable: false,
                });
            }
            if (this.hasAtLeastOneTeamWithConceptOfDaySuggestions) {
                headers.push({
                    value: 'conceptOfDaySuggestions',
                    text: 'Tageskonzept für Runden',
                    sortable: false,
                });
            }
            headers.push({
                value: 'locationNames',
                text: 'Orte für Wegpunkte',
                sortable: false,
            });
            if (this.hasAtLeastOneAdditionalWayPointField) {
                headers.push({
                    value: 'additionalWayPointFields',
                    text: 'zusätzliche Wegpunkt-Felder',
                    sortable: false,
                });
            }
            if (this.hasAtLeastOneTeamAgeRanges) {
                headers.push({
                    value: 'ageRanges',
                    text: 'Altersgruppen',
                    sortable: false,
                });
            }
            if (this.hasAtLeastOneAdditionalWalkField) {
                headers.push({
                    value: 'additionalWalkFields',
                    text: 'zusätzliche Runden-Felder',
                    sortable: false
                });
            }
            if (this.hasAtLeastOneTeamGuestNames) {
                headers.push({
                    value: 'guestNames',
                    text: 'mögliche weitere Teilnehmende',
                    sortable: false,
                });
            }
            if (this.isSuperAdmin) {
                headers.push({
                    value: 'client',
                    text: 'Klient',
                    sortable: false,
                });
                // headers.push({
                //     value: 'createdAt',
                //     text: 'Erstellt am',
                // });
                // headers.push({
                //     value: 'updatedAt',
                //     text: 'Geändert am',
                // });
            }
            headers.push({value: 'actions', text: 'Aktionen'});
            return headers;
        },
        teams() {
            return this.teamStore.getTeams;
        },
        users() {
            return this.userStore.getUsers
                .slice(0)
                .sort((a, b) => {
                    return (a.username.toLowerCase() > b.username.toLowerCase()) ? 1 : -1;
                });
        },
        isLoading() {
            return this.teamStore.isLoading;
        },
        isUserLoading() {
            return this.userStore.isLoading;
        },
        error() {
            return this.teamStore.getErrors;
        },
        isSuperAdmin() {
            return this.authStore.isSuperAdmin;
        },
        hasAtLeastOneTeamGuestNames() {
            return !this.teams.every(team => !team.isWithGuests);
        },
        hasAtLeastOneTeamAgeRanges() {
            return !this.teams.every(team => !team.isWithAgeRanges);
        },
        hasAtLeastOneAdditionalWayPointField() {
            return this.teams.every(team => team.isWithPeopleCount || team.isWithAgeRanges || team.isWithContactsCount || team.isWithUserGroups);
        },
        hasAtLeastOneAdditionalWalkField() {
            return this.teams.every(team => team.isWithGuests || team.isWithSystemicQuestion);
        },
        hasAtLeastOneTeamWithConceptOfDaySuggestions() {
            return !this.teams.every(team => !team.conceptOfDaySuggestions.length);
        },
        hasAtLeastOneTeamWithWalkNameSuggestions() {
            return !this.teams.every(team => !team.walkNames.length);
        },
    },
    async created() {
        await Promise.all([
            this.teamStore.fetchTeams(),
            this.userStore.fetchUsers(),
            this.clientStore.fetchClients(),
        ]);
    },
    methods: {
        clientFormatter(clientIri) {
            return this.clientStore.getClientByIri(clientIri)?.name;
        },
        getUserByIri(userIri) {
            return this.userStore.getUserByIri(userIri);
        },
        getSortedUsers(users) {
            return users
                .map(userIri => this.getUserByIri(userIri))
                .sort((userA, userB) => {
                    if (userA.isEnabled === userB.isEnabled) {
                        return userA.username.toLowerCase() > userB.username.toLowerCase() ? 1 : -1;
                    }

                    return userA.isEnabled && !userB.isEnabled ? -1 : 1;
                });
        },
        getAdditionalWayPointFieldsByTeam(team) {
            let additionalWayPointFields = [];
            if (team.isWithPeopleCount && team.isWithAgeRanges) {
                additionalWayPointFields.push('Altersgruppen & Anzahl Personen vor Ort');
            } else {
                if (team.isWithPeopleCount) {
                    additionalWayPointFields.push('Anzahl Personen vor Ort');
                }
                if (team.isWithAgeRanges) {
                    additionalWayPointFields.push('Altersgruppen');
                }
            }
            if (team.isWithContactsCount) {
                additionalWayPointFields.push('Anzahl direkter Kontakte');
            }
            if (team.isWithUserGroups) {
                additionalWayPointFields.push('Personenanzahl von Nutzergruppen');
            }

            return additionalWayPointFields;
        },
        getAdditionalWalkFieldsByTeam(team) {
            let additionalWalkFields = [];
            if (team.isWithGuests) {
                additionalWalkFields.push('Weitere Teilnehmende');
            }
            if (team.isWithSystemicQuestion) {
                additionalWalkFields.push('Systemische Frage');
            }

            return additionalWalkFields;
        },
        getFormattedAgeRangesByTeam(team) {
            let ageRanges = [];
            if (!team.isWithAgeRanges) {
                return ageRanges;
            }
            team.ageRanges.forEach(ageRange => {
                ageRanges.push(ageRange.rangeStart + '-' + ageRange.rangeEnd)
            })

            return ageRanges;
        },
        openTeamEditDialog: function (team) {
            this.editTeam = team;
            this.dialog = true;
        },
        async handleSubmit(team) {
            const changedTeam = await this.teamStore.change({
                team: team['@id'],
                name: team.name,
                locationNames: team.locationNames,
                walkNames: team.walkNames,
                conceptOfDaySuggestions: team.conceptOfDaySuggestions,
                users: team.users,
                ageRanges: team.ageRanges,
                userGroupNames: team.userGroupNames,
                isWithAgeRanges: team.isWithAgeRanges,
                isWithPeopleCount: team.isWithPeopleCount,
                isWithContactsCount: team.isWithContactsCount,
                isWithUserGroups: team.isWithUserGroups,
                isWithGuests: team.isWithGuests,
                isWithSystemicQuestion: team.isWithSystemicQuestion,
                initialMembersConfig: team.initialMembersConfig,
                guestNames: team.guestNames,
            });

            if (changedTeam) {
                this.alertStore.success(`Das Team ${changedTeam.name} wurde erfolgreich geändert.`, 'Team geändert');
                this.dialog = false;
            } else {
                this.alertStore.error('Team ändern fehlgeschlagen', 'Upps! :-(');
            }
        },
    }
}
</script>

<style scoped lang="scss">
</style>
