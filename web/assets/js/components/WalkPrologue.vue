<template>
    <content-collapse
        :title="`Neue Streetwork-Runde`"
        collapse-key="walk-prologue"
        is-visible-by-default
    >
        <v-form
            v-if="team"
            ref="form"
            lazy-validation
            @submit.prevent="onSubmit"
        >
            <v-col class="my-2">
                <walk-walk-creator-field
                    v-model="form.walkCreator"
                    :team="team"
                    :is-loading="isLoading"
                    :error="error"
                    :show-disabled="false"
                    @change="handleWalkCreatorChange"
                />
            </v-col>
            <v-col>
                <walk-team-members-field
                    v-model="form.walkTeamMembers"
                    :users="usersOfTeam"
                    :walk-creator="form.walkCreator"
                    :is-loading="isLoading"
                    :error="error"
                    :label="`Teilnehmende des Teams &quot;${team?.name}&quot;`"
                    description="Wer ist heute mit dabei?"
                />
            </v-col>
            <v-col v-if="team.isWithGuests">
                <walk-guest-names-field
                    v-model="form.guestNames"
                    :team="team"
                    :is-loading="isLoading"
                    :error="error"
                />
            </v-col>
            <v-col>
                <walk-name-field
                    v-model="form.name"
                    :is-loading="isLoading"
                    :team="team"
                    :error="error"
                />
            </v-col>
            <v-col>
                <walk-concept-of-day-field
                    v-model="form.conceptOfDay"
                    :team="team"
                    :is-loading="isLoading"
                    :error="error"
                />
            </v-col>
            <v-col>
                <walk-start-time-field
                    v-model="form.startTime"
                    :is-loading="isLoading"
                    :error="error"
                    description="Die aktuelle Zeit ist vorausgewählt."
                />
            </v-col>
            <v-col>
                <walk-holidays-field
                    v-model="form.holidays"
                    :is-loading="isLoading"
                    :error="error"
                />
            </v-col>
            <v-col>
                <walk-weather-field
                    v-if="team.isWithWeather"
                    v-model="form.weather"
                    :is-loading="isLoading"
                    :error="error"
                />
            </v-col>
            <v-col class="mb-2">
                <v-btn
                    type="submit"
                    :disabled="isFormInvalid"
                    block
                    color="secondary"
                    data-test="btn-Runde beginnen"
                >
                    Runde beginnen
                </v-btn>
            </v-col>
        </v-form>
    </content-collapse>
</template>

<script>
"use strict";
import ContentCollapse from './ContentCollapse.vue';
import WalkAPI from '../api/walk.js';
import dayjs from 'dayjs';
import {useAlertStore, useAuthStore, useTeamStore, useUserStore, useWalkStore} from '../stores';
import {
    WalkConceptOfDayField,
    WalkGuestNamesField,
    WalkHolidaysField,
    WalkNameField,
    WalkStartTimeField,
    WalkTeamMembersField,
    WalkWalkCreatorField,
    WalkWeatherField
} from "./Common/Walk";

export default {
    name: "WalkPrologue",
    components: {
        WalkStartTimeField,
        WalkHolidaysField,
        WalkConceptOfDayField,
        WalkGuestNamesField,
        WalkWalkCreatorField,
        WalkNameField,
        WalkWeatherField,
        WalkTeamMembersField,
        ContentCollapse,
    },
    props: {
        teamId: {
            required: true,
        }
    },
    data: function () {
        return {
            alertStore: useAlertStore(),
            authStore: useAuthStore(),
            teamStore: useTeamStore(),
            userStore: useUserStore(),
            walkStore: useWalkStore(),
            walkNameSearch: '',
            form: {
                name: '',
                team: null,
                walkTeamMembers: [],
                guestNames: [],
                conceptOfDay: [],
                startTime: dayjs().startOf('minute').format(),
                holidays: false,
                weather: '',
                walkCreator: '',
            },
            walkId: false,
            isFormLoading: false,
        }
    },
    computed: {
        usersOfTeam() {
            if (!this.team) {
                return [];
            }

            let users = [];
            this.team.users.forEach(userIri => {
                const user = this.getUserByIri(userIri);
                if (user) {
                    users.push(user);
                }
            });
            users.sort((userA, userB) => (userA.username.toLowerCase() > userB.username.toLowerCase()) ? 1 : -1);

            return users;
        },
        isFormInvalid() {
            return !this.form.name
                || !this.form.conceptOfDay
                || !this.form.startTime
                || !this.form.walkTeamMembers.length
                || !this.form.walkCreator
                || !this.form.weather && this.team.isWithWeather
                || this.isLoading;
        },
        currentUser() {
            return this.authStore.currentUser;
        },
        isLoading() {
            return this.teamStore.isLoading || this.userStore.isLoading || this.walkStore.isLoadingCreate;
        },
        team() {
            return this.teamStore.getTeamById(this.teamId);
        },
        error() {
            return this.walkStore.getErrors.create;
        },
        hasError() {
            return !!this.error;
        },
        validationErrors() {
            const errors = {};
            if (!this.hasError) {
                return errors;
            }
            const error = this.error;
            if (error && error.data.violations) {
                error.data.violations.forEach((violation) => {
                    const key = violation.propertyPath ? violation.propertyPath : 'global';
                    errors[key] = violation.message;
                });
                return errors;
            }
            if (error.data && error.data['description']) {
                errors.global = error.data['description'];
            }

            return errors;
        },
    },
    async mounted() {
        if (!this.team) {
            await this.teamStore.fetchTeams();
        }
        if (!this.team) {
            this.$router.push({name: 'Dashboard', query: {redirect: 'Dieses Team existiert nicht. Du wurdest auf das Dashboard weitergeleitet.'}});
            return;
        }
        if (!this.team.users.includes(this.currentUser['@id'])) {
            this.$router.push({
                name: 'Dashboard',
                query: {redirect: 'Du kannst für dieses Team keine Runde erstellen, da du kein Mitglied des Teams bist. Du wurdest auf das Dashboard weitergeleitet.'}
            });
            return;
        }
        this.team.users.forEach((userIri) => {
            if (!this.getUserByIri(userIri)) {
                this.userStore.fetchByIri(userIri);
            }
        });
        if (this.team.initialMembersConfig === 'mitglieder') {
            this.form.walkTeamMembers = await this.getWalkTeamMembersOfLastWalkOfTeam(this.team);
        } else {
            this.form.walkTeamMembers = [this.currentUser['@id']];
        }
        this.form.team = this.team['@id'];
        this.form.walkCreator = this.currentUser['@id'];
    },
    methods: {
        handleWalkCreatorChange(newWalkCreator) {
            if (!this.form.walkTeamMembers.includes(newWalkCreator)) {
                this.form.walkTeamMembers.push(newWalkCreator)
            }
        },
        async getWalkTeamMembersOfLastWalkOfTeam(team) {
            const response = await WalkAPI.findLastWalkByTeam(team);
            const hits = response.data['totalItems'];
            let result = [];
            if (hits) {
                response.data['member'][0].walkTeamMembers.forEach((userIri) => {
                    if (-1 !== team.users.indexOf(userIri)) {
                        result.push(userIri);
                    }
                });
            } else {
                result = team.users;
            }

            if (-1 === result.indexOf(this.currentUser['@id'])) {
                result.push(this.currentUser['@id']);
            }

            return result;
        },
        getUserByIri(userIri) {
            return this.userStore.getUserByIri(userIri);
        },
        onSubmit: async function () {
            this.isFormLoading = true;

            const walk = await this.walkStore.create(this.form);
            window.scrollTo({
                top: 0,
                left: 0,
                behavior: 'smooth'
            });

            if (walk) {
                this.alertStore.success(`Die Runde "${walk.name}" wurde erfolgreich erstellt.`, 'Runde erstellt');
                this.$router.push({name: 'WalkAddWayPoint', params: {walkId: walk.walkId}});
            } else {
                this.alertStore.error('Runde erstellen fehlgeschlagen', 'Upps! :-(');
            }
            this.isFormLoading = false;
        }
    },
}
</script>

<style>

</style>
