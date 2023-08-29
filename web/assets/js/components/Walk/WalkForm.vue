<template>
    <b-form
        @submit.prevent.stop="handleSubmit"
        class="p-1 p-sm-2 p-lg-3"
    >
        <form-group
            :label="`Teilnehmende der Runde`"
            description="Wer war mit dabei?"
        >
            <b-form-checkbox-group
                v-model="walk.walkTeamMembers"
                :disabled="isLoading"
                class="row mt-lg-1 pt-lg-1"
            >
                <div
                    v-for="walkTeamMember in users"
                    :key="walkTeamMember['@id']"
                    class="col-12 col-sm-6 col-md-6 col-lg-4 col-xl-3"
                >
                    <b-form-checkbox
                        :value="walkTeamMember['@id']"
                        :data-test="`walkTeamMember-${walkTeamMember.username}`"
                    >
                        {{ walkTeamMember.username }}
                    </b-form-checkbox>
                </div>
            </b-form-checkbox-group>
        </form-group>
        <form-group
            v-if="walk.isWithGuests"
            :label="`Weitere Teilnehmende`"
        >
            <b-form-tags
                v-model="walk.guestNames"
                :disabled="isLoading"
                tag-pills
                placeholder="Namen eintragen..."
                add-button-text="Hinzufügen"
                duplicate-tag-text="Weiterer Teilnehmender ist schon dabei"
                remove-on-delete
                :input-attrs="{ list: 'guest-name-list' }"
                add-on-change
            />
            <b-form-datalist
                id="guest-name-list"
                :options="guestNames"
                autocomplete="off"
            />
        </form-group>
        <form-group label="Name">
            <b-input-group>
                <b-input
                    v-model="walk.name"
                    required
                    minlength="2"
                    maxlength="300"
                    placeholder="Name"
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
                        @click="walk.name = ''"
                        :disabled="walk.name === ''"
                    >
                        <mdicon name="CloseCircleOutline" size="20"/>
                    </b-button>
                </b-input-group-append>
            </b-input-group>
        </form-group>
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
        <form-group label="Rundenendzeit">
            <b-row>
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
                        Schnellauswahl: {{ initialWalk.wayPoints.length ? '5 Minuten nach dem letzten Wegpunkt' : 'Rundenbeginn' }}
                    </b-button>
                </b-col>
            </b-row>
            <template v-slot:valid-feedback>
                <b-alert
                    :show="!!diffLastWayPointOrRound"
                    class="mb-0 mt-2"
                    variant="warning"
                >
                    Hinweis: Die gewählte Ankunftszeit ist <b>{{ diffLastWayPointOrRound }}</b> nach dem {{ hasLastWayPoint ? 'letzten Wegpunkt' : 'Rundenstart' }} vom {{ lastWayPointOrRoundTimeAsCalendar }}.
                </b-alert>
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
        <form-group label="Wetter">
            <b-form-select
                v-model="walk.weather"
                :disabled="isLoading"
                data-test="Wetter"
                :options="['Sonne', 'Wolken', 'Regen', 'Schnee', 'Arschkalt']"
            />
        </form-group>
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
        <b-button
            type="submit"
            variant="secondary"
            :disabled="isFormInvalid || isSubmitDisabled"
            data-test="button-walk-submit"
            block
            class="col-12"
            :tabindex="isFormInvalid ? '-1' : ''"
        >
            {{ submitButtonText }}
        </b-button>
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
import { StarRating } from 'vue-rate-it';
import WalkRating from './WalkRating.vue';
import { useClientStore } from '../../stores/client';

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
        WalkRating,
        FormGroup,
        FormError,
        StarRating,
    },
    data: function () {
        return {
            clientStore: useClientStore(),
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
                weather: null,
                walkTeamMembers: [],
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
        walkClient() {
            return this.clientStore.getClientByIri(this.initialWalk.client || this.currentUser.client);
        },
        hasLastWayPoint() {
            return this.initialWalk.wayPoints.length > 0;
        },
        lastWayPointOrRoundTime() {
            let time = false;
            this.initialWalk.wayPoints
                .slice()
                .sort((a, b) => {
                        if (dayjs(this.getWayPointByIri(a).visitedAt).isAfter(dayjs(this.getWayPointByIri(b).visitedAt))) {
                            return -1;
                        }
                        return 1;
                    },
                )
                .every(wayPointIri => {
                const wayPoint = this.getWayPointByIri(wayPointIri);
                if (false === wayPoint) {
                    return true;
                }
                time = dayjs(wayPoint.visitedAt);

                return false;
            });

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
            return this.$store.getters['team/getTeamByTeamName'](this.initialWalk.teamName);
        },
        walkNames() {
            let walkNames = [];
            if (!this.team) {
                return walkNames;
            }
            walkNames = [this.initialWalkName, ...new Set(this.team.walkNames)];

            return walkNames.filter((walkName) => {
                return walkName.toLowerCase().startsWith(this.walk.name.toLowerCase()) && walkName !== this.walk.name;
            }).map((walkName) => walkName);
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
        guestNames() {
            let guestNames = [];
            if (!this.team || !this.initialWalk.isWithGuests) {
                return guestNames;
            }
            guestNames = [...new Set(this.initialWalk.guestNames.concat(this.team.guestNames))];

            return guestNames.filter((guestName) => {
                return !this.walk.guestNames.includes(guestName);
            });
        },
        nameState() {
            if (null === this.walk.name || '' === this.walk.name || undefined === this.walk.name) {
                return;
            }

            return this.walk.name.length >= 2 && this.walk.name.length <= 300;
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
            return this.$store.getters['walk/isLoadingChange'];
        },
        currentUser() {
            return this.$store.getters['security/currentUser'];
        },
        users() {
            return this.$store.getters['user/users'];
        },
        isSuperAdmin() {
            return this.$store.getters['security/isSuperAdmin'];
        },
        isFormInvalid() {
            return !this.nameState
                || !this.commitmentsState
                || !this.conceptOfDayState
                || !this.insightsState
                || !this.startTimeState
                || !this.endTimeState
                || !this.systemicAnswerState
                || !this.walkReflectionState
                || this.isLoading;
        },
        error() {
            return this.$store.getters['walk/errorChange'];
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
        this.walk.guestNames = this.initialWalk.guestNames.slice();

        this.isWithoutSystemicAnswer = !this.walk.systemicAnswer.length;
        this.isWithoutWalkReflection = !this.walk.walkReflection.length;
        this.isWithoutCommitments = !this.walk.commitments.length;
        this.isWithoutInsights = !this.walk.insights.length;

        if (!this.users.length) {
            await this.$store.dispatch('user/findAll');
        }
        if (!this.team) {
            await this.$store.dispatch('team/findAll');
        }

        this.startTimeTime = dayjs(this.walk.startTime).format('HH:mm');
        this.startTimeDate = dayjs(this.walk.startTime).format('YYYY-MM-DD');
        this.endTimeTime = dayjs(this.walk.endTime).format('HH:mm');
        this.endTimeDate = dayjs(this.walk.endTime).format('YYYY-MM-DD');
    },
    methods: {
        getWayPointByIri(iri) {
            return this.$store.getters['wayPoint/getWayPointByIri'](iri);
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
