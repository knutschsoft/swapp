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
                    @click="editTeam(row.item)"
                >
                    Team bearbeiten
                    <b-icon-pencil />
                </b-button>
            </template>
        </b-table>

        <b-modal
            :id="editModalTeam.id"
            :title="editModalTeam.title"
            size="lg"
            @hide="resetEditModalTeam"
        >
            Teamname
            <b-input v-model="editModalTeam.name" />
            Benutzer
            <b-select v-model="editModalTeam.users" />
            Altersgruppen
            <b-select v-model="editModalTeam.ageGroups" />
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
                    id: 'edit-modal-team',
                    title: '',
                    ageGroups: [],
                    name: '',
                    users: [],
                },
            }
        },
        computed: {
            teams() {
                return this.$store.getters['team/teams'];
            },
            isLoading() {
                return this.$store.getters["team/isLoading"];
            },
            error() {
                return this.$store.getters["team/error"];
            },
        },
        created() {
            this.$store.dispatch('team/findAll');
        },
        methods: {
            editTeam(team) {
                // this.editModalTeam.id = String(team.id);
                this.editModalTeam.title = `${team.name} bearbeiten`;
                this.editModalTeam.ageGroups = team.ageGroups;
                this.editModalTeam.users = team.users;
                this.editModalTeam.name = team.name;
                this.$root.$emit('bv::show::modal', this.editModalTeam.id);
            },
            resetEditModalTeam() {
                this.editModalTeam.title = ''
            },
        }
    }
</script>

<style scoped lang="scss">
</style>
