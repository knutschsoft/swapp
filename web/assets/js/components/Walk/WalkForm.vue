<template>
    <b-form
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
        <form-group label="Tageskonzept">
            <b-input-group>
                <b-form-tags
                    v-model="walk.conceptOfDay"
                    :disabled="isLoading"
                    tag-pills
                    placeholder="Tageskonzept eintragen..."
                    add-button-text="Hinzufügen"
                    duplicate-tag-text="Tageskonzept ist schon dabei"
                    remove-on-delete
                    :input-attrs="{ list: 'concept-of-day-list', 'data-test': 'Tageskonzept' }"
                    add-on-change
                    :state="conceptOfDayState"
                />
                <datalist id="concept-of-day-list">
                    <option v-for="conceptOfDaySuggestion in conceptOfDaySuggestions">{{ conceptOfDaySuggestion }}</option>
                </datalist>
                <b-input-group-append>
                    <b-button
                        @click="walk.conceptOfDay = []"
                        :disabled="!walk.conceptOfDay.length"
                    >
                        <mdicon name="CloseCircleOutline" size="20"/>
                    </b-button>
                </b-input-group-append>
            </b-input-group>
        </form-group>
        <form-group label="Rundenstartzeit">
            <b-row class="mb-1 mt-0">
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
        <form-group label="Rundenendzeit">
            <b-row class="mb-1 mt-0">
                <b-col>
                    <b-datepicker
                        v-model="endTimeDate"
                        v-bind="dateLabels['de']"
                        :disabled="isLoading"
                        :state="endTimeState"
                        data-test="endTimeDate"
                        locale="de"
                    />
                </b-col>
                <b-col>
                    <b-timepicker
                        v-model="endTimeTime"
                        v-bind="timeLabels['de']"
                        :disabled="isLoading"
                        :state="endTimeState"
                        data-test="endTimeTime"
                        minutes-step="5"
                        locale="de"
                        right
                    />
                </b-col>
            </b-row>
            <div class="mt-2 border-left-0 border-bottom-0 border-right-0 border-secondary border-dashed border-top"/>
            <b-row class="mb-1 mt-0">
                <b-col
                    class="mt-2"
                >

                    <v-btn
                        color="secondary"
                        outlined
                        block
                        small
                        @click="selectCurrentTime"
                    >
                        Schnellauswahl: aktueller Zeitpunkt
                    </v-btn>
                </b-col>
                <b-col
                    class="mt-2"
                >
                    <v-btn
                        color="secondary"
                        outlined
                        block
                        small
                        @click="selectFiveMinutesAfterLastWayPointOrStartOfWalkTime"
                    >
                        Schnellauswahl: {{ initialWalk.wayPoints.length ? '5 Minuten nach dem letzten Wegpunkt' : 'Rundenbeginn' }}
                    </v-btn>
                </b-col>
            </b-row>
            <template v-slot:valid-feedback>
                <v-alert
                    v-if="!!diffLastWayPointOrRound"
                    class="mb-0 mt-2"
                    color="warning"
                >
                    Hinweis: Die gewählte Ankunftszeit ist <b>{{ diffLastWayPointOrRound }}</b> nach dem {{ hasLastWayPoint ? 'letzten Wegpunkt' : 'Rundenstart' }} vom
                    {{ lastWayPointOrRoundTimeAsCalendar }}.
                </v-alert>
            </template>
        </form-group>
        <form-group label="Ferien">
            <b-form-checkbox
                v-model="walk.holidays"
                :disabled="isLoading"
                class="mt-lg-1 pt-lg-1"
            >
                ja, es sind Ferien
            </b-form-checkbox>
        </form-group>
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
        <b-form-group
            content-cols="12"
            label-cols="12"
            content-cols-lg="10"
            label-cols-lg="2"
            label="Reflexion"
            description=""
            :disabled="isLoading"
            :state="walkReflectionState"
        >
            <template v-slot:label>
                <div class="d-flex justify-content-between flex-wrap">
                    <div :class="isWithoutWalkReflection ? `text-muted` : ``">
                        Reflexion
                    </div>
                    <b-form-checkbox
                        v-model="isWithoutWalkReflection"
                        :disabled="isLoading"
                        class="font-weight-normal"
                    >
                        nicht benötigt
                    </b-form-checkbox>
                </div>
            </template>
            <b-textarea
                v-model="walk.walkReflection"
                :disabled="isLoading || isWithoutWalkReflection"
                minlength="1"
                maxlength="2500"
                placeholder="Reflexion"
                :state="walkReflectionState"
                data-test="walkReflection"
                rows="3"
                trim
                max-rows="15"
            />
        </b-form-group>
        <form-group label="Rundenbewertung">
            <walk-rating
                v-if="walk.rating && walkClient"
                :rating="walk.rating"
                :client="walkClient"
                :read-only="isLoading"
                @select-rating="walk.rating = $event"
            />
        </form-group>
        <b-form-group
            content-cols="12"
            label-cols="12"
            content-cols-lg="10"
            label-cols-lg="2"
            description=""
            :disabled="isLoading"
            :state="commitmentsState"
        >
            <template v-slot:label>
                <div class="d-flex justify-content-between flex-wrap">
                    <div :class="isWithoutCommitments ? `text-muted ` : ``">
                        Termine, Besorgungen, Verabredungen
                    </div>
                    <b-form-checkbox
                        v-model="isWithoutCommitments"
                        :disabled="isLoading"
                        class="font-weight-normal"
                    >
                        nicht benötigt
                    </b-form-checkbox>
                </div>
            </template>
            <b-textarea
                v-model="walk.commitments"
                :disabled="isLoading || isWithoutCommitments"
                minlength="1"
                maxlength="2500"
                placeholder="Termine, Besorgungen, Verabredungen"
                :state="commitmentsState"
                data-test="commitments"
                rows="3"
                trim
                max-rows="15"
            />
        </b-form-group>
        <b-form-group
            content-cols="12"
            label-cols="12"
            content-cols-lg="10"
            label-cols-lg="2"
            :state="insightsState"
        >
            <template v-slot:label>
                <div class="d-flex justify-content-between flex-wrap">
                    <div :class="isWithoutInsights ? `text-muted` : ``">
                        Erkenntnisse, Überlegungen, Zielsetzungen
                    </div>
                    <b-form-checkbox
                        v-model="isWithoutInsights"
                        :disabled="isLoading"
                        class="font-weight-normal"
                    >
                        nicht benötigt
                    </b-form-checkbox>
                </div>
            </template>
            <b-textarea
                v-model="walk.insights"
                :disabled="isLoading || isWithoutInsights"
                minlength="1"
                maxlength="2500"
                placeholder="Erkenntnisse, Überlegungen, Zielsetzungen"
                :state="insightsState"
                data-test="insights"
                rows="3"
                trim
                max-rows="15"
            />
        </b-form-group>
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
            :tabindex="isFormInvalid ? '-1' : ''"
        >
            {{ submitButtonText }}
        </v-btn>
        <form-error
            :error="error"
        />
    </b-form>
</template>

<script>
'use strict';
import dayjs from 'dayjs';
import FormError from '../Common/FormError.vue';
import FormGroup from '../Common/FormGroup.vue';
import {StarRating} from 'vue-rate-it';
import WalkRating from './WalkRating.vue';
import {useAuthStore, useTeamStore, useUserStore, useWalkStore, useWayPointStore} from '../../stores';
import {WalkGuestNamesField, WalkNameField, WalkTeamMembersField, WalkWalkCreatorField, WalkWeatherField} from "../Common/Walk";

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
            initialConceptOfDay: [],
            initialWalkName: '',
            isWithoutSystemicAnswer: false,
            isWithoutWalkReflection: false,
            isWithoutCommitments: false,
            isWithoutInsights: false,
            startTimeDate: null,
            startTimeTime: null,
            endTimeDate: null,
            endTimeTime: null,
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
                const wayPoint = this.wayPointStore.getWayPointByIri(wayPointIri);
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
        conceptOfDaySuggestions() {
            let conceptOfDaySuggestions = [];
            if (!this.team) {
                return conceptOfDaySuggestions;
            }
            conceptOfDaySuggestions = [...new Set(this.initialConceptOfDay), ...new Set(this.team.conceptOfDaySuggestions)];

            return conceptOfDaySuggestions.filter((conceptOfDaySuggestion) => {
                return !this.walk.conceptOfDay.includes(conceptOfDaySuggestion);
            });
        },
        commitmentsState() {
            if (this.isWithoutCommitments) {
                return true;
            }
            if (null === this.walk.commitments || undefined === this.walk.commitments) {
                return;
            }

            return this.walk.commitments.length >= 1 && this.walk.commitments.length <= 2500;
        },
        conceptOfDayState() {
            if (null === this.walk.conceptOfDay || undefined === this.walk.conceptOfDay) {
                return;
            }

            return this.walk.conceptOfDay.length >= 1 && this.walk.conceptOfDay.length <= 2500;
        },
        insightsState() {
            if (this.isWithoutInsights) {
                return true;
            }
            if (null === this.walk.insights || undefined === this.walk.insights) {
                return;
            }

            return this.walk.insights.length >= 1 && this.walk.insights.length <= 2500;
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
        startTimeState() {
            if (null === this.walk.startTime || undefined === this.walk.startTime) {
                return;
            }

            return !!this.walk.startTime;
        },
        endTimeState() {
            if (null === this.walk.endTime || undefined === this.walk.endTime) {
                return;
            }

            return this.walk.endTime > this.walk.startTime;
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
                || !this.commitmentsState
                || !this.conceptOfDayState
                || !this.insightsState
                || !this.startTimeState
                || !this.endTimeState
                || !this.systemicAnswerState
                || !this.walkReflectionState
                || this.walk.walkCreator
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
        endTimeTime(endTimeTime) {
            const values = endTimeTime.split(':');
            if (values.length < 2) {
                return;
            }
            let endTime = dayjs(this.walk.endTime);
            endTime = endTime.hour(Number(values[0]));
            endTime = endTime.minute(Number(values[1]));
            this.walk.endTime = endTime.format();
        },
        endTimeDate(endTimeDate) {
            const endTimeDateValue = dayjs(endTimeDate);
            let endTime = dayjs(this.walk.endTime);
            endTime = endTime.year(endTimeDateValue.year());
            endTime = endTime.month(endTimeDateValue.month());
            endTime = endTime.date(endTimeDateValue.date());
            this.walk.endTime = endTime.format();
        },
    },
    async created() {
        this.walk.name = this.initialWalk.name;
        this.initialConceptOfDay = this.initialWalk.conceptOfDay;
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

        this.startTimeTime = dayjs(this.walk.startTime).format('HH:mm');
        this.startTimeDate = dayjs(this.walk.startTime).format('YYYY-MM-DD');
        this.endTimeTime = dayjs(this.walk.endTime).format('HH:mm');
        this.endTimeDate = dayjs(this.walk.endTime).format('YYYY-MM-DD');
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
            this.endTimeTime = dayjs().format('HH:mm');
            this.endTimeDate = dayjs().format('YYYY-MM-DD');
        },
        selectFiveMinutesAfterLastWayPointOrStartOfWalkTime() {
            let time = this.lastWayPointOrRoundTime;
            if (this.hasLastWayPoint) {
                time = time.add(5, 'minute');
            }
            this.endTimeTime = time.format('HH:mm');
            this.endTimeDate = time.format('YYYY-MM-DD');
        },
        async handleSubmit() {
            this.$emit('submit', this.walk);
        },
    },
};
</script>

<style scoped lang="scss">
</style>
