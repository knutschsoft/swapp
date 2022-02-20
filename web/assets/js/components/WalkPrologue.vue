<template>
    <div>
        <content-collapse
            :title="`Neue Streetwork-Runde`"
            collapse-key="walk-prologue"
            is-visible-by-default
        >
            <div
                v-if="teamId"
                class="p-2"
            >
                <b-form
                    @submit.stop.prevent="onSubmit"
                    class="pt-2 px-2"
                >
                    <form-group
                        :label="`Teilnehmer des Teams &quot;${team.name}&quot;`"
                        description="Wer ist heute mit dabei?"
                    >
                        <b-form-checkbox-group
                            v-model="form.walkTeamMembers"
                            :disabled="isLoading"
                            class="row mt-lg-1 pt-lg-1"
                        >
                            <div
                                v-for="walkTeamMember in team.users"
                                :key="walkTeamMember"
                                class="col-12 col-sm-6 col-md-6 col-lg-4 col-xl-3"
                            >
                                <b-form-checkbox
                                    :value="walkTeamMember"
                                    :disabled="currentUser['@id'] === walkTeamMember"
                                    :data-test="`walkTeamMember-${getUserByIri(walkTeamMember).username}`"
                                >
                                    {{ getUserByIri(walkTeamMember).username }}
                                    <template v-if="currentUser['@id'] === walkTeamMember">(Rundenersteller)</template>
                                </b-form-checkbox>
                            </div>
                        </b-form-checkbox-group>
                    </form-group>

                    <form-group
                        label="Name"
                        :state="nameState"
                        :invalid-feedback="invalidNameFeedback"
                        description=""
                    >
                        <b-form-input
                            v-model="form.name"
                            type="text"
                            :state="nameState"
                            :disabled="isLoading"
                            placeholder="Wie ist der Name der Runde?"
                            data-test="Name"
                            required
                        ></b-form-input>
                    </form-group>

                    <form-group
                        label="Tageskonzept"
                        :state="conceptOfDayState"
                        :invalid-feedback="invalidConceptOfDayFeedback"
                        description=""
                    >
                        <b-form-textarea
                            v-model="form.conceptOfDay"
                            data-test="Tageskonzept"
                            :state="conceptOfDayState"
                            :disabled="isLoading"
                            placeholder=""
                            rows="3"
                            max-rows="15"
                        ></b-form-textarea>
                    </form-group>

                    <form-group
                        label="Rundenstartzeit"
                        description="Die aktuelle Zeit ist vorausgewählt."
                    >
                        <b-datepicker
                            v-model="startTimeDate"
                            v-bind="dateLabels['de']"
                            :disabled="isLoading"
                            :state="startTimeState"
                            data-test="startTimeDate"
                            locale="de"
                        />
                        <b-timepicker
                            v-model="startTimeTime"
                            v-bind="timeLabels['de']"
                            :disabled="isLoading"
                            :state="startTimeState"
                            data-test="startTimeTime"
                            minutes-step="5"
                            class="mt-2"
                            locale="de"
                        />
                    </form-group>

                    <form-group label="Ferien">
                        <b-form-checkbox
                            v-model="form.holidays"
                            :disabled="isLoading"
                            aria-label="Ferien"
                            class="mt-lg-1 pt-lg-1"
                            data-test="Ferien"
                        >
                            ja, es sind Ferien
                        </b-form-checkbox>
                    </form-group>

                    <form-group
                        label="Wetter"
                        :state="weatherState"
                        :invalid-feedback="weatherFeedback"
                    >
                        <b-form-select
                            v-model="form.weather"
                            :disabled="isLoading"
                            :state="weatherState"
                            :options="weatherOptions"
                            data-test="Wetter"
                        />
                    </form-group>

                    <b-button
                        type="submit"
                        variant="secondary"
                        :disabled="isFormInvalid"
                        data-test="button-walk-create"
                        block
                        class="col-12"
                        :tabindex="isFormInvalid ? '-1' : ''"
                    >
                        Runde beginnen
                    </b-button>
                </b-form>
            </div>
        </content-collapse>
    </div>
</template>

<script>
    "use strict";
    import FormGroup from './Common/FormGroup.vue';
    import ContentCollapse from './ContentCollapse.vue';
    import WalkAPI from '../api/walk.js';
    import dayjs from 'dayjs';

    export default {
        name: "WalkPrologue",
        components: {
            FormGroup,
            ContentCollapse,
        },
        props: {
            teamId: {
                required: true,
            }
        },
        data: function () {
            return {
                startTimeTime: null,
                startTimeDate: null,
                form: {
                    name: '',
                    team: null,
                    walkTeamMembers: [],
                    conceptOfDay: '',
                    startTime: dayjs().format(),
                    holidays: false,
                    weather: '',
                },
                walkId: false,
                isFormLoading: false,
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
            }
        },
        computed: {
            isFormInvalid() {
                return (!this.nameState && undefined === this.validationErrors.name)
                    || (!this.conceptOfDayState && undefined === this.validationErrors.conceptOfDay)
                    || (!this.startTimeState && undefined === this.validationErrors.startTime)
                    || !this.walkTeamMembersState
                    || !this.weatherState
                    || this.isLoading;
            },
            nameState() {
                if (!this.form.name) {
                    return null;
                }

                return this.form.name.length >= 1 && undefined === this.validationErrors.name;
            },
            invalidNameFeedback() {
                let message = '';
                ['name'].forEach(key => {
                    if (this.validationErrors[key]) {
                        message += ` ${this.validationErrors[key]}`;
                    }
                });

                return message;
            },
            walkTeamMembersState() {
                if (!this.form.walkTeamMembers || !this.form.walkTeamMembers.length) {
                    return null;
                }

                return undefined === this.validationErrors.walkTeamMembers;
            },
            conceptOfDayState() {
                if (!this.form.conceptOfDay) {
                    return null;
                }

                return this.form.conceptOfDay.length > 0 && undefined === this.validationErrors.conceptOfDay;
            },
            startTimeState() {
                if (null === this.form.startTime || undefined === this.form.startTime) {
                    return;
                }

                return !!this.form.startTime && undefined === this.validationErrors.startTime;
            },
            invalidConceptOfDayFeedback() {
                let message = '';
                ['conceptOfDay'].forEach(key => {
                    if (this.validationErrors[key]) {
                        message += ` ${this.validationErrors[key]}`;
                    }
                });

                return message;
            },
            weatherFeedback() {
                let message = '';
                ['weather'].forEach(key => {
                    if (this.validationErrors[key]) {
                        message += ` ${this.validationErrors[key]}`;
                    }
                });

                return message;
            },
            weatherState() {
                if (this.form.weather === '') {
                    return null;
                }

                return this.weatherOptions.indexOf(this.form.weather) !== -1;
            },
            currentUser() {
                return this.$store.getters["security/currentUser"];
            },
            isLoading() {
                return this.$store.getters["team/isLoading"] || this.$store.getters["walk/isLoadingCreate"];
            },
            team() {
                return this.$store.getters["team/getTeamById"](this.teamId);
            },
            error() {
                return this.$store.getters['walk/errorCreate'];
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
                if (error.data && error.data['hydra:description']) {
                    errors.global = error.data['hydra:description'];
                }

                return errors;
            },
        },
        watch: {
            startTimeTime(startTimeTime) {
                const values = startTimeTime.split(':');
                if (values.length !== 3) {
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
        },
        async created() {
            if (!this.team) {
                await this.$store.dispatch('team/findAll');
            }
            if (!this.team) {
                this.$router.push({ name: 'Dashboard', params: { redirect: 'Dieses Team existiert nicht. Du wurdest auf das Dashboard weitergeleitet.' } });
                return;
            }
            if (!this.team.users.includes(this.currentUser['@id'])) {
                this.$router.push({ name: 'Dashboard', params: { redirect: 'Du kannst für dieses Team keine Runde erstellen, da du kein Mitglied des Teams bist. Du wurdest auf das Dashboard weitergeleitet.' } });
                return;
            }
            this.team.users.forEach((userIri) => {
                if (!this.getUserByIri(userIri)) {
                    this.$store.dispatch('user/findByIri', userIri);
                }
            });
            this.form.walkTeamMembers = await this.getWalkTeamMembersOfLastWalkOfTeam(this.team);
            this.form.team = this.team['@id'];
            this.startTimeTime = dayjs().format('HH:mm');
            this.startTimeDate = dayjs().format('YYYY-MM-DD');
        },
        methods: {
            async getWalkTeamMembersOfLastWalkOfTeam(team) {
                const response = await WalkAPI.findLastWalkByTeam(team);
                const hits = response.data['hydra:totalItems'];
                let result = [];
                if (hits) {
                    response.data['hydra:member'][0].walkTeamMembers.forEach((userIri) => {
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
                return this.$store.getters['user/getUserByIri'](userIri);
            },
            onSubmit: async function () {
                this.isFormLoading = true;

                const walk = await this.$store.dispatch('walk/create', this.form);
                window.scrollTo({
                    top: 0,
                    left: 0,
                    behavior: 'smooth'
                });

                if (walk) {
                    const message = `Die Runde "${walk.name}" wurde erfolgreich erstellt.`;
                    this.$bvToast.toast(message, {
                        title: 'Runde erstellt',
                        toaster: 'b-toaster-top-right',
                        autoHideDelay: 10000,
                        appendToast: true,
                        solid: true,
                    });
                    this.$router.push({ name: 'WalkAddWayPoint', params: { walkId: walk.id } });
                } else {
                    this.$bvToast.toast('Upps! :-(', {
                        title: 'Runde erstellen fehlgeschlagen',
                        toaster: 'b-toaster-top-right',
                        autoHideDelay: 10000,
                        variant: 'danger',
                        appendToast: true,
                        solid: true,
                    });
                }
                this.isFormLoading = false;
            }
        },
    }
</script>

<style scoped>

</style>
