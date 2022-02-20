<template>
    <b-form
        @submit.prevent.stop="handleSubmit"
        class="p-1 p-sm-2 p-lg-3"
    >
        form
        <b-form-group
            content-cols="12"
            label-cols="12"
            content-cols-lg="10"
            label-cols-lg="2"
            label="Ort"
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
                        <mdicon name="CloseCircleOutline" size="20" />
                    </b-button>
                </b-input-group-append>
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
            :disabled="isFormInvalid"
            data-test="button-way-point-submit"
            block
            class="col-12"
            :tabindex="isFormInvalid ? '-1' : ''"
        >
            {{ submitButtonText }}
        </b-button>
        <b-button
            v-if="walk"
            type="submit"
            variant="secondary"
            :disabled="isFormInvalid"
            data-test="button-way-point-submit-and-finish"
            data-with-finish
            block
            class="col-12"
            :tabindex="isFormInvalid ? '-1' : ''"
        >
            Wegpunkt speichern und Runde abschließen
        </b-button>
        <form-error
            :error="error"
        />
    </b-form>
</template>

<script>
'use strict';
import FormError from '../Common/FormError.vue';
import ColorBadge from '../Tags/ColorBadge.vue';

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
        FormError,
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
            },
            file: null,
            ageRangeOptions: Array.from(Array(21), (x, i) => i),
        };
    },
    computed: {
        locationNames() {
            if (!this.team) {
                return [];
            }
            return this.team.locationNames.filter((locationName) => {
                return locationName.toLowerCase().startsWith(this.wayPoint.locationName.toLowerCase());
            }).map((locationName) => locationName);
        },
        team() {
            return this.$store.getters['team/getTeamByTeamName'](this.walk.teamName);
        },
        walk() {
            return this.initialWalk ? this.initialWalk : this.$store.getters['walk/getWalkByIri'](this.initialWayPoint.walk);
        },
        locationNameState() {
            if (null === this.wayPoint.locationName || '' === this.wayPoint.locationName || undefined === this.wayPoint.locationName) {
                return;
            }

            return this.wayPoint.locationName.length >= 2 && this.wayPoint.locationName.length <= 300;
        },
        noteState() {
            if (null === this.wayPoint.note || undefined === this.wayPoint.note) {
                return;
            }

            return this.wayPoint.note.length >= 0 && this.wayPoint.note.length <= 2500;
        },
        oneOnOneInterviewState() {
            if (null === this.wayPoint.oneOnOneInterview || undefined === this.wayPoint.oneOnOneInterview) {
                return;
            }

            return this.wayPoint.oneOnOneInterview.length >= 0 && this.wayPoint.oneOnOneInterview.length <= 2500;
        },
        imageState() {
            if (!this.wayPoint.imageFileData) {
                return null;
            }

            return undefined === this.validationErrors.decodedImageData && undefined === this.validationErrors.imageFileData && undefined === this.validationErrors.imageFileName;
        },
        invalidImageFeedback() {
            let message = '';
            ['decodedImageData', 'imageFileData', 'imageFileName'].forEach(key => {
                if (this.validationErrors[key]) {
                    message += ` ${this.validationErrors[key]}`;
                }
            });

            return message;
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
            return this.$store.getters['wayPoint/isLoadingChange'];
        },
        currentUser() {
            return this.$store.getters['security/currentUser'];
        },
        isSuperAdmin() {
            return this.$store.getters['security/isSuperAdmin'];
        },
        isFormInvalid() {
            return (!this.locationNameState || !this.noteState || !this.oneOnOneInterviewState || this.isLoading) && this.imageState !== null;
        },
        error() {
            return this.$store.getters['wayPoint/errorChange'];
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
        async handleSubmit(e) {
            const isWithFinish = undefined !== e.submitter.dataset.withFinish;
            this.$emit('submit', { form: this.wayPoint, isWithFinish });
        },
    },
};
</script>

<style scoped lang="scss">
</style>
