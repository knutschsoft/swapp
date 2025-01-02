<template>
    <content-collapse
        :title="`Neue Streetwork-Runde`"
        collapse-key="walk-prologue"
        is-visible-by-default
    >
        <v-form
            ref="form"
            lazy-validation
            @submit.prevent="onSubmit"
        >
            <v-col class="my-2">
                <v-select
                    v-model="form.walkCreator"
                    :items="walkCreatorOptions"
                    :disabled="isLoading"
                    item-value="@id"
                    item-text="username"
                    label="Rundenersteller"
                    required
                    outlined
                    hide-details
                    @change="handleWalkCreatorChange"
                ></v-select>
            </v-col>
            <v-col>
                <walk-team-members-field
                    v-model="form.walkTeamMembers"
                    :users="usersOfTeam"
                    :walk-creator="getUserByIri(form.walkCreator)"
                    :is-loading="isLoading"
                    :label="`Teilnehmende des Teams &quot;${team?.name}&quot;`"
                    description="Wer ist heute mit dabei?"
                />
            </v-col>
            <v-col>
                <v-combobox
                    v-model="form.guestNames"
                    :items="guestNames"
                    chips
                    deletable-chips
                    clearable
                    multiple
                    outlined
                    label="Weitere Teilnehmende"
                    placeholder="Namen eintragen..."
                    :hide-no-data="!guestNameSearch"
                    :search-input.sync="guestNameSearch"
                    :disabled="isLoading"
                    :loading="isLoading"
                    hide-details
                >
                    <template v-slot:no-data>
                        <v-list-item>
                            <v-list-item-content>
                                <v-list-item-title v-if="!guestNameSearch">
                                    Füge "<strong>{{ guestNameSearch }}</strong>" hinzu.
                                </v-list-item-title>
                                <v-list-item-title v-else>
                                    Tippe um zu suchen.
                                </v-list-item-title>
                            </v-list-item-content>
                        </v-list-item>
                    </template>
                </v-combobox>
            </v-col>
            <v-col>
                <v-combobox
                    v-model="form.name"
                    :items="walkNameSuggestions"
                    clearable
                    outlined
                    label="Name"
                    placeholder="Wie ist der Name der Runde?"
                    :disabled="isLoading"
                    :loading="isLoading"
                    no-data-text="Achtung - diese Rundenname ist nicht hinterlegt."
                    hide-details
                    data-test="Name"
                ></v-combobox>
            </v-col>
            <v-col>
                <v-combobox
                    v-model="form.conceptOfDay"
                    :items="conceptOfDaySuggestions"
                    chips
                    deletable-chips
                    clearable
                    outlined
                    multiple
                    label="Tageskonzept"
                    data-test="Tageskonzept"
                    placeholder="Tageskonzept eintragen..."
                    :disabled="isLoading"
                    :loading="isLoading"
                    :hide-no-data="!conceptOfDaySearch"
                    :search-input.sync="conceptOfDaySearch"
                    hide-details
                >
                    <template v-slot:no-data>
                        <v-list-item>
                            <v-list-item-content>
                                <v-list-item-title>
                                    Füge "<strong>{{ conceptOfDaySearch }}</strong>" hinzu.
                                </v-list-item-title>
                            </v-list-item-content>
                        </v-list-item>
                    </template>
                </v-combobox>
            </v-col>
            <v-col>
                Rundenstartzeit<br>
                <date-picker
                    v-model="startTimeDate"
                    label="Rundenstartzeit"
                    :disabled="isLoading"
                    format="DD.MM.YYYY"
                    title-format="DD.MM.YYYY"
                    show-week-number
                    data-test="startTimeDate"
                    :lang="datePickerLang"
                >

                </date-picker>
                <date-picker
                    v-model="startTimeTime"
                    type="time"
                    :disabled="isLoading"
                    data-test="startTimeTime"
                    :minute-step="5"
                    format="HH:mm"
                    title-format="HH:mm"
                    :show-second="false"
                    :lang="datePickerLang"
                >

                </date-picker>
            </v-col>
            <v-col>
                Ferien<br>
                <v-switch
                    v-model="form.holidays"
                    :disabled="isLoading"
                    label="ja, es sind Ferien"
                ></v-switch>
            </v-col>
            <v-col>
                <v-select
                    v-model="form.weather"
                    :items="weatherOptions"
                    label="Wetter"
                    data-test="Wetter"
                    outlined
                    :disabled="isLoading"
                ></v-select>
            </v-col>
            <v-col class="mb-2">
                <v-btn
                    type="submit"
                    :disabled="isFormInvalid"
                    block
                    color="secondary"
                    data-test="btn-Runde beginnen"
                >
                    Runde beginnen
                </v-btn>
            </v-col>
        </v-form>
    </content-collapse>
</template>

<script>
"use strict";
import FormGroup from './Common/FormGroup.vue';
import ContentCollapse from './ContentCollapse.vue';
import WalkAPI from '../api/walk.js';
import dayjs from 'dayjs';
import {useAlertStore, useAuthStore, useTeamStore, useUserStore, useWalkStore} from '../stores';
import WalkTeamMembersField from "./Common/Walk/WalkTeamMembersField.vue";
import DatePicker from 'vue2-datepicker';
import 'vue2-datepicker/index.css';
import 'vue2-datepicker/locale/de';

export default {
    name: "WalkPrologue",
    components: {
        DatePicker,
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
            datePickerLang: {
                formatLocale: {
                    firstDayOfWeek: 1,
                },
                monthBeforeYear: true,
            },
            alertStore: useAlertStore(),
            authStore: useAuthStore(),
            teamStore: useTeamStore(),
            userStore: useUserStore(),
            walkStore: useWalkStore(),
            walkNameSearch: '',
            guestNameSearch: '',
            conceptOfDaySearch: '',
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
        }
    },
    computed: {
        walkNameSuggestions() {
            if (!this.team) {
                return [];
            }
            return this.team.walkNames.map((walkName) => walkName);
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
            users.sort((userA, userB) => (userA.username.toLowerCase() > userB.username.toLowerCase()) ? 1 : -1);

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
            // const values = startTimeTime.split(':');
            // if (values.length !== 3) {
            //     return;
            // }
            // let startTime = dayjs(this.form.startTime);
            // startTime = startTime.hour(Number(values[0]));
            // startTime = startTime.minute(Number(values[1]));
            // startTime = startTime.startOf('minute');
            let startTime = dayjs(startTimeTime);
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
            this.$router.push({name: 'Dashboard', params: {redirect: 'Dieses Team existiert nicht. Du wurdest auf das Dashboard weitergeleitet.'}});
            return;
        }
        if (!this.team.users.includes(this.currentUser['@id'])) {
            this.$router.push({
                name: 'Dashboard',
                params: {redirect: 'Du kannst für dieses Team keine Runde erstellen, da du kein Mitglied des Teams bist. Du wurdest auf das Dashboard weitergeleitet.'}
            });
            return;
        }
        this.team.users.forEach((userIri) => {
            if (!this.getUserByIri(userIri)) {
                this.userStore.fetchByIri(userIri);
            }
        });
        if (this.team.initialMembersConfig === 'mitglieder') {
            this.form.walkTeamMembers = await this.getWalkTeamMembersOfLastWalkOfTeam(this.team);
        } else {
            this.form.walkTeamMembers = [this.currentUser['@id']];
        }
        this.form.team = this.team['@id'];
        this.form.walkCreator = this.currentUser['@id'];
        this.startTimeTime = dayjs().toDate();
        this.startTimeDate = dayjs().toDate();
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
                this.alertStore.success(`Die Runde "${walk.name}" wurde erfolgreich erstellt.`, 'Runde erstellt');
                this.$router.push({name: 'WalkAddWayPoint', params: {walkId: walk.walkId}});
            } else {
                this.alertStore.error('Runde erstellen fehlgeschlagen', 'Upps! :-(');
            }
            this.isFormLoading = false;
        }
    },
}
</script>

<style scoped>

</style>
