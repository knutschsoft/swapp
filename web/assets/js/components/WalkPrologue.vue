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
                        :label="`Rundenersteller`"
                        :description="`Du bist vorausgewählt.`"
                        :state="walkCreatorState"
                        :invalid-feedback="walkCreatorFeedback"
                        col-md="6"
                    >
                        <b-form-select
                            v-model="form.walkCreator"
                            :disabled="isLoading"
                            :state="walkCreatorState"
                            :options="walkCreatorOptions"
                            data-test="Rundenersteller"
                            value-field="@id"
                            text-field="username"
                            required
                            @change="handleWalkCreatorChange"
                        />
                    </form-group>
                    <walk-team-members-field
                        v-model="form.walkTeamMembers"
                        :users="usersOfTeam"
                        :walk-creator="getUserByIri(form.walkCreator)"
                        :is-loading="isLoading"
                        :label="`Teilnehmende des Teams &quot;${team?.name}&quot;`"
                        description="Wer ist heute mit dabei?"
                    />
                    <form-group
                        v-if="team?.isWithGuests"
                        :label="`Weitere Teilnehmende`"
                    >
                        <b-form-tags
                            v-model="form.guestNames"
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
                    <form-group
                        label="Name"
                        :state="nameState"
                        :invalid-feedback="invalidNameFeedback"
                        description=""
                    >
                        <b-input-group>
                            <b-input
                                v-model="form.name"
                                required
                                maxlength="300"
                                placeholder="Wie ist der Name der Runde?"
                                :state="nameState"
                                :disabled="isLoading"
                                data-test="Name"
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
                    </form-group>

                    <form-group
                        label="Tageskonzept"
                        :state="conceptOfDayState"
                        :invalid-feedback="invalidConceptOfDayFeedback"
                        description=""
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
                            />
                            <datalist id="concept-of-day-list"
                              data-test="Tageskonzept2"
                            >
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
    import { useTeamStore } from '../stores/team';
    import { useWalkStore } from '../stores/walk';
    import { useUserStore } from '../stores/user';
    import { useAuthStore } from '../stores/auth';
    import WalkTeamMembersField from "./Common/Walk/WalkTeamMembersField.vue";

    export default {
        name: "WalkPrologue",
        components: {
            WalkTeamMembersField,
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
                authStore: useAuthStore(),
                teamStore: useTeamStore(),
                userStore: useUserStore(),
                walkStore: useWalkStore(),
                startTimeTime: null,
                startTimeDate: null,
                form: {
                    name: '',
                    team: null,
                    walkTeamMembers: [],
                    guestNames: [],
                    conceptOfDay: [],
                    startTime: dayjs().startOf('minute').format(),
                    holidays: false,
                    weather: '',
                    walkCreator: '',
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
            walkNames() {
                if (!this.team) {
                    return [];
                }
                return this.team.walkNames.filter((walkName) => {
                    return walkName.toLowerCase().startsWith(this.form.name.toLowerCase()) && walkName !== this.form.name;
                }).map((walkName) => walkName);
            },
            usersOfTeam() {
                if (!this.team) {
                    return [];
                }

                let users = [];
                this.team.users.forEach(userIri => {
                    const user = this.getUserByIri(userIri);
                    if (user) {
                        users.push(user);
                    }
                });
                users.sort((userA, userB) => (userA.username.toLowerCase() > userB.username.toLowerCase()) ? 1 : - 1 );

                return users;
            },
            isFormInvalid() {
                return (!this.nameState && undefined === this.validationErrors.name)
                    || (!this.conceptOfDayState && undefined === this.validationErrors.conceptOfDay)
                    || (!this.startTimeState && undefined === this.validationErrors.startTime)
                    || !this.walkTeamMembersState
                    || !this.walkCreatorState
                    || !this.weatherState
                    || this.isLoading;
            },
            nameState() {
                if (!this.form.name) {
                    return null;
                }

                return this.form.name.length >= 1 && undefined === this.validationErrors.name;
            },
            guestNames() {
                if (!this.team || !this.team.isWithGuests) {
                    return [];
                }

                return this.team.guestNames.filter((guestName) => {
                    return !this.form.guestNames.includes(guestName);
                });
            },
            conceptOfDaySuggestions() {
                if (!this.team) {
                    return [];
                }

                return this.team.conceptOfDaySuggestions.filter((conceptOfDaySuggestion) => {
                    return !this.form.conceptOfDay.includes(conceptOfDaySuggestion);
                });
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
            walkCreatorOptions() {
                if (!this.team) {
                    return [];
                }

                return this.team.users
                    .map(userIri => this.getUserByIri(userIri))
                    .filter(user => undefined !== user)
                    ;
            },
            walkCreatorFeedback() {
                let message = '';
                ['walkCreator'].forEach(key => {
                    if (this.validationErrors[key]) {
                        message += ` ${this.validationErrors[key]}`;
                    }
                });

                return message;
            },
            walkCreatorState() {
                if (this.form.walkCreator === '') {
                    return null;
                }

                return !this.walkCreatorOptions.some(user => this.form.walkCreator === user['id']);
            },
            currentUser() {
                return this.authStore.currentUser;
            },
            isLoading() {
                return this.teamStore.isLoading || this.userStore.isLoading || this.walkStore.isLoadingCreate;
            },
            team() {
                return this.teamStore.getTeamById(this.teamId);
            },
            error() {
                return this.walkStore.getErrors.create;
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
        },
        async mounted() {
            if (!this.team) {
                await this.teamStore.fetchTeams();
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
                    this.userStore.fetchByIri(userIri);
                }
            });
            this.form.walkTeamMembers = await this.getWalkTeamMembersOfLastWalkOfTeam(this.team);
            this.form.team = this.team['@id'];
            this.form.walkCreator = this.currentUser['@id'];
            this.startTimeTime = dayjs().format('HH:mm');
            this.startTimeDate = dayjs().format('YYYY-MM-DD');
        },
        methods: {
            handleWalkCreatorChange(newWalkCreator) {
                if (!this.form.walkTeamMembers.includes(newWalkCreator)) {
                    this.form.walkTeamMembers.push(newWalkCreator)
                }
            },
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
                return this.userStore.getUserByIri(userIri);
            },
            onSubmit: async function () {
                this.isFormLoading = true;

                const walk = await this.walkStore.create(this.form);
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
                    this.$router.push({ name: 'WalkAddWayPoint', params: { walkId: walk.walkId } });
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
