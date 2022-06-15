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
            :title="`Runde &quot;${walk.name}&quot; abschließen`"
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
                    <b-input
                        v-model="form.name"
                        type="text"
                        minlength="2"
                        :disabled="isLoading"
                        maxlength="300"
                        placeholder="Name"
                        description="Der Wert vom Rundenbeginn ist vorausgewählt."
                        :state="nameState"
                        data-test="name"
                    />
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
                    <b-textarea
                        v-model="form.conceptOfDay"
                        minlength="1"
                        maxlength="2500"
                        placeholder="Tageskonzept"
                        :disabled="isLoading"
                        description="Der Wert vom Rundenbeginn ist vorausgewählt."
                        :state="conceptOfDayState"
                        data-test="conceptOfDay"
                        rows="3"
                        max-rows="15"
                    />
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
                    content-cols="12"
                    label-cols="12"
                    content-cols-lg="10"
                    label-cols-lg="2"
                    :label="`Systemische Antwort`"
                    :description="walk.systemicQuestion"
                    :disabled="isLoading"
                    :invalid-feedback="invalidSystemicAnswerFeedback"
                    :state="systemicAnswerState"
                >
                    <b-textarea
                        v-model="form.systemicAnswer"
                        minlength="1"
                        maxlength="2500"
                        :placeholder="walk.systemicQuestion"
                        :disabled="isLoading"
                        :state="systemicAnswerState"
                        data-test="systemicAnswer"
                        rows="3"
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
                    <b-textarea
                        v-model="form.walkReflection"
                        minlength="1"
                        maxlength="2500"
                        placeholder="Reflexion"
                        :disabled="isLoading"
                        :state="walkReflectionState"
                        data-test="walkReflection"
                        rows="3"
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
                    <b-form-select
                        v-model="form.rating"
                        :disabled="isLoading"
                        :options="[1, 2, 3, 4, 5]"
                        data-test="rating"
                    />
                </b-form-group>
                <b-form-group
                    content-cols="12"
                    label-cols="12"
                    content-cols-lg="10"
                    label-cols-lg="2"
                    label="Termine, Besorgungen, Verabredungen"
                    description=""
                    :disabled="isLoading"
                    :invalid-feedback="invalidCommitmentsFeedback"
                    :state="commitmentsState"
                >
                    <b-textarea
                        v-model="form.commitments"
                        minlength="1"
                        maxlength="2500"
                        placeholder="Termine, Besorgungen, Verabredungen"
                        :disabled="isLoading"
                        :state="commitmentsState"
                        data-test="commitments"
                        rows="3"
                        max-rows="15"
                    />
                </b-form-group>
                <b-form-group
                    content-cols="12"
                    label-cols="12"
                    content-cols-lg="10"
                    label-cols-lg="2"
                    label="Erkenntnisse, Überlegungen, Zielsetzungen"
                    :invalid-feedback="invalidInsightsFeedback"
                    :state="insightsState"
                >
                    <b-textarea
                        v-model="form.insights"
                        minlength="1"
                        maxlength="2500"
                        placeholder="Erkenntnisse, Überlegungen, Zielsetzungen"
                        :disabled="isLoading"
                        description=""
                        :state="insightsState"
                        data-test="insights"
                        rows="3"
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
                    :disabled="isLoading"
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
    </div>
</template>

<script>
'use strict';
import ContentCollapse from './ContentCollapse.vue';
import GlobalFormError from './Common/GlobalFormError.vue';
import dayjs from 'dayjs';
import getViolationsFeedback from '../utils/validation.js';

export default {
    name: 'WalkEpilogue',
    components: {
        ContentCollapse,
        GlobalFormError,
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
            startTimeTime: null,
            startTimeDate: null,
            endTimeTime: null,
            endTimeDate: null,
            form: {
                walk: '',
                name: '',
                conceptOfDay: '',
                startTime: dayjs().format(),
                endTime: dayjs().format(),
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
            return getViolationsFeedback(['endTime', 'startTimeBeforeEndTime'], this.error);
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
            if (!this.form.systemicAnswer && !this.invalidSystemicAnswerFeedback) {
                return;
            }

            return !this.invalidSystemicAnswerFeedback;
        },
        invalidSystemicAnswerFeedback() {
            return getViolationsFeedback(['systemicAnswer'], this.error);
        },
        walkReflectionState() {
            if (!this.form.walkReflection && !this.invalidWalkReflectionFeedback) {
                return;
            }

            return !this.invalidWalkReflectionFeedback;
        },
        invalidWalkReflectionFeedback() {
            return getViolationsFeedback(['walkReflection'], this.error);
        },
        insightsState() {
            if (!this.form.insights && !this.invalidInsightsFeedback) {
                return;
            }

            return !this.invalidInsightsFeedback;
        },
        invalidInsightsFeedback() {
            return getViolationsFeedback(['insights'], this.error);
        },
        commitmentsState() {
            if (!this.form.commitments && !this.invalidCommitmentsFeedback) {
                return;
            }

            return !this.invalidCommitmentsFeedback;
        },
        invalidCommitmentsFeedback() {
            return getViolationsFeedback(['commitments'], this.error);
        },
        isLoading() {
            return this.$store.getters['walk/isLoading'];
        },
        hasWalks() {
            return this.$store.getters['walk/hasWalks'];
        },
        walks() {
            return this.$store.getters['walk/walks'];
        },
        walk() {
            return this.$store.getters["walk/getWalkById"](this.walkId);
        },
        error() {
            return this.$store.getters["walk/errorChange"];
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
            this.form.startTime = startTime.format();
        },
        startTimeDate(startTimeDate) {
            const startTimeDateValue = dayjs(startTimeDate);
            let startTime = dayjs(this.form.startTime);
            startTime = startTime.date(startTimeDateValue.date());
            startTime = startTime.month(startTimeDateValue.month());
            startTime = startTime.year(startTimeDateValue.year());
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
            endTime = endTime.date(endTimeDateValue.date());
            endTime = endTime.month(endTimeDateValue.month());
            endTime = endTime.year(endTimeDateValue.year());
            this.form.endTime = endTime.format();
        },
    },
    async mounted() {
        await this.$store.dispatch('walk/resetChangeError');
        if (!this.walk) {
            await this.refreshWalk();
        }
        if (!this.walk) {
            this.$router.push({ name: 'Dashboard', params: { redirect: 'Diese Runde existiert nicht. Du wurdest auf das Dashboard weitergeleitet.' } });
        }

        this.form.walk = this.walk['@id'];
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
        refreshWalk: async function() {
            await this.$store.dispatch('walk/findById', this.walkId);
        },
        async handleSubmit() {
            const walk = await this.$store.dispatch('walk/epilogue', this.form);
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
