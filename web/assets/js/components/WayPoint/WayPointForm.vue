<template>
    <v-form
        v-if="walk && !isLoading"
        @submit.prevent="handleSubmit"
        class="pa-1 pa-sm-2 pa-md-4 pa-lg-5 pa-xl-6 pa-xxl-7"
    >
        <way-point-location-name-field
            v-model="wayPoint.locationName"
            :walk="walk"
            :initial-way-point="initialWayPoint"
            :team="team"
            :is-loading="isLoading"
            :error="error"
        />
        <v-row class="my-1" dense>
            <v-col cols="12" md="6">
                <way-point-visited-at-field
                    v-model="wayPoint.visitedAt"
                    :initial-way-point="initialWayPoint"
                    :is-loading="isLoading"
                    :error="error"
                />
            </v-col>
            <v-divider vertical />
            <v-col>
                <div class="d-none d-md-block">&nbsp;</div>
                <v-btn
                    block
                    color="secondary"
                    variant="outlined"
                    @click="selectCurrentTime"
                    class="mb-2"
                >
                    Schnellauswahl: aktueller Zeitpunkt
                </v-btn>
                <v-btn
                    color="secondary"
                    block
                    variant="outlined"
                    @click="selectFiveMinutesAfterLastWayPointOrStartOfWalkTime"
                    class="m-2"
                >
                    Schnellauswahl: {{ walk.wayPoints.length ? '5 Minuten nach dem letzten Wegpunkt' : 'Rundenbeginn' }}
                </v-btn>
            </v-col>
        </v-row>
        <v-alert
            v-if="!!diffLastWayPointOrRound"
            class="mb-1"
            color="warning"
            variant="tonal"
        >
            Hinweis: Die gewählte Ankunftszeit ist <b>{{ diffLastWayPointOrRound }}</b> nach dem {{ hasLastWayPoint ? 'letzten Wegpunkt' : 'Rundenstart' }} vom {{ lastWayPointOrRoundTimeAsCalendar }}.
        </v-alert>
        <v-alert
            v-if="visitedAtState === false"
            class="mb-0"
            variant="tonal"
            color="warning"
        >
            {{ visitedAtDescription }}
        </v-alert>
        <v-alert
            v-if="isShowWalkStartTimeButton"
            class="mt-1"
            variant="tonal"
            color="warning"
        >
            Hinweis: Die gewählte Ankunftszeit ist <b>{{ diffWalkStartTime }}</b> vor dem Rundenstart. Hier kannst du die Rundenstartzeit auf die aktuell gewählte Ankunftszeit ändern.
            <div class="bg-white">
                <v-btn
                    color="secondary"
                    variant="outlined"
                    block
                    size="small"
                    class="mt-2"
                    data-test="button-set-walk-start-time"
                    @click="handleSetWalkStartTime"
                >
                    {{ setWalkStartTimeButtonLabel }}
                </v-btn>
            </div>
        </v-alert>
        <template v-if="walk.isWithAgeRanges || walk.isWithPeopleCount">
            <div class="mb-2">
                <b v-text="walk.isWithAgeRanges ? `Altersgruppen` : `Anzahl der Personen vor Ort`" />
                <br v-if="walk.isWithAgeRanges">
                <small
                    v-if="walk.isWithAgeRanges"
                    class="font-weight-normal text-muted"
                >
                    Anzahl der Personen vor Ort
                </small>
            </div>
            <v-row
                v-if="walk.isWithAgeRanges && wayPoint"
                v-for="(ageGroup, index) in wayPoint.ageGroups"
                :key="ageGroup.frontendLabel"
                dense
            >
                <template
                    v-for="colIndex in 3"
                    :key="wayPoint.ageGroups[index].frontendLabel + colIndex"
                >
                    <v-col
                        v-if="index % 3 === 0"
                        cols="4"
                        sm="4"
                        md="4"
                        class="mb-1"
                    >
                        <v-select
                            v-model="wayPoint.ageGroups[index + colIndex - 1].peopleCount.count"
                            :label="wayPoint.ageGroups[index + colIndex - 1].frontendLabel"
                            :items="ageRangeOptions"
                            :disabled="isLoading"
                            :help="wayPoint.ageGroups[index + colIndex - 1].frontendLabel"
                            :data-test="wayPoint.ageGroups[index + colIndex - 1].frontendLabel"
                            density="compact"
                            variant="outlined"
                            hide-details
                            :clearable="wayPoint.ageGroups[index + colIndex - 1].peopleCount.count !== 0"
                            persistent-clear
                            class=""
                            @click:clear="wayPoint.ageGroups[index + colIndex - 1].peopleCount.count = 0"
                        />
                    </v-col>
                </template>
            </v-row>
            <v-text-field
                v-if="walk.isWithAgeRanges"
                v-model="sumPeopleCount"
                type="text"
                data-test="sumPeopleCount"
                disabled
                readonly
                density="compact"
                persistent-hint
                variant="outlined"
                background-color="grey lighten-5"
                label="Anzahl Personen vor Ort"
                hint="Ergibt sich automatisch aus der Summe der Altersgruppen."
                class="mt-2"
            />
            <v-select
                v-else
                v-model="wayPoint.peopleCount"
                :items="ageRangeOptions"
                :disabled="isLoading"
                data-test="peopleCount"
                class=""
                variant="outlined"
                density="compact"
                label="Anzahl Personen vor Ort"
            />
        </template>
        <div
            v-if="walk.isWithUserGroups"
            class="mb-4"
        >
            <b>Personenanzahl von Nutzergruppen</b>
        </div>
        <v-row
            v-if="walk.isWithUserGroups"
            class="d-flex align-items-end"
            dense
        >
            <v-col
                v-for="(userGroup, index) in wayPoint.userGroups"
                :key="userGroup.userGroupName.name"
            >
                <v-select
                    v-model="userGroup.peopleCount.count"
                    :items="ageRangeOptions"
                    :disabled="isLoading"
                    :help="userGroup.userGroupName.name"
                    density="compact"
                    variant="outlined"
                    :label="userGroup.userGroupName.name"
                    @click:clear="userGroup.peopleCount.count = 0"
                />
            </v-col>
        </v-row>
        <div
            v-if="walk.isWithConsumables"
            class="mb-4"
        >
            <b>Ausgabematerialien</b>
        </div>
        <v-row
            v-if="walk.isWithConsumables"
            class="d-flex align-items-end"
            dense
        >
            <v-col
                v-for="(consumable, index) in wayPoint.consumables"
                :key="consumable.consumableName.name"
            >
                <v-select
                    v-model="consumable.peopleCount.count"
                    :items="ageRangeOptions"
                    :disabled="isLoading"
                    :help="consumable.consumableName.name"
                    density="compact"
                    variant="outlined"
                    :label="consumable.consumableName.name"
                    @click:clear="consumable.peopleCount.count = 0"
                />
            </v-col>
        </v-row>
        <div
            v-if="walk.isWithCounselings"
            class="mb-4"
        >
            <b>Beratungen</b>
        </div>
        <v-row
            v-if="walk.isWithCounselings"
            class="d-flex align-items-end"
            dense
        >
            <v-col
                v-for="(counseling, index) in wayPoint.counselings"
                :key="counseling.counselingName.name"
            >
                <v-select
                    v-model="counseling.peopleCount.count"
                    :items="ageRangeOptions"
                    :disabled="isLoading"
                    :help="counseling.counselingName.name"
                    density="compact"
                    variant="outlined"
                    :label="counseling.counselingName.name"
                    @click:clear="counseling.peopleCount.count = 0"
                />
            </v-col>
        </v-row>
        <div
            v-if="walk.isWithMedicals"
            class="mb-4"
        >
            <b>Medizin</b>
        </div>
        <v-row
            v-if="walk.isWithMedicals"
            class="d-flex align-items-end"
            dense
        >
            <v-col
                v-for="(medical, index) in wayPoint.medicals"
                :key="medical.medicalName.name"
            >
                <v-select
                    v-model="medical.peopleCount.count"
                    :items="ageRangeOptions"
                    :disabled="isLoading"
                    :help="medical.medicalName.name"
                    density="compact"
                    variant="outlined"
                    :label="medical.medicalName.name"
                    @click:clear="medical.peopleCount.count = 0"
                />
            </v-col>
        </v-row>
        <way-point-contacts-count-field
            v-if="walk.isWithContactsCount"
            v-model="wayPoint.contactsCount"
            label="Anzahl direkter Kontakte"
            hint="Mit wie viel Personen wurde gesprochen?"
        />
        <v-row class="my-2">
            <v-col cols="12" sm="6">
                <v-file-input
                    v-model="file"
                    accept="image/*"
                    data-test="Bildupload"
                    placeholder="kein Bild gewählt"
                    label="Bildupload"
                    density="compact"
                    hide-details
                    variant="outlined"
                    :disabled="isLoading"
                />
                <v-alert
                    v-if="invalidImageFeedback"
                    type="error"
                    class="mt-2"
                >{{invalidImageFeedback}}</v-alert>
            </v-col>
            <v-col v-if="wayPoint.imageFileData" cols="12" sm="6">
                <div
                    class="position-relative"
                    style="max-width: 50px;"
                >
                    <div class="d-flex align-center mb-4 justify-center">
                        <v-img :src="wayPoint.imageFileData" alt="Rating-Bild" width="50" height="50" />
                        <v-btn class="align-self-start ml-n4 mt-n4" size="35" icon @click="wayPoint.imageFileData = wayPoint.imageFileName = wayPoint.imageName = null">
                            <v-icon>mdi-close-circle</v-icon>
                        </v-btn>
                    </div>
                </div>
            </v-col>
        </v-row>
        <textarea-field
            v-model="wayPoint.note"
            label="Beobachtung"
            data-test="note"
            :violation-fields="['note']"
            :error="error"
            :isLoading="isLoading"
        />
        <textarea-field
            v-model="wayPoint.oneOnOneInterview"
            label="Einzelgespräch"
            data-test="oneOnOneInterview"
            :violation-fields="['oneOnOneInterview']"
            :error="error"
            :isLoading="isLoading"
        />
        <v-row class="my-2" dense>
            <v-col cols="12" class="font-weight-bold pb-0 mb-0 mt-2">Tags</v-col>
            <v-col
                v-for="tag in tags"
                cols="12"
                sm="6"
                md="6"
                lg="4"
                xl="3"
            >
                <div
                    :key="tag['@id']"
                    :class="{'d-none': !tag.isEnabled}"
                >
                    <v-checkbox
                        v-model="wayPoint.wayPointTags"
                        :value="tag['@id']"
                        :disabled="isLoading"
                        density="compact"
                        hide-details
                    >
                        <template v-slot:label>
                            {{ tag.name }}
                            <color-badge
                                :color="tag.color"
                                class="ml-2"
                            />
                        </template>
                    </v-checkbox>
                </div>
            </v-col>
            <v-col v-if="hasDisabledTag" cols="12" tag="hr"></v-col>
            <v-col
                v-for="tag in disabledTags"
                :key="tag['@id']"
                cols="12"
                sm="6"
                md="6"
                lg="4"
                xl="3"
                :class="!(initialWayPoint && initialWayPoint.wayPointTags.includes(tag['@id'])) ? 'd-none' : ''"
                class="my-0"
            >
                <v-checkbox
                    v-model="wayPoint.wayPointTags"
                    :value="tag['@id']"
                    :disabled="isLoading"
                    persistent-hint
                    hint="(deaktivierter Tag)"
                    density="compact"
                >
                    <template v-slot:label>
                        {{ tag.name }}
                        <color-badge
                            :color="tag.color"
                            class="ml-2 mr-2"
                        />
                    </template>
                </v-checkbox>
            </v-col>
        </v-row>
        <switch-field
            v-model="wayPoint.isMeeting"
            caption="Mobiler Treff?"
            label="mobiler Treff"
            data-test="isMeeting"
            :violation-fields="['isMeeting']"
            :error="error"
            :isLoading="isLoading"
        />
        <v-btn
            color="secondary"
            type="submit"
            :disabled="isSubmitDisabled"
            data-test="button-way-point-submit"
            block
            class="mb-2 text-transform-none"
            :tabindex="isSubmitDisabled ? '-1' : ''"
        >
            {{ submitButtonText }}
        </v-btn>
        <v-btn
            v-if="initialWalk"
            class="text-transform-none"
            color="secondary"
            :disabled="isSubmitDisabled"
            data-test="button-way-point-submit-and-finish"
            data-with-finish
            @click="handleSubmitWithFinish"
            block
            :tabindex="isSubmitDisabled ? '-1' : ''"
        >
            Wegpunkt speichern und Runde abschließen
        </v-btn>
        <global-form-error
            :error="globalErrors"
        />
    </v-form>
</template>

<script>
'use strict';
import GlobalFormError from '../Common/GlobalFormError.vue';
import ColorBadge from '../Tags/ColorBadge.vue';
import { getViolationsFeedback } from '../../utils';
import axios from 'axios';
import dayjs from 'dayjs';
import {useAlertStore, useAuthStore, useTagStore, useTeamStore, useWalkStore, useWayPointStore} from '@/js/stores';
import {WayPointContactsCountField, WayPointLocationNameField, WayPointVisitedAtField} from "../Common/WayPoint";
import {SwitchField, TextareaField} from "../Common";

export default {
    name: 'WayPointForm',
    props: {
        initialWayPoint: {
            type: Object,
            required: false,
            default: () => {},
        },
        initialWalk: {
            type: Object,
            required: false,
            default: () => {},
        },
        submitButtonText: {
            type: String,
            required: true,
        },
    },
    components: {
        WayPointContactsCountField,
        SwitchField,
        TextareaField,
        WayPointVisitedAtField,
        WayPointLocationNameField,
        ColorBadge,
        GlobalFormError,
    },
    data: function () {
        return {
            alertStore: useAlertStore(),
            authStore: useAuthStore(),
            tagStore: useTagStore(),
            teamStore: useTeamStore(),
            walkStore: useWalkStore(),
            wayPointStore: useWayPointStore(),
            wayPoint: {
                locationName: '',
                visitedAt: dayjs().format(),
                ageGroups: [],
                peopleCount: 0,
                userGroups: [],
                consumables: [],
                counselings: [],
                medicals: [],
                imageName: null,
                isMeeting: false,
                note: '',
                oneOnOneInterview: '',
                wayPointTags: [],
                imageFileData: null,
                imageFileName: null,
                contactsCount: 0,
            },
            file: null,
            ageRangeOptions: Array.from(Array(51), (x, i) => {
                let start = 30;
                let stepSize = 5;
                let value = i;
                if (value > start) {
                    value = start + stepSize * (i - start);
                }
                start = 34;
                stepSize = 5;
                if (i > start) {
                    value = value + stepSize * (i - start);
                }
                start = 39;
                stepSize = 15;
                if (i > start) {
                    value = value + stepSize * (i - start);
                }
                start = 45;
                stepSize = 25;
                if (i > start) {
                    value = value + stepSize * (i - start);
                }
                return value;
            }),
            userGroupOptions: Array.from(Array(21), (x, i) => i),
        };
    },
    computed: {
        error() {
            return this.wayPointStore.getErrors.change || this.wayPointStore.getErrors.create || {};
        },
        sumPeopleCount() {
            let sumPeopleCount = 0;
            this.wayPoint.ageGroups.forEach(ageGroup => sumPeopleCount += ageGroup.peopleCount.count);

            return sumPeopleCount;
        },
        team() {
            return this.teamStore.getTeamByTeamName(this.walk.teamName);
        },
        walk() {
            return this.initialWalk ? this.initialWalk : this.walkStore.getWalkByIri(this.initialWayPoint.walk);
        },
        visitedAtState() {
            if (null === this.wayPoint.visitedAt || undefined === this.wayPoint.visitedAt) {
                return;
            }
            if (!this.walk) {
                return;
            }

            const isAfterStart = dayjs(this.wayPoint.visitedAt).diff(dayjs(this.walk.startTime), 'minute') >= 0;

            if (this.walk.isUnfinished) {
                return isAfterStart;
            }

            const isBeforeEnd = dayjs(this.wayPoint.visitedAt).diff(dayjs(this.walk.endTime), 'minute') < 0;

            return isBeforeEnd && isAfterStart;
        },
        isFirstWayPoint() {
            return 0 === this.walk.wayPoints.length;
        },
        isShowWalkStartTimeButton() {
            return this.isFirstWayPoint && dayjs(this.walk.startTime).isAfter(this.wayPoint.visitedAt);
        },
        diffWalkStartTime() {
            return dayjs(this.wayPoint.visitedAt).to(this.walk.startTime, true);
        },
        setWalkStartTimeButtonLabel() {
            return `Rundenbeginn auf "${ dayjs(this.wayPoint.visitedAt).format('dddd DD.MM.YYYY [um] HH:mm') }" setzen`;
        },
        hasLastWayPoint() {
            let hasLastWayPoint = false;
            this.walk.wayPoints.slice().reverse().every(wayPointIri => {
                if (!this.initialWayPoint || this.initialWayPoint['@id'] !== wayPointIri) {
                    hasLastWayPoint = true;

                    return false;
                }
            });

            return hasLastWayPoint;
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
                    if (!this.initialWayPoint || this.initialWayPoint['@id'] !== wayPoint['@id']) {
                        time = dayjs(wayPoint.visitedAt);

                        return false;
                    }
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
            const diff = dayjs(this.wayPoint.visitedAt).diff(this.lastWayPointOrRoundTime, 'minute');
            if (diff > 240) { // 4 hours
                return dayjs(this.wayPoint.visitedAt).to(this.lastWayPointOrRoundTime, true);
            }

            return false;
        },
        visitedAtDescription() {
            if (this.walk.isUnfinished) {
                return `Die Ankunftszeit muss nach der Rundenstartzeit (${dayjs(this.walk.startTime).format('HH:mm')} Uhr am ${dayjs(this.walk.startTime).format('DD.MM.YYYY')}) liegen.`;
            }

            return `Die Ankunftszeit muss nach der Rundenstartzeit (${dayjs(this.walk.startTime).format('HH:mm')} Uhr am ${dayjs(this.walk.startTime).format('DD.MM.YYYY')}) und vor der Rundenendzeit (${dayjs(this.walk.endTime).format('HH:mm')} Uhr am ${dayjs(this.walk.endTime).format('DD.MM.YYYY')}) liegen.`;
        },
        invalidImageFeedback() {
            return getViolationsFeedback(['decodedImageData', 'imageFileData', 'imageFileName'], this.error);
        },
        isLoading() {
            return this.wayPointStore.isLoading
                || this.walkStore.isLoading
                || this.tagStore.isLoading
                || this.teamStore.isLoading;
        },
        currentUser() {
            return this.authStore.currentUser;
        },
        isSuperAdmin() {
            return this.authStore.isSuperAdmin;
        },
        isSubmitDisabled() {
            return this.isLoading || !this.visitedAtState || !this.wayPoint.locationName;
        },
        globalErrors() {
            let keys = ['oneOnOneInterview', 'note', 'locationName', 'decodedImageData', 'imageFileData', 'imageFileName'];

            if (this.walk.isWithContactsCount) {
                keys.push('contactsCount');
            }

            return getViolationsFeedback(keys, this.error, true);
        },
        hasDisabledTag() {
            if (!this.initialWayPoint) {
                return false;
            }

            return this.initialWayPoint.wayPointTags.find(tagIri => !this.getTagByIri(tagIri)?.isEnabled);
        },
        tags() {
            return this.tagStore.getTags
                .slice()
                .sort((tagA, tagB) => tagA.name > tagB.name ? 1 : -1)
                .filter(tag => tag.isEnabled);
        },
        disabledTags() {
            return this.tagStore.getTags
                .slice()
                .sort((tagA, tagB) => tagA.name > tagB.name ? 1 : -1)
                .filter(tag => !tag.isEnabled);
        },
        ageGroups() {
            let ageGroups = [];
            this.walk.ageRanges
                .slice()
                .sort((a, b) => a.rangeStart - b.rangeStart)
                .forEach((ageRange) => {
                    ageGroups.push({
                        ageRange: ageRange,
                        gender: {
                            gender: 'm',
                        },
                        peopleCount: {
                            count: 0,
                        },
                        frontendLabel: `${ageRange.frontendLabel} m`,
                    });
                    ageGroups.push({
                        ageRange: ageRange,
                        gender: {
                            gender: 'w',
                        },
                        peopleCount: {
                            count: 0,
                        },
                        frontendLabel: `${ageRange.frontendLabel} w`,
                    });
                    ageGroups.push({
                        ageRange: ageRange,
                        gender: {
                            gender: 'x',
                        },
                        peopleCount: {
                            count: 0,
                        },
                        frontendLabel: `${ageRange.frontendLabel} x`,
                    });
                });
            return ageGroups;
        },
        userGroups() {
            let userGroups = [];
            if (!this.walk.isWithUserGroups) {
                return userGroups;
            }
            this.walk.userGroupNames
                .slice()
                .forEach((userGroupName) => {
                    userGroups.push({
                        userGroupName,
                        peopleCount: {
                            count: 0,
                        },
                    });
                });

            return userGroups;
        },
        consumables() {
            let consumables = [];
            if (!this.walk.isWithConsumables) {
                return consumables;
            }
            this.walk.consumableNames
                .slice()
                .forEach((consumableName) => {
                    consumables.push({
                        consumableName,
                        peopleCount: {
                            count: 0,
                        },
                    });
                });

            return consumables;
        },
        counselings() {
            let counselings = [];
            if (!this.walk.isWithCounselings) {
                return counselings;
            }
            this.walk.counselingNames
                .slice()
                .forEach((counselingName) => {
                    counselings.push({
                        counselingName,
                        peopleCount: {
                            count: 0,
                        },
                    });
                });

            return counselings;
        },
        medicals() {
            let medicals = [];
            if (!this.walk.isWithMedicals) {
                return medicals;
            }
            this.walk.medicalNames
                .slice()
                .forEach((medicalName) => {
                    medicals.push({
                        medicalName,
                        peopleCount: {
                            count: 0,
                        },
                    });
                });

            return medicals;
        },
    },
    async created() {
        this.wayPointStore.resetChangeError();
        this.wayPointStore.resetCreateError();
        if (this.initialWayPoint) {
            await this.wayPointStore.fetchWayPoints({filter: {walk: this.initialWayPoint.walk}, currentPage: 1, perPage: 1000});
        }
        if (!this.walk && this.initialWayPoint) {
            await this.walkStore.fetchByIri(this.initialWayPoint.walk);
        }
        if (!this.tags.length) {
            await this.tagStore.fetchTags();
        }
        if (!this.team) {
            await this.teamStore.fetchTeams();
        }
        if (this.initialWayPoint) {
            this.wayPoint.locationName = this.initialWayPoint.locationName;
            this.wayPoint.ageGroups = JSON.parse(JSON.stringify(this.initialWayPoint.ageGroups)) || [];
            this.wayPoint.userGroups = JSON.parse(JSON.stringify(this.initialWayPoint.userGroups)) || [];
            this.wayPoint.consumables = JSON.parse(JSON.stringify(this.initialWayPoint.consumables)) || [];
            this.wayPoint.counselings = JSON.parse(JSON.stringify(this.initialWayPoint.counselings)) || [];
            this.wayPoint.medicals = JSON.parse(JSON.stringify(this.initialWayPoint.medicals)) || [];
            this.wayPoint.imageName = this.initialWayPoint.imageName;
            if (this.initialWayPoint.imageSrc) {
                const response = await axios.get(this.initialWayPoint.imageSrc, { responseType: 'blob' });
                if (response.status) {
                    this.wayPoint.imageFileData = await this.readFile(response.data);
                    this.wayPoint.imageFileName = this.initialWayPoint.imageName;
                }
            }
            this.wayPoint.contactsCount = this.initialWayPoint.contactsCount;
            this.wayPoint.isMeeting = this.initialWayPoint.isMeeting || false;
            this.wayPoint.note = this.initialWayPoint.note;
            this.wayPoint.peopleCount = this.initialWayPoint.peopleCount;
            this.wayPoint.oneOnOneInterview = this.initialWayPoint.oneOnOneInterview;
            this.wayPoint.wayPointTags = JSON.parse(JSON.stringify(this.initialWayPoint.wayPointTags)) || [];
        } else {
            this.wayPoint.ageGroups = this.ageGroups;
            this.wayPoint.userGroups = this.userGroups;
            this.wayPoint.consumables = this.consumables;
            this.wayPoint.counselings = this.counselings;
            this.wayPoint.medicals = this.medicals;
            this.wayPoint.walk = this.walk['@id'];
        }

        this.wayPoint.contactsCount = this.walk.isWithContactsCount ? 0 : null;
    },
    watch: {
        file: {
            handler: async function () {
                this.updateFile(this.file)
            }
        },
    },
    methods: {
        getTagByIri(iri) {
            return this.tagStore.getTagByIri(iri);
        },
        selectCurrentTime() {
            this.wayPoint.visitedAt = dayjs().format();
        },
        selectFiveMinutesAfterLastWayPointOrStartOfWalkTime() {
            let time = this.lastWayPointOrRoundTime;
            if (this.hasLastWayPoint) {
                time = time.add(5, 'minute');
            }
            this.wayPoint.visitedAt = time.format();
        },
        updateFile: async function (file) {
            this.wayPoint.imageFileData = file ? await this.readFile(file) : null;
            this.wayPoint.imageFileName = file ? file.name : null;
        },
        readFile: function (file) {
            return new Promise((resolve, reject) => {
                const reader = new FileReader();

                reader.onload = res => {
                    resolve(res.target.result);
                };
                reader.onerror = err => reject(err);

                reader.readAsDataURL(file);
            });
        },
        async handleSubmitWithFinish() {
            this.$emit('submitted', { form: this.wayPoint, isWithFinish: true });
        },
        async handleSubmit() {
            this.$emit('submitted', { form: this.wayPoint, isWithFinish: false });
        },
        async handleSetWalkStartTime() {
            const dateTemplate = 'dddd DD.MM.YYYY [um] HH:mm';
            const previousFormattedDate = dayjs(this.walk.startTime).format(dateTemplate);
            const result = await this.walkStore.changeStartTime({
                walk: this.walk['@id'],
                startTime: dayjs(this.wayPoint.visitedAt).startOf('minute').format(),
            });

            if (result) {
                this.alertStore.success(
                    `Der Rundenbeginn wurde erfolgreich von "${ previousFormattedDate }" auf "${ dayjs(this.walk.startTime).format(dateTemplate) }" geändert.`,
                    'Rundenbeginn geändert'
                );
            } else {
                this.alertStore.error('Rundenbeginn ändern fehlgeschlagen', 'Upps! :-(');
            }
        }
    },
};
</script>

<style scoped lang="scss">
</style>
