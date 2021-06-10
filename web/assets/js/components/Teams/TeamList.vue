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
                    trim
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

            <b-form-group
                label="Altersgruppen"
                v-slot="{ ariaDescribedby }"
            >
                <b-row
                    v-for="(ageRange, i) in editModalTeam.ageRanges"
                    :key="i"
                >
                    <b-col cols="12">
                        {{ ageRange.rangeStart }} - {{ ageRange.rangeEnd }} Jahre
                    </b-col>
                    <b-col cols="4">
                        <b-input
                            v-model="editModalTeam.ageRanges[i].rangeStart"
                            :aria-describedby="ariaDescribedby"
                            :disabled="isDisabled"
                            type="number"
                            min="0"
                            max="120"
                            trim
                            number
                            step="1"
                            required
                            placeholder="von"
                        />
                    </b-col>
                    <b-col cols="4">
                        <b-input
                            v-model="editModalTeam.ageRanges[i].rangeEnd"
                            :aria-describedby="ariaDescribedby"
                            :disabled="isDisabled"
                            type="number"
                            min="0"
                            max="120"
                            trim
                            number
                            step="1"
                            required
                            placeholder="bis"
                        />
                    </b-col>
                    <b-col cols="3">
                        <div
                            class="cursor-pointer mt-1"
                            @click="removeAgeRange(i)"
                        >
                            <mdicon
                                name="DeleteCircleOutline"
                            />
                        </div>
                    </b-col>
                </b-row>
                <b-row>
                    <b-col cols="12">
                        <div
                            class="cursor-pointer mt-1"
                            @click="addAgeRange()"
                        >
                            <mdicon
                                name="PlusCircleOutline"
                            />
                        </div>
                    </b-col>
                </b-row>
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
            isSuperAdmin() {
                return this.$store.getters['security/isSuperAdmin'];
            },
        },
        async created() {
            await Promise.all([
                this.$store.dispatch('team/findAll'),
                this.$store.dispatch('user/findAll'),
            ]);
        },
        methods: {
            clientFormatter(value) {
                return this.getClientByIri(value).name;
            },
            getClientByIri(iri) {
                const id = iri.replace('/api/clients/', '');

                return this.$store.getters['client/getClientById'](id);
            },
            openEditModal: function (team) {
                this.editTeam = team;
                this.editModalTeam.team = team['@id'];
                this.editModalTeam.ageRanges = team.ageRanges
                    .map(ageRange => {
                        return {
                            'rangeStart': ageRange.rangeStart,
                            'rangeEnd': ageRange.rangeEnd,
                        };
                    })
                    .sort((a, b) => {
                        return (a.rangeStart > b.rangeStart) ? 1 : -1;
                    })
                ;
                this.editModalTeam.users = team.users.map(user => user['@id']);
                this.editModalTeam.name = team.name;
                this.$root.$emit('bv::show::modal', 'edit-modal-team');
            },
            async saveTeam() {
                this.editModalTeam.ageRanges.sort((a, b) => {
                    return (a.rangeStart > b.rangeStart) ? 1 : -1;
                });
                await this.$store.dispatch('team/change', this.editModalTeam);
            },
            removeAgeRange(index) {
                this.editModalTeam.ageRanges.splice(index, 1);
            },
            addAgeRange(index) {
                this.editModalTeam.ageRanges = [ ...this.editModalTeam.ageRanges, { rangeStart: '', rangeEnd: '' } ];
            },
        }
    }
</script>

<style scoped lang="scss">
</style>
