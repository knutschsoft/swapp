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
            v-if="walk.isWithContactsCount"
            content-cols="12"
            label-cols="12"
            content-cols-lg="10"
            label-cols-lg="2"
            label="Anzahl der Kontakte"
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
            content-cols="12"
            label-cols="12"
            content-cols-lg="10"
            label-cols-lg="2"
            label="Angetroffene Personenzahl"
            description="Ergibt sich automatisch aus der Summe der Altersgruppen."
        >
            <b-input-group>
                <b-input
                    v-model="sumPeopleCount"
                    type="text"
                    disabled
                    readonly
                />
            </b-input-group>
        </b-form-group>
        <b-form-group
            content-cols="12"
            label-cols="12"
            content-cols-lg="10"
            label-cols-lg="2"
            label="Altersgruppen"
        >
            <b-row
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
                            size="sm"
                            class=""
                        ></b-form-select>
                    </b-form-group>
                </b-col>
            </b-row>
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
            type="submit"
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
                ageGroups: [],
                imageName: null,
                isMeeting: false,
                note: '',
                oneOnOneInterview: '',
                wayPointTags: [],
                imageFileData: null,
                imageFileName: null,
                contactsCount: null,
            },
            file: null,
            ageRangeOptions: Array.from(Array(21), (x, i) => i),
            contactsCountOptions: Array.from(Array(41), (x, i) => i),
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
            return this.$store.getters['wayPoint/isLoadingChange'];
        },
        currentUser() {
            return this.$store.getters['security/currentUser'];
        },
        isSuperAdmin() {
            return this.$store.getters['security/isSuperAdmin'];
        },
        isSubmitDisabled() {
            return this.isLoading;
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
            this.wayPoint.oneOnOneInterview = this.initialWayPoint.oneOnOneInterview;
            this.wayPoint.wayPointTags = JSON.parse(JSON.stringify(this.initialWayPoint.wayPointTags)) || [];
        } else {
            this.wayPoint.ageGroups = this.ageGroups;
            this.wayPoint.walk = this.walk['@id'];
        }
    },
    methods: {
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
