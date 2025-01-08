<template>
    <v-form
        v-if="walk"
        @submit.prevent.stop="handleSubmit"
        class="p-1 p-sm-2 p-lg-3"
    >
        <way-point-location-name-field
            v-model="wayPoint.locationName"
            :walk="walk"
            :initial-way-point="initialWayPoint"
            :team="team"
            :is-loading="isLoading"
            :error="error"
        />
        <v-row class="my-4">
            <v-col cols="12" md="6">
                <way-point-visited-at-field
                    v-model="wayPoint.visitedAt"
                    :initial-way-point="initialWayPoint"
                    :is-loading="isLoading"
                    :error="error"
                />
            </v-col>
            <v-col
                class="d-none d-md-block border-top-0 border-bottom-0 border-right-0 border-secondary border-dashed border-left"
            >
            </v-col>
            <v-col>
                <div class="d-none d-md-block">&nbsp;</div>
                <v-btn
                    block
                    color="secondary"
                    outlined
                    small
                    @click="selectCurrentTime"
                    class="mb-3"
                >
                    Schnellauswahl: aktueller Zeitpunkt
                </v-btn>
                <v-btn
                    color="secondary"
                    block
                    outlined
                    small
                    @click="selectFiveMinutesAfterLastWayPointOrStartOfWalkTime"
                    class="mt-2"
                >
                    Schnellauswahl: {{ walk.wayPoints.length ? '5 Minuten nach dem letzten Wegpunkt' : 'Rundenbeginn' }}
                </v-btn>
            </v-col>
        </v-row>
        <v-alert
            v-if="!!diffLastWayPointOrRound"
            class="mb-0"
            text
            color="warning"
        >
            Hinweis: Die gewählte Ankunftszeit ist <b>{{ diffLastWayPointOrRound }}</b> nach dem {{ hasLastWayPoint ? 'letzten Wegpunkt' : 'Rundenstart' }} vom {{ lastWayPointOrRoundTimeAsCalendar }}.
        </v-alert>
        <v-alert
            v-if="visitedAtState === false"
            class="mb-0"
            text
            color="warning"
        >
            {{ visitedAtDescription }}
        </v-alert>
        <v-alert
            v-if="isShowWalkStartTimeButton"
            class="mt-2"
            text
            color="warning"
        >
            Hinweis: Die gewählte Ankunftszeit ist <b>{{ diffWalkStartTime }}</b> vor dem Rundenstart. Hier kannst du die Rundenstartzeit auf die aktuell gewählte Ankunftszeit ändern.
            <div class="bg-white">
                <v-btn
                    color="secondary"
                    outlined
                    block
                    small
                    class="mt-2"
                    data-test="button-set-walk-start-time"
                    @click="handleSetWalkStartTime"
                >
                    {{ setWalkStartTimeButtonLabel }}
                </v-btn>
            </div>
        </v-alert>
        <template v-if="walk.isWithAgeRanges || walk.isWithPeopleCount">
            <div class="mb-4">
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
                v-if="walk.isWithAgeRanges"
                v-for="(ageGroup, index) in wayPoint.ageGroups"
                :key="ageGroup.frontendLabel"
            >
                <v-col
                    v-if="index % 3 === 0"
                    v-for="colIndex in 3"
                    :key="wayPoint.ageGroups[index + colIndex - 1].frontendLabel"
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
                        dense
                        outlined
                        class=""
                    />
                </v-col>
            </v-row>
            <v-text-field
                v-if="walk.isWithAgeRanges"
                v-model="sumPeopleCount"
                type="text"
                data-test="sumPeopleCount"
                disabled
                readonly
                dense
                persistent-hint
                outlined
                background-color="grey lighten-5"
                label="Anzahl Personen vor Ort"
                hint="Ergibt sich automatisch aus der Summe der Altersgruppen."
            />
            <v-select
                v-else
                v-model="wayPoint.peopleCount"
                :items="ageRangeOptions"
                :disabled="isLoading"
                data-test="peopleCount"
                class=""
                outlined
                dense
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
                    dense
                    outlined
                    :label="userGroup.userGroupName.name"
                />
            </v-col>
        </v-row>
<!--            :state="contactsCountState"-->
<!--            :invalid-feedback="invalidContactsCountState"-->
        <v-select
            v-if="walk.isWithContactsCount"
            v-model="wayPoint.contactsCount"
            required
            :state="contactsCountState"
            :items="contactsCountOptions"
            dense
            persistent-hint
            outlined
            label="Anzahl direkter Kontakte"
            hint="Mit wie viel Personen wurde gesprochen?"
            data-test="contactsCount"
        />
        <b-form-group
            id="input-group-image"
            label="Bildupload"
            label-for="input-image"
            :state="imageState"
            :invalid-feedback="invalidImageFeedback"
            content-cols="12"
            label-cols="12"
            content-cols-lg="10"
            label-cols-lg="2"
        >
            <b-form-file
                id="input-image"
                v-model="file"
                accept="image/*"
                aria-label="Bildupload"
                data-test="Bildupload"
                browse-text="Bild wählen"
                placeholder="kein Bild gewählt"
                drop-placeholder="Bild hierhin ziehen."
                :disabled="isLoading"
                :state="imageState"
                @input="updateFile"
            />
            <div
                v-if="wayPoint.imageFileData"
                class="mt-3 position-relative"
                style="max-width: 50px;"
            >
                <div
                    class="cursor-pointer position-absolute top-0 start-100 translate-middle"
                    @click="wayPoint.imageFileData = wayPoint.imageFileName = wayPoint.imageName = null"
                >
                    <v-icon>
                        mdi-close-circle-outline
                    </v-icon>
                </div>
                <b-img
                    :src="wayPoint.imageFileData"
                    alt="Bildupload"
                    thumbnail
                    fluid
                    width="50"
                    height="50"
                    class=""
                />
            </div>
        </b-form-group>
        <b-form-group
            content-cols="12"
            label-cols="12"
            content-cols-lg="10"
            label-cols-lg="2"
            label="Beobachtung"
            :invalid-feedback="invalidNoteFeedback"
            :state="noteState"
        >
            <b-textarea
                v-model="wayPoint.note"
                minlength="0"
                maxlength="2500"
                placeholder="Beobachtung"
                :state="noteState"
                data-test="note"
                rows="3"
                max-rows="15"
            />
        </b-form-group>
        <b-form-group
            content-cols="12"
            label-cols="12"
            content-cols-lg="10"
            label-cols-lg="2"
            label="Einzelgespräch"
            :invalid-feedback="invalidOneOnOneInterviewFeedback"
            :state="oneOnOneInterviewState"
        >
            <b-textarea
                v-model="wayPoint.oneOnOneInterview"
                minlength="0"
                maxlength="2500"
                placeholder="Einzelgespräch"
                :state="oneOnOneInterviewState"
                data-test="oneOnOneInterview"
                rows="3"
                max-rows="15"
            />
        </b-form-group>
        <b-form-group
            content-cols="12"
            label-cols="12"
            content-cols-lg="10"
            label-cols-lg="2"
            label="Tags"
        >
            <b-form-checkbox-group
                id="input-Tag"
                v-model="wayPoint.wayPointTags"
                :disabled="isLoading"
                class="row"
            >
                <template
                    v-for="tag in tags"
                >
                    <div
                        :key="tag['@id']"
                        :class="{'d-none': !tag.isEnabled}"
                        class="col-12 col-sm-6 col-md-6 col-lg-4 col-xl-3"
                    >
                        <b-form-checkbox
                            :value="tag['@id']"
                        >
                            {{ tag.name }}
                            <color-badge
                                :color="tag.color"
                            />
                        </b-form-checkbox>
                    </div>
                </template>
                <hr
                    v-if="hasDisabledTag"
                    class="col-12"
                >
                <template
                    v-for="tag in disabledTags"
                >
                    <div
                        :key="`disabledTags-${tag['@id']}`"
                        :class="tag.isEnabled || (initialWayPoint && initialWayPoint.wayPointTags.includes(tag['@id'])) ? 'col-12 col-sm-6 col-md-6 col-lg-4 col-xl-3' : 'd-none'"
                    >
                        <b-form-checkbox
                            :value="tag['@id']"
                        >
                            {{ tag.name }}
                            <color-badge
                                :color="tag.color"
                            />
                            <span
                                class="text-muted"
                            >
                                (deaktivierter Tag)
                            </span>
                        </b-form-checkbox>
                    </div>
                </template>
            </b-form-checkbox-group>
        </b-form-group>
        <b-form-group
            content-cols="12"
            label-cols="12"
            content-cols-lg="10"
            label-cols-lg="2"
            label="Mobiler Treff?"
        >
            <b-form-checkbox
                v-model="wayPoint.isMeeting"
                :disabled="isLoading"
            >
                mobiler Treff
            </b-form-checkbox>
        </b-form-group>
        <v-btn
            color="secondary"
            type="submit"
            :disabled="isSubmitDisabled"
            data-test="button-way-point-submit"
            block
            class="mb-2"
            :tabindex="isSubmitDisabled ? '-1' : ''"
        >
            {{ submitButtonText }}
        </v-btn>
        <v-btn
            v-if="initialWalk"
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
import {useAlertStore, useAuthStore, useTagStore, useTeamStore, useWalkStore, useWayPointStore} from '../../stores';
import { WayPointLocationNameField, WayPointVisitedAtField } from "../Common/WayPoint";

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
            contactsCountOptions: Array.from(Array(41), (x, i) => i),
            userGroupOptions: Array.from(Array(21), (x, i) => i),
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
                    labelHelp: 'Mit den Pfeiltasten durch den Kalender navigieren',
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
                    labelCloseButton: 'Schließen',
                },
            },
        };
    },
    computed: {
        error() {
            return this.wayPointStore.getErrors.change || this.wayPointStore.getErrors.create;
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
        contactsCountState() {
            if ('' === this.invalidContactsCountState && null === this.wayPoint.contactsCount) {
                return null;
            }

            return '' === this.invalidContactsCountState;
        },
        invalidContactsCountState() {
            return getViolationsFeedback(['contactsCount'], this.error);
        },
        noteState() {
            if (null === this.wayPoint.note || undefined === this.wayPoint.note) {
                return;
            }

            return '' === this.invalidNoteFeedback;
        },
        invalidNoteFeedback() {
            return getViolationsFeedback(['note'], this.error);
        },
        oneOnOneInterviewState() {
            if (null === this.wayPoint.oneOnOneInterview || undefined === this.wayPoint.oneOnOneInterview) {
                return;
            }

            return '' === this.invalidOneOnOneInterviewFeedback;
        },
        invalidOneOnOneInterviewFeedback() {
            return getViolationsFeedback(['oneOnOneInterview'], this.error);
        },
        imageState() {
            if (!this.wayPoint.imageFileData) {
                return null;
            }

            return '' === this.invalidImageFeedback;
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
            return this.isLoading || !this.visitedAtState;
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
            this.wayPoint.walk = this.walk['@id'];
        }

        this.wayPoint.contactsCount = this.walk.isWithContactsCount ? 0 : null;
    },
    methods: {
        getTagByIri(iri) {
            return this.tagStore.getTagByIri(iri);
        },
        getWayPointByIri(iri) {
            return this.wayPointStore.getWayPointByIri(iri);
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
            this.$emit('submit', { form: this.wayPoint, isWithFinish: true });
        },
        async handleSubmit() {
            this.$emit('submit', { form: this.wayPoint, isWithFinish: false });
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
