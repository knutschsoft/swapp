<template>
    <b-form
        @submit.prevent.stop="handleSubmit"
        class="p-1 p-sm-2 p-lg-3"
    >
        <form-group label="Ort">
            <b-input
                v-model="walk.name"
                :disabled="isLoading"
                required
                minlength="2"
                maxlength="300"
                placeholder="Ort"
                :state="nameState"
                data-test="name"
            />
        </form-group>
        <form-group label="Tageskonzept">
            <b-textarea
                v-model="walk.conceptOfDay"
                :disabled="isLoading"
                minlength="1"
                maxlength="2500"
                placeholder="Tageskonzept"
                :state="conceptOfDayState"
                data-test="conceptOfDay"
                rows="3"
                max-rows="15"
            />
        </form-group>
        <form-group label="Rundenstartzeit">
            <b-datepicker
                v-model="startTimeDate"
                :disabled="isLoading"
                :state="startTimeState"
                data-test="startTimeDate"
            />
            <b-timepicker
                v-model="startTimeTime"
                :disabled="isLoading"
                :state="startTimeState"
                data-test="startTimeTime"
            />
        </form-group>
        <form-group label="Rundenendzeit">
            <b-datepicker
                v-model="endTimeDate"
                :disabled="isLoading"
                :state="endTimeState"
                data-test="endTimeDate"
            />
            <b-timepicker
                v-model="endTimeTime"
                :disabled="isLoading"
                :state="endTimeState"
                data-test="endTimeTime"
                class="mt-2"
            />
        </form-group>
        <form-group label="Ferien">
            <b-form-checkbox
                v-model="walk.holidays"
                :disabled="isLoading"
                class="mt-lg-1 pt-lg-1"
            >
                js, es sind Ferien
            </b-form-checkbox>
        </form-group>
        <form-group label="Wetter">
            <b-form-select
                v-model="walk.weather"
                :disabled="isLoading"
                :options="['Sonne', 'Wolken', 'Regen', 'Schnee', 'Arschkalt']"
            />
        </form-group>
        <form-group label="Systemische Frage">
            <b-form-input
                v-model="walk.systemicQuestion"
                disabled
                readonly
            />
        </form-group>
        <form-group label="Systemische Antwort">
            <b-textarea
                v-model="walk.systemicAnswer"
                :disabled="isLoading"
                minlength="1"
                maxlength="2500"
                placeholder="Systemische Antwort"
                :state="systemicAnswerState"
                data-test="systemicAnswer"
                rows="3"
                max-rows="15"
            />
        </form-group>
        <form-group label="Reflexion">
            <b-textarea
                v-model="walk.walkReflection"
                :disabled="isLoading"
                minlength="1"
                maxlength="2500"
                placeholder="Reflexion"
                :state="walkReflectionState"
                data-test="walkReflection"
                rows="3"
                max-rows="15"
            />
        </form-group>
        <form-group label="Rundenbewertung">
            <b-form-select
                v-model="walk.rating"
                :disabled="isLoading"
                :options="[1, 2, 3, 4, 5]"
            />
        </form-group>
        <form-group label="Termine, Besorgungen, Verabredungen">
            <b-textarea
                v-model="walk.commitments"
                :disabled="isLoading"
                minlength="1"
                maxlength="2500"
                placeholder="Termine, Besorgungen, Verabredungen"
                :state="commitmentsState"
                data-test="commitments"
                rows="3"
                max-rows="15"
            />
        </form-group>
        <form-group label="Erkenntnisse, Überlegungen, Zielsetzungen">
            <b-textarea
                v-model="walk.insights"
                :disabled="isLoading"
                minlength="1"
                maxlength="2500"
                placeholder="Termine, Besorgungen, Verabredungen"
                :state="insightsState"
                data-test="insights"
                rows="3"
                max-rows="15"
            />
        </form-group>
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
            :disabled="isFormInvalid"
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
        FormGroup,
        FormError,
    },
    data: function () {
        return {
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
            },
        };
    },
    computed: {
        nameState() {
            if (null === this.walk.name || '' === this.walk.name || undefined === this.walk.name) {
                return;
            }

            return this.walk.name.length >= 2 && this.walk.name.length <= 300;
        },
        commitmentsState() {
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
            if (null === this.walk.insights || undefined === this.walk.insights) {
                return;
            }

            return this.walk.insights.length >= 1 && this.walk.insights.length <= 2500;
        },
        systemicAnswerState() {
            if (null === this.walk.systemicAnswer || undefined === this.walk.systemicAnswer) {
                return;
            }

            return this.walk.systemicAnswer.length >= 1 && this.walk.systemicAnswer.length <= 2500;
        },
        walkReflectionState() {
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
            if (values.length !== 3) {
                return;
            }
            let startTime = dayjs(this.walk.startTime);
            startTime = startTime.hour(Number(values[0]));
            startTime = startTime.minute(Number(values[1]));
            this.walk.startTime = startTime.toISOString();
        },
        startTimeDate(startTimeDate) {
            const startTimeDateValue = dayjs(startTimeDate);
            let startTime = dayjs(this.walk.startTime);
            startTime = startTime.date(startTimeDateValue.date());
            startTime = startTime.month(startTimeDateValue.month());
            startTime = startTime.year(startTimeDateValue.year());
            this.walk.startTime = startTime.toISOString();
        },
        endTimeTime(endTimeTime) {
            const values = endTimeTime.split(':');
            if (values.length !== 3) {
                return;
            }
            let endTime = dayjs(this.walk.endTime);
            endTime = endTime.hour(Number(values[0]));
            endTime = endTime.minute(Number(values[1]));
            this.walk.endTime = endTime.toISOString();
        },
        endTimeDate(endTimeDate) {
            const endTimeDateValue = dayjs(endTimeDate);
            let endTime = dayjs(this.walk.endTime);
            endTime = endTime.date(endTimeDateValue.date());
            endTime = endTime.month(endTimeDateValue.month());
            endTime = endTime.year(endTimeDateValue.year());
            this.walk.endTime = endTime.toISOString();
        },
    },
    async created() {
        this.walk.name = this.initialWalk.name;
        this.walk.commitments = this.initialWalk.commitments;
        this.walk.conceptOfDay = this.initialWalk.conceptOfDay;
        this.walk.startTime = this.initialWalk.startTime;
        this.walk.endTime = this.initialWalk.endTime;
        this.walk.holidays = this.initialWalk.holidays;
        this.walk.insights = this.initialWalk.insights;
        this.walk.isResubmission = this.initialWalk.isResubmission;
        this.walk.rating = this.initialWalk.rating;
        this.walk.systemicAnswer = this.initialWalk.systemicAnswer;
        this.walk.systemicQuestion = this.initialWalk.systemicQuestion;
        this.walk.walkReflection = this.initialWalk.walkReflection;
        this.walk.weather = this.initialWalk.weather;

        this.startTimeTime = dayjs(this.walk.startTime).format('HH:mm');
        this.startTimeDate = dayjs(this.walk.startTime).format('YYYY-MM-DD');
        this.endTimeTime = dayjs(this.walk.endTime).format('HH:mm');
        this.endTimeDate = dayjs(this.walk.endTime).format('YYYY-MM-DD');
    },
    methods: {
        async handleSubmit() {
            this.$emit('submit', this.walk);
        },
    },
};
</script>

<style scoped lang="scss">
</style>
