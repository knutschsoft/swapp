<template>
    <div>
        <b-alert
            :show="!!successMessage"
            variant="success"
            class="mt-1 mt-sm-2 mt-lg-3 mb-0"
        >
            <mdicon
                name="check-circle-outline"
                class="mr-2"
            />
            {{ successMessage }}
        </b-alert>
        <content-collapse
            v-if="walk"
            :title="`Runde &quot;${walk?.name}&quot; abschließen`"
            collapse-key="walk-epilogue"
            is-visible-by-default
        >
            <b-form
                @submit.prevent.stop="handleSubmit"
                class="p-1 p-sm-2 p-lg-3"
            >
                <b-form-group
                    content-cols="12"
                    label-cols="12"
                    content-cols-lg="10"
                    label-cols-lg="2"
                    label="Name"
                    description="Der Wert vom Rundenbeginn ist vorausgewählt."
                    :invalid-feedback="invalidNameFeedback"
                    :state="nameState"
                >
                    <b-input-group>
                        <b-input
                            v-model="form.name"
                            required
                            minlength="2"
                            maxlength="300"
                            placeholder="Name"
                            description="Der Wert vom Rundenbeginn ist vorausgewählt."
                            :state="nameState"
                            :disabled="isLoading"
                            data-test="name"
                            autocomplete="off"
                            list="walk-name-list"
                        />
                        <datalist id="walk-name-list">
                            <option v-for="walkName in walkNames">{{ walkName }}</option>
                        </datalist>
                        <b-input-group-append>
                            <b-button
                                @click="form.name = ''"
                                :disabled="form.name === ''"
                            >
                                <mdicon name="CloseCircleOutline" size="20"/>
                            </b-button>
                        </b-input-group-append>
                    </b-input-group>
                </b-form-group>
                <b-form-group
                    content-cols="12"
                    label-cols="12"
                    content-cols-lg="10"
                    label-cols-lg="2"
                    label="Tageskonzept"
                    description="Der Wert vom Rundenbeginn ist vorausgewählt."
                    :invalid-feedback="invalidConceptOfDayFeedback"
                    :state="conceptOfDayState"
                >
                    <b-input-group>
                        <b-form-tags
                            v-model="form.conceptOfDay"
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
                                @click="form.conceptOfDay = []"
                                :disabled="!form.conceptOfDay.length"
                            >
                                <mdicon name="CloseCircleOutline" size="20"/>
                            </b-button>
                        </b-input-group-append>
                    </b-input-group>
                </b-form-group>
                <b-form-group
                    content-cols="12"
                    label-cols="12"
                    content-cols-lg="10"
                    label-cols-lg="2"
                    label="Rundenstartzeit"
                    description="Die Zeit vom Rundenbeginn ist vorausgewählt."
                    :invalid-feedback="invalidStartTimeFeedback"
                    :state="startTimeState"
                >
                    <b-row>
                        <b-col>
                            <b-timepicker
                                v-model="startTimeTime"
                                v-bind="timeLabels['de']"
                                :disabled="isLoading"
                                hide-header
                                :state="startTimeState"
                                data-test="startTimeTime"
                                minutes-step="5"
                                locale="de"
                                size="sm"
                            />
                        </b-col>
                        <b-col>
                            <b-form-datepicker
                                v-model="startTimeDate"
                                v-bind="dateLabels['de']"
                                :disabled="isLoading"
                                :state="startTimeState"
                                data-test="startTimeDate"
                                locale="de"
                                size="sm"
                                right
                            />
                        </b-col>
                    </b-row>
                </b-form-group>
                <b-form-group
                    content-cols="12"
                    label-cols="12"
                    content-cols-lg="10"
                    label-cols-lg="2"
                    label="Rundenendzeit"
                    description="Die aktuelle Zeit ist vorausgewählt."
                    :invalid-feedback="invalidEndTimeFeedback"
                    :state="endTimeState"
                >
                    <b-row>
                        <b-col>
                            <b-timepicker
                                v-model="endTimeTime"
                                v-bind="timeLabels['de']"
                                :disabled="isLoading"
                                hide-header
                                :state="endTimeState"
                                data-test="endTimeTime"
                                minutes-step="5"
                                locale="de"
                                size="sm"
                            />
                        </b-col>
                        <b-col>
                            <b-datepicker
                                v-model="endTimeDate"
                                v-bind="dateLabels['de']"
                                :disabled="isLoading"
                                :state="endTimeState"
                                data-test="endTimeDate"
                                locale="de"
                                size="sm"
                                right
                            />
                        </b-col>
                    </b-row>
                    <div class="mt-2 border-left-0 border-bottom-0 border-right-0 border-secondary border-dashed border-top" />
                    <b-row>
                        <b-col
                            class="mt-2"
                        >
                            <b-button
                                variant="outline-secondary"
                                block
                                size="sm"
                                @click="selectCurrentTime"
                            >
                                Schnellauswahl: aktueller Zeitpunkt
                            </b-button>
                        </b-col>
                        <b-col
                            class="mt-2"
                        >
                            <b-button
                                variant="outline-secondary"
                                block
                                size="sm"
                                @click="selectFiveMinutesAfterLastWayPointOrStartOfWalkTime"
                            >
                                Schnellauswahl: {{ walk.wayPoints.length ? '5 Minuten nach dem letzten Wegpunkt' : 'Rundenbeginn' }}
                            </b-button>
                        </b-col>
                    </b-row>
                    <template v-slot:valid-feedback>
                        <b-alert
                            :show="!!diffLastWayPointOrRound"
                            class="mb-0"
                            variant="warning"
                        >
                            Hinweis: Die gewählte Ankunftszeit ist <b>{{ diffLastWayPointOrRound }}</b> nach dem {{ hasLastWayPoint ? 'letzten Wegpunkt' : 'Rundenstart' }} vom {{ lastWayPointOrRoundTimeAsCalendar }}.
                        </b-alert>
                    </template>
                </b-form-group>
                <b-form-group
                    content-cols="12"
                    label-cols="12"
                    content-cols-lg="10"
                    label-cols-lg="2"
                    label="Ferien"
                    description="Der Wert vom Rundenbeginn ist vorausgewählt."
                >
                    <b-form-checkbox
                        v-model="form.holidays"
                        :disabled="isLoading"
                        data-test="weather"
                    >
                        Sind gerade Ferien?
                    </b-form-checkbox>
                </b-form-group>
                <b-form-group
                    content-cols="12"
                    label-cols="12"
                    content-cols-lg="10"
                    label-cols-lg="2"
                    label="Wetter"
                    description="Der Wert vom Rundenbeginn ist vorausgewählt."
                >
                    <b-form-select
                        v-model="form.weather"
                        :disabled="isLoading"
                        :state="weatherState"
                        :options="weatherOptions"
                        data-test="Wetter"
                    />
                </b-form-group>
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
                <b-form-group
                    content-cols="12"
                    label-cols="12"
                    content-cols-lg="10"
                    label-cols-lg="2"
                    label="Reflexion"
                    description=""
                    :disabled="isLoading"
                    :invalid-feedback="invalidWalkReflectionFeedback"
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
                        v-model="form.walkReflection"
                        minlength="1"
                        maxlength="2500"
                        placeholder="Reflexion"
                        :disabled="isLoading || isWithoutWalkReflection"
                        :state="walkReflectionState"
                        data-test="walkReflection"
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
                <b-form-group
                    content-cols="12"
                    label-cols="12"
                    content-cols-lg="10"
                    label-cols-lg="2"
                    description=""
                    :disabled="isLoading"
                    :invalid-feedback="invalidCommitmentsFeedback"
                    :state="commitmentsState"
                >
                    <template v-slot:label>
                        <div class="d-flex justify-content-between flex-wrap">
                            <div :class="isWithoutCommitments ? `text-muted` : ``">
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
                        v-model="form.commitments"
                        minlength="1"
                        maxlength="2500"
                        placeholder="Termine, Besorgungen, Verabredungen"
                        :disabled="isLoading || isWithoutCommitments"
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
                    :invalid-feedback="invalidInsightsFeedback"
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
                        v-model="form.insights"
                        minlength="1"
                        maxlength="2500"
                        placeholder="Erkenntnisse, Überlegungen, Zielsetzungen"
                        :disabled="isLoading || isWithoutInsights"
                        description=""
                        :state="insightsState"
                        data-test="insights"
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
                <b-button
                    type="submit"
                    variant="secondary"
                    :disabled="isLoading || isSubmitDisabled"
                    data-test="button-walk-submit"
                    block
                    class="col-12"
                >
                    Runde abschließen
                </b-button>
                <global-form-error
                    :error="globalErrors"
                />
            </b-form>
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
import WayPointList from './Walk/WayPointList';
import WalkRating from './Walk/WalkRating';
import dayjs from 'dayjs';
import getViolationsFeedback from '../utils/validation.js';
import { useClientStore } from '../stores/client';
import { useTeamStore } from '../stores/team';
import { useWayPointStore } from '../stores/way-point';
import { useWalkStore } from '../stores/walk';

export default {
    name: 'WalkEpilogue',
    components: {
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
            clientStore: useClientStore(),
            teamStore: useTeamStore(),
            walkStore: useWalkStore(),
            wayPointStore: useWayPointStore(),
            initialConceptOfDay: [],
            initialWalkName: '',
            isWithoutSystemicAnswer: false,
            isWithoutWalkReflection: false,
            isWithoutCommitments: false,
            isWithoutInsights: false,
            startTimeTime: null,
            startTimeDate: null,
            endTimeTime: null,
            endTimeDate: null,
            form: {
                walk: '',
                name: '',
                conceptOfDay: [],
                startTime: dayjs().startOf('minute').format(),
                endTime: dayjs().endOf('minute').format(),
                systemicAnswer: '',
                walkReflection: '',
                rating: 1,
                holidays: false,
                weather: null,
                insights: '',
                commitments: '',
                isResubmission: false,
            },
            weatherOptions: ['', 'Sonne', 'Wolken', 'Regen', 'Schnee', 'Arschkalt'],
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
        walkClient() {
            return this.clientStore.getClientByIri(this.walk.client);
        },
        hasLastWayPoint() {
            return this.walk.wayPoints.length > 0;
        },
        wayPointsOfWalk() {
            let wayPoints = [];
            this.walk.wayPoints.forEach(wayPointIri =>{
                const wayPoint = this.wayPointStore.getWayPointByIri(wayPointIri);
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
                'startTimeBeforeEndTime',
                'weather',
                'systemicAnswer',
                'walkReflection',
                'insights',
                'commitments',
                'endTimeAfterWayPointsVisitedAt',
            ], this.error, true);
        },
        nameState() {
            if (!this.form.name && !this.invalidNameFeedback) {
                return;
            }

            return !this.invalidNameFeedback;
        },
        invalidNameFeedback() {
            return getViolationsFeedback(['name'], this.error);
        },
        conceptOfDayState() {
            if (!this.form.conceptOfDay && !this.invalidConceptOfDayFeedback) {
                return;
            }

            return !this.invalidConceptOfDayFeedback;
        },
        invalidConceptOfDayFeedback() {
            return getViolationsFeedback(['conceptOfDay'], this.error);
        },
        startTimeState() {
            if (null === this.form.startTime || undefined === this.form.startTime) {
                return;
            }

            return !!this.form.startTime && '' === this.invalidStartTimeFeedback;
        },
        invalidStartTimeFeedback() {
            return getViolationsFeedback(['startTime', 'startTimeBeforeEndTime'], this.error);
        },
        endTimeState() {
            if (null === this.form.endTime || undefined === this.form.endTime) {
                return;
            }

            return !!this.form.endTime && '' === this.invalidEndTimeFeedback;
        },
        invalidEndTimeFeedback() {
            return getViolationsFeedback(['endTime', 'startTimeBeforeEndTime', 'endTimeAfterWayPointsVisitedAt'], this.error);
        },
        team() {
            return this.teamStore.getTeamByTeamName(this.walk.teamName);
        },
        conceptOfDaySuggestions() {
            let conceptOfDaySuggestions = [];
            if (!this.team) {
                return conceptOfDaySuggestions;
            }
            conceptOfDaySuggestions = [...new Set(this.initialConceptOfDay), ...new Set(this.team.conceptOfDaySuggestions)];

            return conceptOfDaySuggestions.filter((conceptOfDaySuggestion) => {
                return !this.form.conceptOfDay.includes(conceptOfDaySuggestion);
            });
        },
        walkNames() {
            let walkNames = [];
            if (!this.team) {
                return walkNames;
            }
            walkNames = [this.initialWalkName, ...new Set(this.team.walkNames)];

            return walkNames.filter((walkName) => {
                return walkName.toLowerCase().startsWith(this.form.name.toLowerCase()) && walkName !== this.form.name;
            }).map((walkName) => walkName);
        },
        weatherState() {
            if ('' === this.form.weather) {
                return null;
            }

            return '' === this.invalidWeatherFeedback || this.weatherOptions.indexOf(this.form.weather) !== -1;
        },
        invalidWeatherFeedback() {
            return getViolationsFeedback(['weather'], this.error);
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
        walkReflectionState() {
            if (this.isWithoutWalkReflection) {
                return true;
            }
            if (!this.form.walkReflection && !this.invalidWalkReflectionFeedback) {
                return;
            }

            return !this.invalidWalkReflectionFeedback;
        },
        invalidWalkReflectionFeedback() {
            return getViolationsFeedback(['walkReflection'], this.error);
        },
        insightsState() {
            if (this.isWithoutInsights) {
                return true;
            }
            if (!this.form.insights && !this.invalidInsightsFeedback) {
                return;
            }

            return !this.invalidInsightsFeedback;
        },
        invalidInsightsFeedback() {
            return getViolationsFeedback(['insights'], this.error);
        },
        commitmentsState() {
            if (this.isWithoutCommitments) {
                return true;
            }
            if (!this.form.commitments && !this.invalidCommitmentsFeedback) {
                return;
            }

            return !this.invalidCommitmentsFeedback;
        },
        invalidCommitmentsFeedback() {
            return getViolationsFeedback(['commitments'], this.error);
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
    watch: {
        startTimeTime(startTimeTime) {
            const values = startTimeTime.split(':');
            if (values.length < 2) {
                return;
            }
            let startTime = dayjs(this.form.startTime);
            startTime = startTime.hour(Number(values[0]));
            startTime = startTime.minute(Number(values[1]));
            startTime = startTime.startOf('minute');
            this.form.startTime = startTime.format();
        },
        startTimeDate(startTimeDate) {
            const startTimeDateValue = dayjs(startTimeDate);
            let startTime = dayjs(this.form.startTime);
            startTime = startTime.year(startTimeDateValue.year());
            startTime = startTime.month(startTimeDateValue.month());
            startTime = startTime.date(startTimeDateValue.date());
            startTime = startTime.startOf('minute');
            this.form.startTime = startTime.format();
        },
        endTimeTime(endTimeTime) {
            const values = endTimeTime.split(':');
            if (values.length < 2) {
                return;
            }
            let endTime = dayjs(this.form.endTime);
            endTime = endTime.hour(Number(values[0]));
            endTime = endTime.minute(Number(values[1]));
            this.form.endTime = endTime.format();
        },
        endTimeDate(endTimeDate) {
            const endTimeDateValue = dayjs(endTimeDate);
            let endTime = dayjs(this.form.endTime);
            endTime = endTime.year(endTimeDateValue.year());
            endTime = endTime.month(endTimeDateValue.month());
            endTime = endTime.date(endTimeDateValue.date());
            this.form.endTime = endTime.format();
        },
    },
    async mounted() {
        await this.walkStore.resetChangeError();
        if (!this.walk) {
            await this.refreshWalk();
        }
        if (!this.walk) {
            this.$router.push({ name: 'Dashboard', params: { redirect: 'Diese Runde existiert nicht. Du wurdest auf das Dashboard weitergeleitet.' } });
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
        this.initialConceptOfDay = this.walk.conceptOfDay;
        this.initialWalkName = this.walk.name;
        this.form.name = this.walk.name;
        this.form.conceptOfDay = this.walk.conceptOfDay;
        this.startTimeTime = dayjs(this.walk.startTime).format('HH:mm');
        this.startTimeDate = dayjs(this.walk.startTime).format('YYYY-MM-DD');
        this.endTimeTime = dayjs().format('HH:mm');
        this.endTimeDate = dayjs().format('YYYY-MM-DD');

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
        refreshWalk: async function() {
            await this.walkStore.fetchById(this.walkId);
        },
        async handleSubmit() {
            const walk = await this.walkStore.epilogue(this.form);
            if (walk) {
                const message = `Die Runde "${walk.name}" wurde erfolgreich erstellt.`;
                this.$bvToast.toast(message, {
                    title: 'Runde geändert',
                    toaster: 'b-toaster-top-right',
                    autoHideDelay: 10000,
                    variant: 'success',
                    appendToast: true,
                    solid: true,
                });
                window.scrollTo({
                    top: 0,
                    left: 0,
                    behavior: 'smooth'
                });
                this.$router.push({ name: 'Dashboard' });
            } else {
                this.$bvToast.toast('Upps! :-(', {
                    title: 'Runde abschließen fehlgeschlagen',
                    toaster: 'b-toaster-top-right',
                    autoHideDelay: 10000,
                    variant: 'danger',
                    appendToast: true,
                    solid: true,
                });
            }
        },
    },
};
</script>

<style lang="scss">
.b-form-datepicker.form-control.is-valid, .b-form-timepicker.form-control {
    padding-right: 0 !important;

    label.form-control.is-valid {
        padding-right: 0.25rem !important;
    }
}
</style>
