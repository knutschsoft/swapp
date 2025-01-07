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
        <v-row class="mb-1 mt-0">
            <v-col
                cols="12"
                sm="12"
                md="6"
                class="mt-2"
            >
                <walk-end-time-field
                    v-model="walk.endTime"
                    :initial-walk="initialWalk"
                    :is-loading="isLoading"
                    :error="error"
                />
            </v-col>
            <v-col
                cols="12"
                class="d-md-none"
            >
                <div class="mt-2 border-left-0 border-bottom-0 border-right-0 border-secondary border-dashed border-top"/>
            </v-col>
            <v-col
                cols="12"
                sm="6"
                md="3"
                class="mt-2"
            >
                <div class="d-none d-md-block">
                    &nbsp;
                </div>
                <v-btn
                    color="secondary"
                    outlined
                    small
                    block
                    min-height="40px"
                    @click="selectCurrentTime"
                >
                    Schnellauswahl:<br>aktueller Zeitpunkt
                </v-btn>
            </v-col>
            <v-col
                cols="12"
                sm="6"
                md="3"
                class="mt-2"
            >
                <div class="d-none d-md-block">
                    &nbsp;
                </div>
                <v-btn
                    color="secondary"
                    outlined
                    small
                    block
                    min-height="40px"
                    @click="selectFiveMinutesAfterLastWayPointOrStartOfWalkTime"
                >
                    Schnellauswahl:<br>{{ initialWalk.wayPoints.length ? '5 Minuten nach dem letzten Wegpunkt' : 'Rundenbeginn' }}
                </v-btn>
            </v-col>
        </v-row>
        <v-alert
            v-if="!!diffLastWayPointOrRound"
            class="mb-0 mt-2"
            color="warning"
        >
            Hinweis: Die gewählte Ankunftszeit ist <b>{{ diffLastWayPointOrRound }}</b> nach dem {{ hasLastWayPoint ? 'letzten Wegpunkt' : 'Rundenstart' }} vom
            {{ lastWayPointOrRoundTimeAsCalendar }}.
        </v-alert>
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
        <form-group
            v-if="walk.isWithSystemicQuestion"
            label="Systemische Frage"
        >
            <b-form-input
                v-model="walk.systemicQuestion"
                disabled
                readonly
            />
        </form-group>
        <b-form-group
            v-if="walk.isWithSystemicQuestion"
            content-cols="12"
            label-cols="12"
            content-cols-lg="10"
            label-cols-lg="2"
            :description="walk.systemicQuestion"
            :disabled="isLoading"
            :state="systemicAnswerState"
        >
            <template v-slot:label>
                <div class="d-flex justify-content-between flex-wrap">
                    <div :class="isWithoutSystemicAnswer ? `text-muted` : ``">
                        Systemische Antwort
                    </div>
                    <b-form-checkbox
                        v-model="isWithoutSystemicAnswer"
                        :disabled="isLoading"
                        class="font-weight-normal"
                    >
                        nicht benötigt
                    </b-form-checkbox>
                </div>
            </template>
            <b-textarea
                v-model="walk.systemicAnswer"
                :disabled="isLoading || isWithoutSystemicAnswer"
                minlength="1"
                maxlength="2500"
                placeholder="Systemische Antwort"
                :state="systemicAnswerState"
                data-test="systemicAnswer"
                rows="3"
                trim
                max-rows="15"
            />
        </b-form-group>
        <walk-walk-reflection-field
            v-model="walk.walkReflection"
            :is-loading="isLoading"
            :disabled="isLoading || isWithoutWalkReflection"
            :error="error"
        />
        <v-switch
            v-model="isWithoutWalkReflection"
            label="nicht benötigt"
            class="mt-0 ml-auto"
            dense
        />
        <form-group label="Rundenbewertung">
            <walk-rating
                v-if="walk.rating && walkClient"
                :rating="walk.rating"
                :client="walkClient"
                :read-only="isLoading"
                @select-rating="walk.rating = $event"
            />
        </form-group>
        <walk-commitments-field
            v-model="walk.commitments"
            :is-loading="isLoading"
            :disabled="isLoading || isWithoutCommitments"
            :error="error"
        />
        <v-switch
            v-model="isWithoutCommitments"
            label="nicht benötigt"
            class="mt-0 ml-auto"
            dense
        />
        <walk-insights-field
            v-model="walk.insights"
            :is-loading="isLoading"
            :disabled="isLoading || isWithoutInsights"
            :error="error"
        />
        <v-switch
            v-model="isWithoutInsights"
            label="nicht benötigt"
            class="mt-0 ml-auto"
            dense
        />
        <form-group label="">
            <b-form-checkbox
                v-model="walk.isResubmission"
                :disabled="isLoading"
            >
                Wiedervorlage Dienstberatung
            </b-form-checkbox>
        </form-group>
        <v-btn
            type="submit"
            color="secondary"
            :disabled="isFormInvalid || isSubmitDisabled"
            data-test="button-walk-submit"
            block
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
import {StarRating} from 'vue-rate-it';
import WalkRating from './WalkRating.vue';
import {useAuthStore, useClientStore, useTeamStore, useUserStore, useWalkStore, useWayPointStore} from '../../stores';
import {
    WalkCommitmentsField,
    WalkConceptOfDayField,
    WalkEndTimeField,
    WalkGuestNamesField,
    WalkHolidaysField,
    WalkInsightsField,
    WalkNameField,
    WalkStartTimeField,
    WalkTeamMembersField,
    WalkWalkCreatorField,
    WalkWalkReflectionField,
    WalkWeatherField
} from "../Common/Walk";

export default {
    name: 'WalkForm',
    props: {
        initialWalk: {
            type: Object,
            required: false,
            default: {},
        },
        submitButtonText: {
            type: String,
            required: true,
        },
    },
    components: {
        WalkInsightsField,
        WalkCommitmentsField,
        WalkWalkReflectionField,
        WalkEndTimeField,
        WalkStartTimeField,
        WalkHolidaysField,
        WalkConceptOfDayField,
        WalkGuestNamesField,
        WalkWalkCreatorField,
        WalkNameField,
        WalkWeatherField,
        WalkTeamMembersField,
        WalkRating,
        FormGroup,
        FormError,
        StarRating,
    },
    data: function () {
        return {
            authStore: useAuthStore(),
            clientStore: useClientStore(),
            teamStore: useTeamStore(),
            userStore: useUserStore(),
            walkStore: useWalkStore(),
            wayPointStore: useWayPointStore(),
            initialWalkName: '',
            isWithoutSystemicAnswer: false,
            isWithoutWalkReflection: false,
            isWithoutCommitments: false,
            isWithoutInsights: false,
            walk: {
                name: null,
                commitments: null,
                conceptOfDay: null,
                startTime: null,
                endTime: null,
                holidays: null,
                insights: null,
                isResubmission: null,
                rating: null,
                systemicAnswer: null,
                systemicQuestion: null,
                walkReflection: null,
                weather: '',
                walkTeamMembers: [],
                walkCreator: null,
                guestNames: [],
            },
        };
    },
    computed: {
        hasError() {
            return !!this.error;
        },
        walkClient() {
            return this.clientStore.getClientByIri(this.initialWalk.client || this.currentUser.client);
        },
        hasLastWayPoint() {
            return this.initialWalk.wayPoints.length > 0;
        },
        wayPointsOfInitialWalk() {
            let wayPoints = [];
            this.initialWalk.wayPoints.forEach(wayPointIri => {
                const wayPoint = this.getWayPointByIri(wayPointIri);
                if (wayPoint) {
                    wayPoints.push(wayPoint);
                }
            });

            return wayPoints;
        },
        lastWayPointOrRoundTime() {
            let time = false;
            this.wayPointsOfInitialWalk
                .slice()
                .sort((a, b) => {
                        if (dayjs(a.visitedAt).isAfter(dayjs(b.visitedAt))) {
                            return -1;
                        }
                        return 1;
                    },
                )
                .every(wayPoint => {
                        if (false === wayPoint) {
                            return true;
                        }
                        time = dayjs(wayPoint.visitedAt);

                        return false;
                    }
                );

            if (time) {
                return time;
            }

            return dayjs(this.initialWalk.startTime);
        },
        lastWayPointOrRoundTimeAsCalendar() {
            return this.lastWayPointOrRoundTime.calendar();
        },
        diffLastWayPointOrRound() {
            const diff = dayjs(this.walk.endTime).diff(this.lastWayPointOrRoundTime, 'minute');
            if (diff > 240) { // 4 hours
                return dayjs(this.walk.endTime).to(this.lastWayPointOrRoundTime, true);
            }

            return false;
        },
        isSubmitDisabled() {
            return !this.walk
                || !this.walk.systemicAnswer && !this.isWithoutSystemicAnswer
                || !this.walk.walkReflection && !this.isWithoutWalkReflection
                || !this.walk.commitments && !this.isWithoutCommitments
                || !this.walk.insights && !this.isWithoutInsights
        },
        team() {
            return this.teamStore.getTeamByTeamName(this.initialWalk.teamName);
        },
        systemicAnswerState() {
            if (this.isWithoutSystemicAnswer) {
                return true;
            }
            if (null === this.walk.systemicAnswer || undefined === this.walk.systemicAnswer) {
                return;
            }

            return this.walk.systemicAnswer.length >= 1 && this.walk.systemicAnswer.length <= 2500;
        },
        walkReflectionState() {
            if (this.isWithoutWalkReflection) {
                return true;
            }
            if (null === this.walk.walkReflection || undefined === this.walk.walkReflection) {
                return;
            }

            return this.walk.walkReflection.length >= 1 && this.walk.walkReflection.length <= 2500;
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
            if (this.initialWalk) {
                return this.walkStore.isLoadingChange(this.initialWalk['@id'])
            }

            return this.walkStore.isLoadingCreate;
        },
        currentUser() {
            return this.authStore.currentUser;
        },
        users() {
            return this.userStore.getUsers;
        },
        isSuperAdmin() {
            return this.authStore.isSuperAdmin;
        },
        isFormInvalid() {
            return !this.walk.name
                || !this.walk.commitments && !this.isWithoutCommitments
                || !this.walk.conceptOfDay
                || !this.walk.insights && !this.isWithoutInsights
                || !this.walk.startTime
                || !this.walk.endTime
                || !this.systemicAnswerState
                || !this.walkReflectionState
                || !this.walk.walkCreator
                || this.isLoading;
        },
        error() {
            return this.walkStore.getErrors.change;
        },
    },
    async created() {
        this.walk.name = this.initialWalk.name;
        this.initialWalkName = this.initialWalk.name;
        this.walk.commitments = this.initialWalk.commitments;
        this.walk.conceptOfDay = this.initialWalk.conceptOfDay;
        this.walk.startTime = this.initialWalk.startTime;
        this.walk.endTime = this.initialWalk.endTime;
        this.walk.holidays = this.initialWalk.holidays;
        this.walk.insights = this.initialWalk.insights;
        this.walk.isResubmission = this.initialWalk.isResubmission;
        this.walk.rating = this.initialWalk.rating;
        this.isWithoutSystemicAnswer = !this.walk.isWithSystemicQuestion;
        this.walk.systemicAnswer = this.initialWalk.systemicAnswer;
        this.walk.systemicQuestion = this.initialWalk.systemicQuestion;
        this.walk.walkReflection = this.initialWalk.walkReflection;
        this.walk.weather = this.initialWalk.weather;
        this.walk.walkTeamMembers = this.initialWalk.walkTeamMembers.slice();
        this.walk.walkCreator = this.initialWalk.walkCreator;
        this.walk.guestNames = this.initialWalk.guestNames.slice();

        this.isWithoutSystemicAnswer = !this.walk.systemicAnswer.length;
        this.isWithoutWalkReflection = !this.walk.walkReflection.length;
        this.isWithoutCommitments = !this.walk.commitments.length;
        this.isWithoutInsights = !this.walk.insights.length;

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
        getWayPointByIri(iri) {
            return this.wayPointStore.getWayPointByIri(iri);
        },
        selectCurrentTime() {
            this.walk.endTime = dayjs().format();
        },
        selectFiveMinutesAfterLastWayPointOrStartOfWalkTime() {
            let time = this.lastWayPointOrRoundTime;
            if (this.hasLastWayPoint) {
                time = time.add(5, 'minute');
            }
            this.walk.endTime = time.format();
        },
        async handleSubmit() {
            this.$emit('submit', this.walk);
        },
    },
};
</script>

<style scoped lang="scss">
</style>
