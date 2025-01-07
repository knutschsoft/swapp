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
        <form-group label="Rundenstartzeit">
            <b-row>
                <b-col>
                    <b-datepicker
                        v-model="startTimeDate"
                        v-bind="dateLabels['de']"
                        :disabled="isLoading"
                        :state="startTimeState"
                        data-test="startTimeDate"
                        locale="de"
                    />
                </b-col>
                <b-col>
                    <b-timepicker
                        v-model="startTimeTime"
                        v-bind="timeLabels['de']"
                        :disabled="isLoading"
                        :state="startTimeState"
                        data-test="startTimeTime"
                        minutes-step="5"
                        locale="de"
                        right
                    />
                </b-col>
            </b-row>
        </form-group>
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
import {WalkConceptOfDayField, WalkGuestNamesField, WalkHolidaysField, WalkNameField, WalkTeamMembersField, WalkWalkCreatorField, WalkWeatherField} from "../Common/Walk";

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
            initialWalkName: '',
            startTimeDate: null,
            startTimeTime: null,
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
            dateLabels: {
                de: {
                    labelPrevDecade: 'Vorheriges Jahrzehnt',
                    labelPrevYear: 'Vorheriges Jahr',
                    labelPrevMonth: 'Vorheriger Monat',
                    labelCurrentMonth: 'Aktueller Monat',
                    labelNextMonth: 'Nächster Monat',
                    labelNextYear: 'Nächstes Jahr',
                    labelNextDecade: 'Nächstes Jahrzehnt',
                    labelToday: 'Heute',
                    labelSelected: 'Ausgewähltes Datum',
                    labelNoDateSelected: 'Kein Datum gewählt',
                    labelCalendar: 'Kalender',
                    labelNav: 'Kalendernavigation',
                    labelHelp: 'Mit den Pfeiltasten durch den Kalender navigieren'
                },
            },
            timeLabels: {
                de: {
                    labelHours: 'Stunden',
                    labelMinutes: 'Minuten',
                    labelSeconds: 'Sekunden',
                    labelIncrement: 'Erhöhen',
                    labelDecrement: 'Verringern',
                    labelSelected: 'Ausgewählte Zeit',
                    labelNoTimeSelected: 'Keine Zeit ausgewählt',
                    labelCloseButton: 'Schließen'
                },
            },
        };
    },
    computed: {
        isSubmitDisabled() {
            return !this.walk
                || !this.walk.weather
                || !this.walk.conceptOfDay
                || (!this.startTimeState && undefined === this.validationErrors.startTime)
                || !this.walkTeamMembersState
                || !this.walk.walkCreator
                || !this.walk.weather
                || this.isLoading;
        },
        team() {
            return this.teamStore.getTeamByTeamName(this.initialWalk.teamName);
        },
        walkTeamMembersState() {
            if (!this.walk.walkTeamMembers || !this.walk.walkTeamMembers.length) {
                return null;
            }

            return undefined === this.validationErrors.walkTeamMembers;
        },
        startTimeState() {
            if (null === this.walk.startTime || undefined === this.walk.startTime) {
                return;
            }

            return !!this.walk.startTime;
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
                || !this.startTimeState
                || !this.walk.walkCreator
                || this.isLoading;
        },
        error() {
            return this.walkStore.getErrors.change;
        },
    },
    watch: {
        startTimeTime(startTimeTime) {
            const values = startTimeTime.split(':');
            if (values.length < 2) {
                return;
            }
            let startTime = dayjs(this.walk.startTime);
            startTime = startTime.hour(Number(values[0]));
            startTime = startTime.minute(Number(values[1]));
            startTime = startTime.startOf('minute');
            this.walk.startTime = startTime.format();
        },
        startTimeDate(startTimeDate) {
            const startTimeDateValue = dayjs(startTimeDate);
            let startTime = dayjs(this.walk.startTime);
            startTime = startTime.year(startTimeDateValue.year());
            startTime = startTime.month(startTimeDateValue.month());
            startTime = startTime.date(startTimeDateValue.date());
            startTime = startTime.startOf('minute');
            this.walk.startTime = startTime.format();
        },
    },
    async created() {
        this.walk.name = this.initialWalk.name;
        this.initialWalkName = this.initialWalk.name;
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

        this.startTimeTime = dayjs(this.walk.startTime).format('HH:mm');
        this.startTimeDate = dayjs(this.walk.startTime).format('YYYY-MM-DD');
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
