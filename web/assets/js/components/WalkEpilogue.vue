<template>
    <div>
        <v-alert
            v-if="!!successMessage"
            type="success"
            class="mt-1 mt-sm-2 mt-lg-3 mb-0"
        >
            {{ successMessage }}
        </v-alert>
        <content-collapse
            v-if="walk"
            :title="`Runde &quot;${walk?.name}&quot; abschließen`"
            collapse-key="walk-epilogue"
            is-visible-by-default
        >
            <v-form
                @submit.prevent.stop="handleSubmit"
                class="p-1 p-sm-2 p-lg-3"
            >
                <walk-name-field
                    v-model="form.name"
                    :team="team"
                    :walk="walk"
                    :is-loading="isLoading"
                    :error="error"
                    description="Der Wert vom Rundenbeginn ist vorausgewählt."
                />
                <walk-concept-of-day-field
                    v-model="form.conceptOfDay"
                    :team="team"
                    :initial-walk="walk"
                    :is-loading="isLoading"
                    :error="error"
                    description="Der Wert vom Rundenbeginn ist vorausgewählt."
                />
                <walk-start-time-field
                    v-model="form.startTime"
                    :initial-walk="walk"
                    :is-loading="isLoading"
                    :error="error"
                    description="Die Zeit vom Rundenbeginn ist vorausgewählt."
                />
                <v-row class="mb-1 mt-0">
                    <v-col
                        cols="12"
                        sm="12"
                        md="6"
                        class="mt-2"
                    >
                        <walk-end-time-field
                            v-model="form.endTime"
                            :initial-walk="walk"
                            :is-loading="isLoading"
                            :error="error"
                            description="Die aktuelle Zeit ist vorausgewählt."
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
                            Schnellauswahl:<br>{{ walk.wayPoints.length ? '5 Minuten nach dem letzten Wegpunkt' : 'Rundenbeginn' }}
                        </v-btn>
                    </v-col>
                </v-row>
                <v-alert
                    v-if="!!diffLastWayPointOrRound"
                    class="mb-0"
                    color="warning"
                >
                    Hinweis: Die gewählte Ankunftszeit ist <b>{{ diffLastWayPointOrRound }}</b> nach dem {{ hasLastWayPoint ? 'letzten Wegpunkt' : 'Rundenstart' }} vom
                    {{ lastWayPointOrRoundTimeAsCalendar }}.
                </v-alert>
                <walk-holidays-field
                    v-model="form.holidays"
                    :is-loading="isLoading"
                    :error="error"
                    description="Der Wert vom Rundenbeginn ist vorausgewählt."
                />
                <walk-weather-field
                    v-model="form.weather"
                    :is-loading="isLoading"
                    :error="error"
                    description="Der Wert vom Rundenbeginn ist vorausgewählt."
                />
                <b-form-group
                    v-if="walk.isWithSystemicQuestion"
                    content-cols="12"
                    label-cols="12"
                    content-cols-lg="10"
                    label-cols-lg="2"
                    :description="walk.systemicQuestion"
                    :disabled="isLoading"
                    :invalid-feedback="invalidSystemicAnswerFeedback"
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
                        v-model="form.systemicAnswer"
                        minlength="1"
                        maxlength="2500"
                        :placeholder="walk.systemicQuestion"
                        :disabled="isLoading || isWithoutSystemicAnswer"
                        :state="systemicAnswerState"
                        data-test="systemicAnswer"
                        rows="3"
                        trim
                        max-rows="15"
                    />
                </b-form-group>
                <walk-walk-reflection-field
                    v-model="form.walkReflection"
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
                <b-form-group
                    content-cols="12"
                    label-cols="12"
                    content-cols-lg="10"
                    label-cols-lg="2"
                    label="Rundenbewertung"
                    description=""
                    :disabled="isLoading"
                >
                    <walk-rating
                        v-if="walkClient && form.rating"
                        :rating="form.rating"
                        :client="walkClient"
                        :read-only="isLoading"
                        @select-rating="form.rating = $event"
                    />
                </b-form-group>
                <walk-commitments-field
                    v-model="form.commitments"
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
                    v-model="form.insights"
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
                <b-form-group
                    content-cols="12"
                    label-cols="12"
                    content-cols-lg="10"
                    label-cols-lg="2"
                    label="Wiedervorlage Dienstberatung"
                    description=""
                >
                    <b-form-checkbox
                        v-model="form.isResubmission"
                        :disabled="isLoading"
                    >
                        Soll die Runde in der Dienstberatung wieder vorgelegt werden?
                    </b-form-checkbox>
                </b-form-group>
                <v-btn
                    type="submit"
                    color="secondary"
                    :disabled="isLoading || isSubmitDisabled"
                    data-test="button-walk-submit"
                    block
                >
                    Runde abschließen
                </v-btn>
                <global-form-error
                    :error="globalErrors"
                />
            </v-form>
            <div
                v-if="false"
                id="form-holder"
                ref="forms"
                v-on:submit.prevent="onSubmit"
                class="p-2"
            />
        </content-collapse>

        <content-collapse
            :title="`Wegpunkte der Runde &quot;${walk?.name}&quot;`"
            collapse-key="waypoints-of-round"
            is-visible-by-default
            :is-loading="!walk"
        >
            <WayPointList
                :walk-id="walkId"
            />
        </content-collapse>
    </div>
</template>

<script>
'use strict';
import ContentCollapse from './ContentCollapse.vue';
import GlobalFormError from './Common/GlobalFormError.vue';
import {
    WalkCommitmentsField,
    WalkConceptOfDayField,
    WalkEndTimeField,
    WalkHolidaysField,
    WalkInsightsField,
    WalkNameField,
    WalkStartTimeField,
    WalkWalkReflectionField,
    WalkWeatherField
} from "./Common/Walk";
import WayPointList from './Walk/WayPointList.vue';
import WalkRating from './Walk/WalkRating.vue';
import dayjs from 'dayjs';
import {getViolationsFeedback} from '../utils';
import {useAlertStore, useClientStore, useTeamStore, useWayPointStore, useWalkStore} from '../stores';

export default {
    name: 'WalkEpilogue',
    components: {
        WalkInsightsField,
        WalkCommitmentsField,
        WalkWalkReflectionField,
        WalkEndTimeField,
        WalkStartTimeField,
        WalkHolidaysField,
        WalkConceptOfDayField,
        WalkNameField,
        WalkWeatherField,
        ContentCollapse,
        GlobalFormError,
        WayPointList,
        WalkRating,
    },
    props: {
        walkId: {
            required: true,
        },
        successMessage: {
            required: false,
            type: String,
            default: '',
        },
    },
    data: function () {
        return {
            alertStore: useAlertStore(),
            clientStore: useClientStore(),
            teamStore: useTeamStore(),
            walkStore: useWalkStore(),
            wayPointStore: useWayPointStore(),
            initialWalkName: '',
            isWithoutSystemicAnswer: false,
            isWithoutWalkReflection: false,
            isWithoutCommitments: false,
            isWithoutInsights: false,
            form: {
                walk: '',
                name: '',
                conceptOfDay: [],
                startTime: '',
                endTime: dayjs().endOf('minute').format(),
                systemicAnswer: '',
                walkReflection: '',
                rating: 1,
                holidays: false,
                weather: '',
                insights: '',
                commitments: '',
                isResubmission: false,
            },
        };
    },
    computed: {
        walkClient() {
            return this.clientStore.getClientByIri(this.walk.client);
        },
        hasLastWayPoint() {
            return this.walk.wayPoints.length > 0;
        },
        wayPointsOfWalk() {
            let wayPoints = [];
            this.walk.wayPoints.forEach(wayPointIri => {
                const wayPoint = this.getWayPointByIri(wayPointIri);
                if (wayPoint) {
                    wayPoints.push(wayPoint);
                }
            });

            return wayPoints;
        },
        lastWayPointOrRoundTime() {
            let time = false;
            this.wayPointsOfWalk
                .slice()
                .sort((a, b) => {
                        if (dayjs(a.visitedAt).isAfter(dayjs(b.visitedAt))) {
                            return -1;
                        }
                        return 1;
                    },
                )
                .every(wayPoint => {
                        time = dayjs(wayPoint.visitedAt);

                        return false;
                    }
                );

            if (time) {
                return time;
            }

            return dayjs(this.walk.startTime);
        },
        lastWayPointOrRoundTimeAsCalendar() {
            return this.lastWayPointOrRoundTime.calendar();
        },
        diffLastWayPointOrRound() {
            const diff = dayjs(this.form.endTime).diff(this.lastWayPointOrRoundTime, 'minute');
            if (diff > 240) { // 4 hours
                return dayjs(this.form.endTime).to(this.lastWayPointOrRoundTime, true);
            }

            return false;
        },
        isSubmitDisabled() {
            return !this.form.systemicAnswer && !this.isWithoutSystemicAnswer
                || !this.form.walkReflection && !this.isWithoutWalkReflection
                || !this.form.commitments && !this.isWithoutCommitments
                || !this.form.insights && !this.isWithoutInsights
        },
        globalErrors() {
            return getViolationsFeedback([
                'name',
                'conceptOfDay',
                'startTime',
                'endTime',
                'weather',
                'systemicAnswer',
                'walkReflection',
                'insights',
                'commitments',
                'endTimeAfterWayPointsVisitedAt',
            ], this.error, true);
        },
        team() {
            return this.teamStore.getTeamByTeamName(this.walk.teamName);
        },
        systemicAnswerState() {
            if (this.isWithoutSystemicAnswer) {
                return true;
            }
            if (!this.form.systemicAnswer && !this.invalidSystemicAnswerFeedback) {
                return;
            }

            return !this.invalidSystemicAnswerFeedback;
        },
        invalidSystemicAnswerFeedback() {
            return getViolationsFeedback(['systemicAnswer'], this.error);
        },
        isLoading() {
            return this.walkStore.isLoading;
        },
        hasWalks() {
            return this.walkStore.hasWalks;
        },
        walks() {
            return this.walkStore.getWalks;
        },
        walk() {
            return this.walkStore.getWalkById(this.walkId);
        },
        error() {
            return this.walkStore.getErrors.change;
        },
    },
    async mounted() {
        await this.walkStore.resetChangeError();
        if (!this.walk) {
            await this.refreshWalk();
        }
        if (!this.walk) {
            this.$router.push({name: 'Dashboard', params: {redirect: 'Diese Runde existiert nicht. Du wurdest auf das Dashboard weitergeleitet.'}});
            return;
        }
        if (!this.team) {
            await this.teamStore.fetchTeams();
        }
        if (!this.walkClient) {
            await this.clientStore.fetchByIri(this.walk.client);
        }
        this.isWithoutSystemicAnswer = !this.walk.isWithSystemicQuestion;
        this.form.walk = this.walk['@id'];
        this.initialWalkName = this.walk.name;
        this.form.name = this.walk.name;
        this.form.startTime = this.walk.startTime;
        this.form.conceptOfDay = this.walk.conceptOfDay;

        this.form.systemicAnswer = this.walk.systemicAnswer;
        this.form.walkReflection = this.walk.walkReflection;
        this.form.holidays = this.walk.holidays;
        this.form.weather = this.walk.weather;
    },
    methods: {
        getWayPointByIri(iri) {
            return this.wayPointStore.getWayPointByIri(iri);
        },
        selectCurrentTime() {
            this.form.endTime = dayjs().format();
        },
        selectFiveMinutesAfterLastWayPointOrStartOfWalkTime() {
            let time = this.lastWayPointOrRoundTime;
            if (this.hasLastWayPoint) {
                time = time.add(5, 'minute');
            }
            this.form.endTime = time.format();
        },
        refreshWalk: async function () {
            await this.walkStore.fetchById(this.walkId);
        },
        async handleSubmit() {
            const walk = await this.walkStore.epilogue(this.form);
            if (walk) {
                this.alertStore.success(`Die Runde "${walk.name}" wurde erfolgreich erstellt.`, 'Runde erstellt');
                window.scrollTo({
                    top: 0,
                    left: 0,
                    behavior: 'smooth'
                });
                this.$router.push({name: 'Dashboard'});
            } else {
                this.alertStore.error('Runde abschließen fehlgeschlagen', 'Upps! :-(');
            }
        },
    },
};
</script>

<style lang="scss">
</style>
