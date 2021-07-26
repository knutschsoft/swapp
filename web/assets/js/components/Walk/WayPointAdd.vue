<template>
    <div>
        <b-alert
            :show="showSuccess"
            variant="success"
            class="m-2"
        >
            <mdicon
                name="check-circle-outline"
                class="mr-2"
            />
            Wegpunkt erfolgreich hinzugefügt.
        </b-alert>
        <b-form
            @submit.stop.prevent="onSubmit"
            class="pt-2 px-2"
        >
            <b-form-group
                label="Ort"
                label-for="input-Ort"
                :state="locationNameState"
                :invalid-feedback="invalidLocationNameFeedback"
                description=""
            >
                <b-form-input
                    id="input-Ort"
                    v-model="form.locationName"
                    type="text"
                    :state="locationNameState"
                    :disabled="isLoading"
                    list="location-name-list"
                    placeholder="Wo seid ihr gerade?"
                    data-test="Ort"
                    autocomplete="off"
                    required
                ></b-form-input>
                <datalist id="location-name-list">
                    <option v-for="locationName in locationNames">{{ locationName }}</option>
                </datalist>
            </b-form-group>

            <b-form-group id="input-group-Altersgruppen" label="Altersgruppen" label-for="input-Altersgruppen">
                <b-form-group
                    v-for="ageGroup in form.ageGroups"
                    :key="ageGroup.frontendLabel"
                    :label="ageGroup.frontendLabel"
                    label-cols="4"
                    content-cols="8"
                    label-cols-sm="3"
                    content-cols-sm="9"
                    label-cols-md="2"
                    content-cols-md="10"
                    label-cols-lg="1"
                    content-cols-lg="11"
                >
                    <b-form-select
                        v-model="ageGroup.peopleCount.count"
                        :options="ageRangeOptions"
                        :disabled="isLoading"
                        size="sm"
                        class=""
                    ></b-form-select>
                </b-form-group>
            </b-form-group>

            <b-form-group
                id="input-group-image"
                label="Bildupload"
                label-for="input-image"
                :state="imageState"
                :invalid-feedback="invalidImageFeedback"
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
                    v-if="form.imageFileData"
                    class="mt-3 position-relative"
                    style="max-width: 200px;"
                >
                    <div
                        class="cursor-pointer position-absolute top-0 start-100 translate-middle"
                        @click="form.imageFileData = form.imageFileName = null"
                    >
                        <mdicon
                            name="close-circle-outline"
                        />
                    </div>
                    <b-img
                        :src="form.imageFileData"
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
                label="Beobachtung"
                label-for="input-Beobachtung"
                :state="noteState"
                :invalid-feedback="invalidNoteFeedback"
                description=""
            >
                <b-form-textarea
                    id="input-Beobachtung"
                    v-model="form.note"
                    data-test="Beobachtung"
                    :state="noteState"
                    :disabled="isLoading"
                    placeholder=""
                    rows="3"
                    max-rows="15"
                ></b-form-textarea>
            </b-form-group>
            <b-form-group id="input-group-Tag" label="Tags" label-for="input-Tag">
                <b-form-checkbox-group
                    id="input-Tag"
                    v-model="form.tags"
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
            <b-form-group id="input-group-is-mobile" label="" label-for="input-is-mobile">
                <b-form-checkbox
                    id="input-is-mobile"
                    v-model="form.isMeeting"
                    :disabled="isLoading"
                    aria-label="mobiler Treff"
                >
                    mobiler Treff
                </b-form-checkbox>
            </b-form-group>
            <div
                class="mx-2 my-1 my-sm-2 my-lg-3"
            >
                <b-button
                    class="btn btn-secondary"
                    :disabled="isLoading"
                    type="submit"
                    data-test="save-way-point"
                    block
                >
                    Wegpunkt speichern und weiteren Wegpunkt hinzufügen
                </b-button>
            </div>
            <hr class="mx-2 mt-0">
            <div
                class="mx-2 my-1 my-sm-2 my-lg-3"
            >
                <b-button
                    class="btn btn-secondary"
                    :disabled="isLoading"
                    type="submit"
                    data-test="save-way-point-and-finish"
                    data-with-finish
                    block
                >
                    Wegpunkt speichern und Runde abschließen
                </b-button>
            </div>
        </b-form>
    </div>
</template>

<script>
'use strict';

import LocationLink from '../LocationLink.vue';
import ColorBadge from '../Tags/ColorBadge.vue';
import WayPointApi from '../../api/wayPoint.js';

export default {
    name: 'WayPointAdd',
    components: {
        ColorBadge,
        LocationLink,
    },
    props: {
        walk: {
            type: Object,
            required: true,
        },
    },
    data: function () {
        const emptyForm = {
            walk: this.walk['@id'],
            locationName: '',
            ageGroups: [],
            imageFileData: null,
            imageFileName: null,
            note: '',
            tags: [],
            isMeeting: false,
        };
        return {
            showSuccess: false,
            form: emptyForm,
            emptyForm,
            file: null,
            ageRangeOptions: Array.from(Array(21), (x, i) => i),
            allLocationNames: [],
        };
    },
    computed: {
        locationNames() {
            if (this.form.locationName.length < 1) {
                return [];
            }
            return this.allLocationNames.filter((locationName) => {
               return locationName.locationName.toLowerCase().startsWith(this.form.locationName.toLowerCase());
            }).map((locationName) => locationName.locationName);
        },
        isLoading() {
            return this.$store.getters['walk/isLoading'] || this.$store.getters['wayPoint/isLoading'] || this.isFormLoading;
        },
        isFormLoading() {
            return this.$store.getters['wayPoint/isLoadingChange'];
        },
        hasWalks() {
            return this.$store.getters['walk/hasWalks'];
        },
        walks() {
            return this.$store.getters['walk/walks'];
        },
        hasWayPoints() {
            return this.$store.getters['wayPoint/hasWayPoints'];
        },
        wayPoints() {
            return this.$store.getters['wayPoint/wayPoints'];
        },
        tags() {
            return this.$store.getters['tag/tags'];
        },
        error() {
            return this.$store.getters['wayPoint/errorChange'];
        },
        hasError() {
            return !!this.error;
        },
        locationNameState() {
            if (!this.form.locationName) {
                return null;
            }

            return undefined === this.validationErrors.locationName;
        },
        invalidLocationNameFeedback() {
            let message = '';
            ['locationName'].forEach(key => {
                if (this.validationErrors[key]) {
                    message += ` ${this.validationErrors[key]}`;
                }
            });

            return message;
        },
        noteState() {
            if (!this.form.note) {
                return null;
            }

            return undefined === this.validationErrors.note;
        },
        invalidNoteFeedback() {
            let message = '';
            ['note'].forEach(key => {
                if (this.validationErrors[key]) {
                    message += ` ${this.validationErrors[key]}`;
                }
            });

            return message;
        },
        imageState() {
            if (!this.form.imageFileData) {
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
    },
    watch: {},
    async mounted() {
        if (!this.tags.length) {
            await this.$store.dispatch('tag/findAll');
        }
        let ageGroups = [];
        this.walk.ageRanges
            .slice()
            .sort((a, b) => a.rangeStart - b.rangeStart)
            .forEach((ageRange) => {
                ageGroups.push({
                    ageRange: ageRange,
                    gender: {
                        gender: 'm',
                        wurst: 'm',
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
        this.form.ageGroups = ageGroups;
        this.emptyForm = JSON.parse(JSON.stringify(this.form));

        const allLocationNames = await WayPointApi.findAllLocationNames();
        console.log(allLocationNames);
        this.allLocationNames = allLocationNames.data['hydra:member'];
    },
    methods: {
        updateFile: async function (file) {
            this.form.imageFileData = file ? await this.readFile(file) : null;
            this.form.imageFileName = file ? file.name : null;
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
        getWayPointByIri(iri) {
            return this.$store.getters['wayPoint/getWayPointByIri'](iri);
        },
        onSubmit: async function (e) {
            this.showSuccess = false;
            const wayPoint = await this.$store.dispatch('wayPoint/create', this.form);

            window.scrollTo({
                top: 0,
                left: 0,
                behavior: 'smooth',
            });
            if (wayPoint) {
                await this.$store.dispatch('walk/findByIri', wayPoint.walk);
                this.showSuccess = true;
                this.form = JSON.parse(JSON.stringify(this.emptyForm));
                this.file = null;
                if (undefined !== e.submitter.dataset.withFinish) {
                    this.$router.push({
                        name: 'WalkEpilogue',
                        params: { walkId: this.walk.id, successMessage: 'Wegpunkt erfolgreich hinzugefügt. Die Runde kann jetzt abgeschlossen werden.' },
                    });
                }
            } else {
                this.showSuccess = false;
            }
        },
    },
};
</script>

<style scoped>
</style>
