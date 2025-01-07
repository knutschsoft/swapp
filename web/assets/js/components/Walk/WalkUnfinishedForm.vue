<template>
    <v-form
        @submit.prevent.stop="handleSubmit"
        class="p-1 p-sm-2 p-lg-3"
    >
        <walk-walk-creator-field
            v-model="walk.walkCreator"
            :team="team"
            :walk="initialWalk"
            :is-loading="isLoading"
            :error="error"
            @change="handleWalkCreatorChange"
        />
        <walk-team-members-field
            v-model="walk.walkTeamMembers"
            :users="users"
            :walk-creator="walk.walkCreator"
            :is-loading="isLoading"
            label="Teilnehmende der Runde"
            description="Wer war mit dabei?"
        />
        <walk-guest-names-field
            v-if="walk.isWithGuests"
            v-model="walk.guestNames"
            :team="team"
            :initial-walk="initialWalk"
            :is-loading="isLoading"
            :error="error"
        />
        <walk-name-field
            v-model="walk.name"
            :team="team"
            :walk="initialWalk"
            :is-loading="isLoading"
            :error="error"
        />
        <walk-concept-of-day-field
            v-model="walk.conceptOfDay"
            :team="team"
            :initial-walk="initialWalk"
            :is-loading="isLoading"
            :error="error"
        />
        <walk-start-time-field
            v-model="walk.startTime"
            :initial-walk="initialWalk"
            :is-loading="isLoading"
            :error="error"
        />
        <walk-holidays-field
            v-model="walk.holidays"
            :is-loading="isLoading"
            :error="error"
        />
        <walk-weather-field
            v-model="walk.weather"
            :is-loading="isLoading"
            :error="error"
        />
        <v-btn
            color="secondary"
            type="submit"
            :disabled="isFormInvalid || isSubmitDisabled"
            data-test="button-walk-submit"
            block
            :tabindex="isFormInvalid ? '-1' : ''"
        >
            {{ submitButtonText }}
        </v-btn>
        <form-error
            :error="error"
        />
    </v-form>
</template>

<script>
'use strict';
import dayjs from 'dayjs';
import FormError from '../Common/FormError.vue';
import FormGroup from '../Common/FormGroup.vue';
import {useTeamStore, useUserStore, useWalkStore, useWayPointStore} from '../../stores';
import {WalkConceptOfDayField, WalkGuestNamesField, WalkHolidaysField, WalkNameField, WalkStartTimeField, WalkTeamMembersField, WalkWalkCreatorField, WalkWeatherField} from "../Common/Walk";

export default {
    name: 'WalkUnfinishedForm',
    props: {
        initialWalk: {
            type: Object,
            required: true,
        },
        submitButtonText: {
            type: String,
            required: true,
        },
    },
    components: {
        WalkStartTimeField,
        WalkHolidaysField,
        WalkConceptOfDayField,
        WalkGuestNamesField,
        WalkWalkCreatorField,
        WalkNameField,
        WalkWeatherField,
        WalkTeamMembersField,
        FormGroup,
        FormError,
    },
    data: function () {
        return {
            teamStore: useTeamStore(),
            userStore: useUserStore(),
            walkStore: useWalkStore(),
            wayPointStore: useWayPointStore(),
            walk: {
                name: null,
                conceptOfDay: [],
                startTime: null,
                holidays: null,
                weather: '',
                walkTeamMembers: [],
                walkCreator: '',
                guestNames: [],
            },
        };
    },
    computed: {
        isSubmitDisabled() {
            return !this.walk
                || !this.walk.weather
                || !this.walk.conceptOfDay
                || !this.walk.startTime
                || !this.walk.walkTeamMembers.length
                || !this.walk.walkCreator
                || !this.walk.weather
                || this.isLoading;
        },
        team() {
            return this.teamStore.getTeamByTeamName(this.initialWalk.teamName);
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
            if (error.data && error.data['hydra:description']) {
                errors.global = error.data['hydra:description'];
            }

            return errors;
        },
        isLoading() {
            return this.walkStore.isLoadingChange(this.initialWalk['@id']);
        },
        currentUser() {
            return this.authStore.currentUser;
        },
        users() {
            return this.userStore.getUsers;
        },
        isFormInvalid() {
            return !this.walk.name
                || !this.walk.conceptOfDay
                || !this.walk.startTime
                || !this.walk.walkCreator
                || this.isLoading;
        },
        error() {
            return this.walkStore.getErrors.change;
        },
    },
    async created() {
        this.walk.name = this.initialWalk.name;
        this.walk.conceptOfDay = this.initialWalk.conceptOfDay;
        this.walk.startTime = this.initialWalk.startTime;
        this.walk.holidays = this.initialWalk.holidays;
        this.walk.weather = this.initialWalk.weather;
        this.walk.walkTeamMembers = this.initialWalk.walkTeamMembers.slice();
        this.walk.walkCreator = this.initialWalk.walkCreator;
        this.walk.guestNames = this.initialWalk.guestNames.slice();

        if (!this.users.length) {
            await this.userStore.fetchUsers();
        }
        if (!this.team) {
            await this.teamStore.fetchTeams();
        }
    },
    methods: {
        handleWalkCreatorChange(newWalkCreator) {
            if (!newWalkCreator) {
                return;
            }
            if (!this.walk.walkTeamMembers.includes(newWalkCreator)) {
                this.walk.walkTeamMembers.push(newWalkCreator)
            }
        },
        getUserByIri(userIri) {
            return this.userStore.getUserByIri(userIri);
        },
        async handleSubmit() {
            this.$emit('submit', this.walk);
        },
    },
};
</script>

<style scoped lang="scss">
</style>
