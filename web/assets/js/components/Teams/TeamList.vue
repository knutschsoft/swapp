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
            stacked="md"
            class="mb-0"
        >
            <template #cell(users)="data">
                <b-spinner
                    v-if="isUserLoading"
                    small
                    type="grow"
                />
                <span v-else>{{ data.value }}</span>
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
            size="xl"
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

    export default {
        name: "TeamList",
        components: { TeamForm },
        data: function () {
            return {
                fields: [
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
                            let usernames = [];
                            userIris.forEach(userIri => {
                                usernames.push(this.getUserByIri(userIri).username)
                            })

                            if (usernames.length) {
                                return usernames.join(', ')
                            }

                            return '-';
                        },
                        sortByFormatted: true,
                        class: 'text-center',
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
                            if (item.isWithAgeRanges) {
                                additionalWayPointFields.push('Altersgruppen');
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
                        class: 'text-center',
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
                ],
                editTeam: null,
            }
        },
        computed: {
            isDisabled() {
                return this.$store.getters["team/changeTeamIsLoading"];
            },
            teams() {
                return this.$store.getters['team/teams'];
            },
            users() {
                return this.$store.getters['user/users']
                    .slice(0)
                    .sort((a, b) => {
                        return (a.username.toLowerCase() > b.username.toLowerCase()) ? 1 : -1;
                    });
            },
            isLoading() {
                return this.$store.getters["team/isLoading"];
            },
            isUserLoading() {
                return this.$store.getters["user/isLoading"];
            },
            error() {
                return this.$store.getters["team/error"];
            },
            isSuperAdmin() {
                return this.$store.getters['security/isSuperAdmin'];
            },
        },
        async created() {
            await Promise.all([
                this.$store.dispatch('team/findAll'),
                this.$store.dispatch('user/findAll'),
                this.$store.dispatch('client/findAll'),
            ]);
        },
        methods: {
            clientFormatter(clientIri) {
                return this.$store.getters['client/getClientByIri'](clientIri).name;
            },
            getUserByIri(userIri) {
                return this.$store.getters['user/getUserByIri'](userIri);
            },
            openEditModal: function (team) {
                this.editTeam = team;
                this.$root.$emit('bv::show::modal', 'edit-modal-team');
            },
            async handleSubmit(team) {
                const changedTeam = await this.$store.dispatch('team/change', {
                    team: team['@id'],
                    name: team.name,
                    locationNames: team.locationNames,
                    users: team.users,
                    ageRanges: team.ageRanges,
                    userGroupNames: team.userGroupNames,
                    isWithAgeRanges: team.isWithAgeRanges,
                    isWithContactsCount: team.isWithContactsCount,
                    isWithUserGroups: team.isWithUserGroups,
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
