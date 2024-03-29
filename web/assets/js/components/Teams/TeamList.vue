<template>
    <div>
        <div
            v-if="isLoading"
            class="d-flex justify-content-center my-3"
        >
            <b-spinner
                v-show="isLoading"
                style="width: 3rem; height: 3rem;"
                label="is Loading Spinner"
            />
        </div>
        <b-table
            v-show="!isLoading && teams.length"
            :items="teams"
            :fields="fields"
            small
            striped
            :stacked="isSuperAdmin ? 'xl' : 'lg'"
            class="mb-0"
        >
            <template #cell(users)="data">
                <b-spinner
                    v-if="isUserLoading"
                    small
                    type="grow"
                />
                <div
                    v-else-if="0 === data.value.length"
                >
                    -
                </div>
                <div
                    v-else
                >
                    <template
                        v-for="(user,key) in data.value"
                    >
                        <span
                            :class="{'text-muted': !user.isEnabled}"
                            class="d-inline-flex align-items-center"
                        >
                          <span>
                            {{ user.username }}
                          </span>
                          <mdicon
                            v-if="!user.isEnabled"
                            name="AccountOff"
                            class="text-muted d-inline-flex align-items-center"
                            title="Account ist aktuell nicht aktiviert."
                            size="16"
                          /><span class="d-inline-block mr-1">
                            {{ key < data.value.length - 1 ? ', ' : '' }}
                          </span>
                        </span>
                    </template>
                </div>
            </template>

            <template v-slot:cell(actions)="row">
                <b-button
                    size="sm"
                    @click="openEditModal(row.item)"
                >
                    Team bearbeiten
                    <b-icon-pencil />
                </b-button>
            </template>
        </b-table>

        <b-modal
            id="edit-modal-team"
            :title="`Team &quot;${editTeam ? editTeam.name : ''}&quot; bearbeiten`"
            size="xxl"
            hide-footer
        >
            <team-form
                refs="teamForm"
                :initial-team="editTeam"
                @submit="handleSubmit"
                button-label="Team speichern"
            />
        </b-modal>
    </div>
</template>

<script>
    "use strict";
    import TeamForm from './TeamForm.vue';
    import { useClientStore } from '../../stores/client';
    import { useTeamStore } from '../../stores/team';
    import { useUserStore } from '../../stores/user';
    import { useAuthStore } from '../../stores/auth';

    export default {
        name: "TeamList",
        components: { TeamForm },
        data: function () {
            return {
                authStore: useAuthStore(),
                clientStore: useClientStore(),
                teamStore: useTeamStore(),
                userStore: useUserStore(),
                editTeam: null,
            };
        },
        computed: {
            fields() {
                return [
                    {
                        key: 'name',
                        sortable: true,
                        class: 'text-center',
                    },
                    {
                        key: 'users',
                        label: 'Benutzer',
                        sortable: true,
                        formatter: (userIris, key, item) => {
                            return userIris
                                .map(userIri => this.getUserByIri(userIri))
                                .sort((userA, userB) => {
                                  if (userA.isEnabled === userB.isEnabled) {
                                    return userA.username.toLowerCase() > userB.username.toLowerCase() ? 1 : -1;
                                  }

                                  return userA.isEnabled && !userB.isEnabled ? -1 : 1;
                                });
                        },
                        sortByFormatted: true,
                        class: 'text-center',
                    },
                    {
                        key: 'walkNames',
                        label: 'Namen für Runden',
                        sortable: true,
                        formatter: (walkNames) => {
                            if (walkNames.length) {
                                return walkNames.join(', ');
                            }

                            return '-';
                        },
                        sortByFormatted: true,
                        class: this.hasAtLeastOneTeamWithWalkNameSuggestions ? 'text-center' : 'd-none',
                    },
                    {
                        key: 'conceptOfDaySuggestions',
                        label: 'Tageskonzept für Runden',
                        sortable: true,
                        formatter: (conceptOfDaySuggestion) => {
                            if (conceptOfDaySuggestion.length) {
                                return conceptOfDaySuggestion.join(', ');
                            }

                            return '-';
                        },
                        sortByFormatted: true,
                        class: this.hasAtLeastOneTeamWithConceptOfDaySuggestions ? 'text-center' : 'd-none',
                    },
                    {
                        key: 'locationNames',
                        label: 'Orte für Wegpunkte',
                        sortable: true,
                        formatter: (locationNames) => {
                            if (locationNames.length) {
                                return locationNames.join(', ');
                            }

                            return '-';
                        },
                        sortByFormatted: true,
                        class: 'text-center',
                    },
                    {
                        key: 'additionalWayPointFields',
                        label: 'zusätzliche Wegpunkt-Felder',
                        sortable: false,
                        sortByFormatted: false,
                        class: 'text-center',
                        formatter: (value, key, item) => {
                            let additionalWayPointFields = [];
                            if (item.isWithPeopleCount && item.isWithAgeRanges) {
                                additionalWayPointFields.push('Altersgruppen & Anzahl Personen vor Ort');
                            } else {
                                if (item.isWithPeopleCount) {
                                    additionalWayPointFields.push('Anzahl Personen vor Ort');
                                }
                                if (item.isWithAgeRanges) {
                                    additionalWayPointFields.push('Altersgruppen');
                                }
                            }
                            if (item.isWithContactsCount) {
                                additionalWayPointFields.push('Anzahl direkter Kontakte');
                            }
                            if (item.isWithUserGroups) {
                                additionalWayPointFields.push('Personenanzahl von Nutzergruppen');
                            }

                            if (additionalWayPointFields.length) {
                                return additionalWayPointFields.join(', ');
                            }

                            return '-';
                        },
                    },
                    {
                        key: 'ageRanges',
                        label: 'Altersgruppen',
                        formatter: (value, key, item) => {
                            let ageRanges = [];
                            if (!item.isWithAgeRanges) {
                                return '-';
                            }
                            value.forEach(ageRange => {
                                ageRanges.push(ageRange.rangeStart + '-' + ageRange.rangeEnd)
                            })

                            if (ageRanges.length) {
                                return ageRanges.join(', ')
                            }

                            return '-'
                        },
                        sortByFormatted: true,
                        sortable: true,
                        class: this.hasAtLeastOneTeamAgeRanges ? 'text-center' : 'd-none',
                    },
                    {
                        key: 'additionalWalkFields',
                        label: 'zusätzliche Runden-Felder',
                        sortable: false,
                        sortByFormatted: false,
                        class: 'text-center',
                        formatter: (value, key, item) => {
                            let additionalWayPointFields = [];
                            if (item.isWithGuests) {
                                additionalWayPointFields.push('Weitere Teilnehmende');
                            }
                            if (item.isWithSystemicQuestion) {
                                additionalWayPointFields.push('Systemische Frage');
                            }

                            if (additionalWayPointFields.length) {
                                return additionalWayPointFields.join(', ');
                            }

                            return '-';
                        },
                    },
                    {
                        key: 'guestNames',
                        label: 'mögliche weitere Teilnehmende',
                        formatter: (value, key, item) => {
                            let ageRanges = [];
                            if (!item.isWithGuests) {
                                return '-';
                            }
                            value.forEach(ageRange => {
                                ageRanges.push(ageRange)
                            })

                            if (ageRanges.length) {
                                return ageRanges.join(', ')
                            }

                            return '-'
                        },
                        sortByFormatted: true,
                        sortable: true,
                        class: this.hasAtLeastOneTeamGuestNames ? 'text-center' : 'd-none',
                    },
                    {
                        key: 'client',
                        label: 'Klient',
                        sortable: true,
                        sortByFormatted: true,
                        class: !this.isSuperAdmin ? 'd-none' : '',
                        formatter: this.clientFormatter,
                    },
                    {key: 'actions', label: 'Aktionen', class: 'text-center',}
                ];
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
            openEditModal: function (team) {
                this.editTeam = team;
                this.$root.$emit('bv::show::modal', 'edit-modal-team');
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
                    guestNames: team.guestNames,
                });

                if (changedTeam) {
                    const message = `Das Team ${changedTeam.name} wurde erfolgreich geändert.`;
                    this.$bvToast.toast(message, {
                        title: 'Team geändert',
                        toaster: 'b-toaster-top-right',
                        variant: 'success',
                        autoHideDelay: 10000,
                        appendToast: true,
                    });
                    this.$root.$emit('bv::hide::modal', 'edit-modal-team');
                } else {
                    this.$bvToast.toast('Upps! :-(', {
                        title: 'Team ändern fehlgeschlagen',
                        toaster: 'b-toaster-top-right',
                        autoHideDelay: 10000,
                        variant: 'danger',
                        appendToast: true,
                    });
                }
            },
        }
    }
</script>

<style scoped lang="scss">
</style>
