<template>
    <div>
        <v-data-table-server
            :items="teams"
            :headers="headers"
            :items-per-page="itemsPerPage"
            :items-per-page-options="itemsPerPageOptions"
            :items-per-page-text="itemsPerPageText"
            :loading="isLoading"
            :no-data-text="noItemsText"
            :loading-text="loadingText"
            :no-results-text="noItemsText"
            multi-sort
            mobile-breakpoint="md"
            density="compact"
            items-length=""
            hide-default-footer
            class="mb-0"
        >
            <template v-slot:item.users="{item}">
                <v-progress-circular
                    v-if="isUserLoading"
                    spin
                    indeterminate
                    size="18"
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
                <tooltip :text="item.walkNames.join(', ') || '-'" />
            </template>
            <template v-slot:item.conceptOfDaySuggestions="{item}">
                <tooltip :text="item.conceptOfDaySuggestions.join(', ') || '-'" />
            </template>
            <template v-slot:item.locationNames="{item}">
                <tooltip :text="item.locationNames.join(', ') || '-'" />
            </template>
            <template v-slot:item.additionalWayPointFields="{item}">
                <tooltip :text="getAdditionalWayPointFieldsByTeam(item).join(', ') || '-'" />
            </template>
            <template v-slot:item.ageRanges="{item}">
                <tooltip :text="getFormattedAgeRangesByTeam(item).join(', ') || '-'" />
            </template>
            <template v-slot:item.additionalWalkFields="{item}">
                <tooltip :text="getAdditionalWalkFieldsByTeam(item).join(', ') || '-'" />
            </template>
            <template v-slot:item.guestNames="{item}">
                <tooltip :text="item.guestNames.join(', ') || '-'" />
            </template>
            <template v-slot:item.client="{item}">
                {{ clientFormatter(item.client) }}
            </template>
            <template v-slot:item.actions="{item}">
                <v-btn
                    size="small"
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
        </v-data-table-server>

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
                        @submitted="handleSubmit"
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
} from '@/js/utils'
import Tooltip from "@/js/components/Common/Tooltip.vue";

export default {
    name: "TeamList",
    components: {Tooltip, TeamForm, UserItem},
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
        headers() {
            let headers = [
                {
                    key: 'name',
                    title: 'Name',
                    sortable: true,
                    align: 'center',
                },
                {
                    key: 'users',
                    title: 'Benutzer',
                    sortable: false,
                    align: 'center',
                }
            ];

            if (this.hasAtLeastOneTeamWithWalkNameSuggestions) {
                headers.push({
                    key: 'walkNames',
                    title: 'Namen für Runden',
                    sortable: false,
                    align: 'center',
                });
            }
            if (this.hasAtLeastOneTeamWithConceptOfDaySuggestions) {
                headers.push({
                    key: 'conceptOfDaySuggestions',
                    title: 'Tageskonzept für Runden',
                    sortable: false,
                    align: 'center',
                });
            }
            headers.push({
                key: 'locationNames',
                title: 'Orte für Wegpunkte',
                sortable: false,
                align: 'center',
            });
            if (this.hasAtLeastOneAdditionalWayPointField) {
                headers.push({
                    key: 'additionalWayPointFields',
                    title: 'zusätzliche Wegpunkt-Felder',
                    sortable: false,
                    align: 'center',
                });
            }
            if (this.hasAtLeastOneTeamAgeRanges) {
                headers.push({
                    key: 'ageRanges',
                    title: 'Altersgruppen',
                    sortable: false,
                    align: 'center',
                });
            }
            if (this.hasAtLeastOneAdditionalWalkField) {
                headers.push({
                    key: 'additionalWalkFields',
                    title: 'zusätzliche Runden-Felder',
                    sortable: false,
                    align: 'center',
                });
            }
            if (this.hasAtLeastOneTeamGuestNames) {
                headers.push({
                    key: 'guestNames',
                    title: 'mögliche weitere Teilnehmende',
                    sortable: false,
                    align: 'center',
                });
            }
            if (this.isSuperAdmin) {
                headers.push({
                    key: 'client',
                    title: 'Klient',
                    sortable: false,
                    align: 'center',
                });
                // headers.push({
                //     key: 'createdAt',
                //     title: 'Erstellt am',
                // });
                // headers.push({
                //     key: 'updatedAt',
                //     title: 'Geändert am',
                // });
            }
            headers.push({key: 'actions', title: 'Aktionen', align: 'center'});
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
            return this.teams.every(team => team.isWithPeopleCount || team.isWithAgeRanges || team.isWithContactsCount || team.isWithUserGroups || team.isWithConsumables || team.isWithCounselings || team.isWithMedicals);
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
            if (team.isWithConsumables) {
                additionalWayPointFields.push('Ausgabematerialien');
            }
            if (team.isWithCounselings) {
                additionalWayPointFields.push('Beratungen');
            }
            if (team.isWithMedicals) {
                additionalWayPointFields.push('Medizin');
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
                consumableNames: team.consumableNames,
                counselingNames: team.counselingNames,
                medicalNames: team.medicalNames,
                isWithAgeRanges: team.isWithAgeRanges,
                isWithPeopleCount: team.isWithPeopleCount,
                isWithContactsCount: team.isWithContactsCount,
                isWithUserGroups: team.isWithUserGroups,
                isWithConsumables: team.isWithConsumables,
                isWithCounselings: team.isWithCounselings,
                isWithMedicals: team.isWithMedicals,
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
