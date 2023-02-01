<template>
    <b-form
        @submit.prevent.stop="handleSubmit"
        class="p-1 p-sm-2 p-lg-3"
    >
        <b-form-group
            content-cols="12"
            label-cols="12"
            content-cols-lg="10"
            label-cols-lg="2"
            label="Ort"
            :state="locationNameState"
            :invalid-feedback="invalidLocationNameState"
        >
            <b-input-group>
                <b-input
                    v-model="wayPoint.locationName"
                    required
                    maxlength="300"
                    :placeholder="walk ? 'Wo seid ihr gerade?' : 'Ort'"
                    :state="locationNameState"
                    data-test="locationName"
                    list="location-name-list"
                />
                <datalist id="location-name-list">
                    <option v-for="locationName in locationNames">{{ locationName }}</option>
                </datalist>
                <b-input-group-append>
                    <b-button
                        @click="wayPoint.locationName = ''"
                        :disabled="wayPoint.locationName === ''"
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
            label="Ankunft"
            :description="visitedAtDescription"
            :invalid-feedback="invalidVisitedAtState"
            :state="visitedAtState"
        >
            <b-row>
                <b-col>
                    <b-timepicker
                        v-model="visitedAtTime"
                        v-bind="timeLabels['de']"
                        :disabled="isLoading"
                        :state="visitedAtState"
                        data-test="visitedAtTime"
                        minutes-step="5"
                        locale="de"
                        size="sm"
                    />
                    <b-datepicker
                        v-model="visitedAtDate"
                        v-bind="dateLabels['de']"
                        :disabled="isLoading"
                        :state="visitedAtState"
                        data-test="visitedAtDate"
                        locale="de"
                        size="sm"
                        class="mt-2"
                        right
                    />
                </b-col>
                <b-col
                    class="border-top-0 border-bottom-0 border-right-0 border-secondary border-dashed border-left"
                >
                    <b-button
                        variant="outline-secondary"
                        block
                        size="sm"
                        @click="selectCurrentTime"
                    >
                        Schnellauswahl: aktueller Zeitpunkt
                    </b-button>
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
                    variant="warning"
                >
                    Hinweis: Die gewählte Ankunftszeit ist <b>{{ diffLastWayPointOrRound }}</b> nach dem {{ hasLastWayPoint ? 'letzten Wegpunkt' : 'Rundenstart' }} vom {{ lastWayPointOrRoundTimeAsCalendar }}.
                </b-alert>
            </template>
        </b-form-group>
        <b-form-group
            v-if="walk.isWithAgeRanges || walk.isWithPeopleCount"
            content-cols="12"
            label-cols="12"
            content-cols-lg="10"
            label-cols-lg="2"
        >

            <template v-slot:label>
                <b v-text="walk.isWithAgeRanges ? `Altersgruppen` : `Anzahl der Personen vor Ort`" />
                <br v-if="walk.isWithAgeRanges">
                <small
                    v-if="walk.isWithAgeRanges"
                    class="font-weight-normal text-muted"
                >
                    Anzahl der Personen vor Ort
                </small>
            </template>
            <b-row
                v-if="walk.isWithAgeRanges"
                v-for="(ageGroup, index) in wayPoint.ageGroups"
                :key="ageGroup.frontendLabel"
            >
                <b-col
                    v-if="index % 3 === 0"
                    v-for="colIndex in 3"
                    :key="wayPoint.ageGroups[index + colIndex - 1].frontendLabel"
                    cols="4"
                    sm="4"
                    md="4"
                    class="mb-1"
                >
                    <b-form-group
                        :label="wayPoint.ageGroups[index + colIndex - 1].frontendLabel"
                        label-cols-sm="auto"
                        label-cols="12"
                    >
                        <b-form-select
                            v-model="wayPoint.ageGroups[index + colIndex - 1].peopleCount.count"
                            :options="ageRangeOptions"
                            :disabled="isLoading"
                            :help="wayPoint.ageGroups[index + colIndex - 1].frontendLabel"
                            :data-test="wayPoint.ageGroups[index + colIndex - 1].frontendLabel"
                            size="sm"
                            class=""
                        ></b-form-select>
                    </b-form-group>
                </b-col>
            </b-row>

            <b-form-group
                v-if="walk.isWithAgeRanges"
                content-cols="12"
                label-cols="12"
                content-cols-lg="10"
                label-cols-lg="2"
                label="Anzahl Personen vor Ort"
                description="Ergibt sich automatisch aus der Summe der Altersgruppen."
            >
                <b-input-group>
                    <b-input
                        v-model="sumPeopleCount"
                        type="text"
                        data-test="sumPeopleCount"
                        disabled
                        readonly
                    />
                </b-input-group>
            </b-form-group>
            <b-input-group
                v-else
                content-cols="12"
                label-cols="12"
                content-cols-lg="10"
                label-cols-lg="2"
                label="Anzahl Personen vor Ort"
            >
                <b-form-select
                    v-model="wayPoint.peopleCount"
                    :options="ageRangeOptions"
                    :disabled="isLoading"
                    data-test="peopleCount"
                    class=""
                ></b-form-select>
            </b-input-group>
        </b-form-group>
        <b-form-group
            v-if="walk.isWithUserGroups"
            content-cols="12"
            label-cols="12"
            content-cols-lg="10"
            label-cols-lg="2"
            label="Personenanzahl von Nutzergruppen"
        >
            <b-row
                class="d-flex align-items-end"
            >
                <b-col
                    v-for="(userGroup, index) in wayPoint.userGroups"
                    :key="userGroup.userGroupName.name"
                >
                    <b-form-group
                        :label="userGroup.userGroupName.name"
                    >
                        <b-form-select
                            v-model="userGroup.peopleCount.count"
                            :options="ageRangeOptions"
                            :disabled="isLoading"
                            :help="userGroup.userGroupName.name"
                            size="sm"
                        ></b-form-select>
                    </b-form-group>
                </b-col>
            </b-row>
        </b-form-group>
        <b-form-group
            v-if="walk.isWithContactsCount"
            content-cols="12"
            label-cols="12"
            content-cols-lg="10"
            label-cols-lg="2"
            label="Anzahl direkter Kontakte"
            :state="contactsCountState"
            description="Mit wie viel Personen wurde gesprochen?"
            :invalid-feedback="invalidContactsCountState"
        >
            <b-input-group>
                <b-form-select
                    v-model="wayPoint.contactsCount"
                    required
                    :state="contactsCountState"
                    :options="contactsCountOptions"
                    size="sm"
                    data-test="contactsCount"
                ></b-form-select>
            </b-input-group>
        </b-form-group>
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
                style="max-width: 200px;"
            >
                <div
                    class="cursor-pointer position-absolute top-0 start-100 translate-middle"
                    @click="wayPoint.imageFileData = wayPoint.imageFileName = null"
                >
                    <mdicon
                        name="close-circle-outline"
                    />
                </div>
                <b-img
                    :src="wayPoint.imageFileData"
                    alt="Bildupload"
                    thumbnail
                    fluid
                    width="200"
                    height="200"
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
                <div
                    v-for="tag in tags"
                    :key="tag['@id']"
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
        <b-button
            type="submit"
            variant="secondary"
            :disabled="isSubmitDisabled"
            data-test="button-way-point-submit"
            block
            class="col-12"
            :tabindex="isSubmitDisabled ? '-1' : ''"
        >
            {{ submitButtonText }}
        </b-button>
        <b-button
            v-if="initialWalk"
            variant="secondary"
            :disabled="isSubmitDisabled"
            data-test="button-way-point-submit-and-finish"
            data-with-finish
            @click="handleSubmitWithFinish"
            block
            class="col-12"
            :tabindex="isSubmitDisabled ? '-1' : ''"
        >
            Wegpunkt speichern und Runde abschließen
        </b-button>
        <global-form-error
            :error="globalErrors"
        />
    </b-form>
</template>

<script>
'use strict';
import GlobalFormError from '../Common/GlobalFormError.vue';
import ColorBadge from '../Tags/ColorBadge.vue';
import getViolationsFeedback from '../../utils/validation.js';
import axios from 'axios';
import dayjs from 'dayjs';

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
        ColorBadge,
        GlobalFormError,
    },
    data: function () {
        return {
            wayPoint: {
                locationName: '',
                visitedAt: dayjs(),
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
            visitedAtTime: null,
            visitedAtDate: null,
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
            return this.$store.getters['wayPoint/errorChange'];
        },
        locationNames() {
            if (!this.team) {
                return [];
            }
            return this.team.locationNames.filter((locationName) => {
                return locationName.toLowerCase().startsWith(this.wayPoint.locationName.toLowerCase());
            }).map((locationName) => locationName);
        },
        sumPeopleCount() {
            let sumPeopleCount = 0;
            this.wayPoint.ageGroups.forEach(ageGroup => sumPeopleCount += ageGroup.peopleCount.count);

            return sumPeopleCount;
        },
        team() {
            return this.$store.getters['team/getTeamByTeamName'](this.walk.teamName);
        },
        walk() {
            return this.initialWalk ? this.initialWalk : this.$store.getters['walk/getWalkByIri'](this.initialWayPoint.walk);
        },
        locationNameState() {
            if ((null === this.wayPoint.locationName || '' === this.wayPoint.locationName || undefined === this.wayPoint.locationName) && '' === this.invalidLocationNameState) {
                return;
            }

            return '' === this.invalidLocationNameState;
        },
        invalidLocationNameState() {
            return getViolationsFeedback(['locationName'], this.error);
        },
        visitedAtState() {
            if (null === this.wayPoint.visitedAt || undefined === this.wayPoint.visitedAt) {
                return;
            }
            if (!this.visitedAtTime || !this.visitedAtDate) {
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
        lastWayPointOrRoundTime() {
            let time = false;
            this.walk.wayPoints.slice().reverse().every(wayPointIri => {
                if (!this.initialWayPoint || this.initialWayPoint['@id'] !== wayPointIri) {
                    const wayPoint = this.getWayPointByIri(wayPointIri);
                    time = dayjs(wayPoint.visitedAt);

                    return false;
                }
            });

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
        invalidVisitedAtState() {
            return getViolationsFeedback(['visitedAt'], this.error);
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
            return this.$store.getters['wayPoint/isLoadingChange']
                || this.$store.getters['wayPoint/isLoading']
                || this.$store.getters['walk/isLoading']
                || this.$store.getters['tag/isLoading']
                || this.$store.getters['team/isLoading'];
        },
        currentUser() {
            return this.$store.getters['security/currentUser'];
        },
        isSuperAdmin() {
            return this.$store.getters['security/isSuperAdmin'];
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
        tags() {
            return this.$store.getters['tag/tags'];
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
    watch: {
        visitedAtTime(visitedAtTime) {
            const values = visitedAtTime.split(':');
            if (values.length < 2) {
                return;
            }
            let visitedAt = dayjs(this.wayPoint.visitedAt);
            visitedAt = visitedAt.hour(Number(values[0]));
            visitedAt = visitedAt.minute(Number(values[1]));
            this.wayPoint.visitedAt = visitedAt.format();
        },
        visitedAtDate(visitedAtDate) {
            const visitedAtDateValue = dayjs(visitedAtDate);
            let visitedAt = dayjs(this.wayPoint.visitedAt);
            visitedAt = visitedAt.year(visitedAtDateValue.year());
            visitedAt = visitedAt.month(visitedAtDateValue.month());
            visitedAt = visitedAt.date(visitedAtDateValue.date());
            this.wayPoint.visitedAt = visitedAt.format();
        },
    },
    async created() {
        await this.$store.dispatch('wayPoint/resetChangeError');
        if (!this.walk && this.initialWayPoint) {
            await this.$store.dispatch('walk/find', this.initialWayPoint.walk);
        }
        if (!this.tags.length) {
            await this.$store.dispatch('tag/findAll');
        }
        if (!this.team) {
            await this.$store.dispatch('team/findAll');
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
            this.visitedAtDate = dayjs(this.initialWayPoint.visitedAt).format('YYYY-MM-DD');
            this.visitedAtTime = dayjs(this.initialWayPoint.visitedAt).format('HH:mm');
        } else {
            this.wayPoint.ageGroups = this.ageGroups;
            this.wayPoint.userGroups = this.userGroups;
            this.wayPoint.walk = this.walk['@id'];
            this.visitedAtTime = dayjs().format('HH:mm');
            this.visitedAtDate = dayjs().format('YYYY-MM-DD');
        }

        this.wayPoint.contactsCount = this.walk.isWithContactsCount ? 0 : null;
    },
    methods: {
        getWayPointByIri(iri) {
            return this.$store.getters['wayPoint/getWayPointByIri'](iri);
        },
        selectCurrentTime() {
            this.visitedAtTime = dayjs().format('HH:mm');
            this.visitedAtDate = dayjs().format('YYYY-MM-DD');
        },
        selectFiveMinutesAfterLastWayPointOrStartOfWalkTime() {
            let time = this.lastWayPointOrRoundTime;
            if (this.walk.wayPoints.length) {
                time = time.add(5, 'minute');
            }
            this.visitedAtTime = time.format('HH:mm');
            this.visitedAtDate = time.format('YYYY-MM-DD');
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
    },
};
</script>

<style scoped lang="scss">
</style>
