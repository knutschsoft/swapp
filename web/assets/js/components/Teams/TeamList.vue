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
            class="mb-0"
        >
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
            size="lg"
            cancel-title="Abbrechen"
            ok-title="Team speichern"
            :cancel-disabled="isDisabled"
            :ok-disabled="isDisabled"
            @ok="saveTeam"
        >
            <b-form-group
                label="Teamname"
                v-slot="{ ariaDescribedby }"
            >
                <b-input
                    v-model="editModalTeam.name"
                    :aria-describedby="ariaDescribedby"
                    :disabled="isDisabled"
                />
            </b-form-group>

            <b-form-group
                label="Benutzer"
                v-slot="{ ariaDescribedby }"
            >
                <b-form-checkbox-group
                    v-model="editModalTeam.users"
                    class="check-boxes d-flex flex-row flex-wrap justify-content-start"
                    switch
                    data-test="users"
                    button-variant="secondary rounded-0 mt-1 mr-1 px-4"
                    :options="users"
                    :aria-describedby="ariaDescribedby"
                    name="users"
                    :disabled="isDisabled"
                    value-field="@id"
                    text-field="username"
                >
                </b-form-checkbox-group>
            </b-form-group>
        </b-modal>
    </div>
</template>

<script>
    "use strict";
    export default {
        name: "TeamList",
        data: function () {
            return {
                fields: [
                    {
                        key: 'id',
                        label: 'ID',
                        class: 'text-center',
                    },
                    {
                        key: 'name',
                        sortable: true,
                        class: 'text-center',
                    },
                    {
                        key: 'users',
                        sortable: true,
                        formatter: (value, key, item) => {
                            let usernames = [];
                            value.forEach(user => {
                                usernames.push(user.username)
                            })

                            if (usernames.length) {
                                return usernames.join(', ')
                            }

                            return '-'
                        },
                        sortByFormatted: true,
                        class: 'text-center',
                    },
                    {
                        key: 'ageRanges',
                        label: 'Altersgruppen',
                        formatter: (value, key, item) => {
                            let ageRanges = [];
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
                    {key: 'actions', label: 'Aktionen', class: 'text-center',}
                ],
                editModalTeam: {
                    team: '',
                    name: '',
                    users: [],
                    ageRanges: [],
                },
                editTeam: null,
            }
        },
        computed: {
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
            error() {
                return this.$store.getters["team/error"];
            },
            isDisabled() {
                return this.$store.getters["teram/changeTeamIsLoading"];
            },
        },
        async created() {
            await Promise.all([
                this.$store.dispatch('team/findAll'),
                this.$store.dispatch('user/findAll'),
            ]);
        },
        methods: {
            openEditModal(team) {
                this.editTeam = team;
                console.log(team);
                this.editModalTeam.team = team['@id'];
                this.editModalTeam.ageRanges = team.ageRanges;
                this.editModalTeam.users = team.users.map(user => user['@id']);
                this.editModalTeam.name = team.name;
                this.$root.$emit('bv::show::modal', 'edit-modal-team');
            },
            async saveTeam() {
                await this.$store.dispatch('team/change', this.editModalTeam);
            },
        }
    }
</script>

<style scoped lang="scss">
</style>
