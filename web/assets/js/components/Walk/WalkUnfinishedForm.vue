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
                :options="weatherOptions"
                data-test="Wetter"
            />
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
import { useTeamStore } from '../../stores/team';
import { useWayPointStore } from '../../stores/way-point';
import { useWalkStore } from '../../stores/walk';

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
        FormGroup,
        FormError,
    },
    data: function () {
        return {
            teamStore: useTeamStore(),
            walkStore: useWalkStore(),
            wayPointStore: useWayPointStore(),
            initialWalkName: '',
            initialConceptOfDay: [],
            startTimeDate: null,
            startTimeTime: null,
            walk: {
                name: null,
                conceptOfDay: [],
                startTime: null,
                holidays: null,
                weather: null,
                walkTeamMembers: [],
                guestNames: [],
            },
            weatherOptions: ['Sonne', 'Wolken', 'Regen', 'Schnee', 'Arschkalt'],
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
                || (!this.nameState && undefined === this.validationErrors.name)
                || (!this.conceptOfDayState && undefined === this.validationErrors.conceptOfDay)
                || (!this.startTimeState && undefined === this.validationErrors.startTime)
                || !this.walkTeamMembersState
                || !this.weatherState
                || this.isLoading;
        },
        team() {
            return this.teamStore.getTeamByTeamName(this.initialWalk.teamName);
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
        walkTeamMembersState() {
            if (!this.walk.walkTeamMembers || !this.walk.walkTeamMembers.length) {
                return null;
            }

            return undefined === this.validationErrors.walkTeamMembers;
        },
        nameState() {
            if (null === this.walk.name || '' === this.walk.name || undefined === this.walk.name) {
                return;
            }

            return this.walk.name.length >= 2 && this.walk.name.length <= 300;
        },
        weatherState() {
            if (this.walk.weather === '') {
                return null;
            }

            return this.weatherOptions.indexOf(this.walk.weather) !== -1;
        },
        conceptOfDayState() {
            if (null === this.walk.conceptOfDay || undefined === this.walk.conceptOfDay) {
                return;
            }

            return this.walk.conceptOfDay.length >= 1;
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
            return this.$store.getters['security/currentUser'];
        },
        users() {
            return this.$store.getters['user/users'];
        },
        isFormInvalid() {
            return !this.nameState
                || !this.conceptOfDayState
                || !this.startTimeState
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
        this.initialConceptOfDay = this.initialWalk.conceptOfDay;
        this.walk.conceptOfDay = this.initialWalk.conceptOfDay;
        this.walk.startTime = this.initialWalk.startTime;
        this.walk.holidays = this.initialWalk.holidays;
        this.walk.weather = this.initialWalk.weather;
        this.walk.walkTeamMembers = this.initialWalk.walkTeamMembers.slice();
        this.walk.guestNames = this.initialWalk.guestNames.slice();

        if (!this.users.length) {
            await this.$store.dispatch('user/findAll');
        }
        if (!this.team) {
            await this.teamStore.fetchTeams();
        }

        this.startTimeTime = dayjs(this.walk.startTime).format('HH:mm');
        this.startTimeDate = dayjs(this.walk.startTime).format('YYYY-MM-DD');
    },
    methods: {
        getWayPointByIri(iri) {
            return this.wayPointStore.getWayPointByIri(iri);
        },
        async handleSubmit() {
            this.$emit('submit', this.walk);
        },
    },
};
</script>

<style scoped lang="scss">
</style>
