<template>
    <b-form
        @submit.prevent.stop="handleSubmit"
        ref="form"
        class="p-1 p-sm-2 p-lg-3"
    >
        <b-form-group
            content-cols="12"
            label-cols="12"
            content-cols-lg="8"
            label-cols-lg="2"
            label="Name"
        >
            <b-input
                v-model="client.name"
                required
                minlength="4"
                maxlength="100"
                placeholder="Name"
                :state="nameState"
                data-test="name"
            />
        </b-form-group>
        <b-form-group
            content-cols="12"
            label-cols="12"
            content-cols-lg="8"
            label-cols-lg="2"
            label="E-Mail"
        >
            <b-input
                v-model="client.email"
                required
                minlength="4"
                maxlength="100"
                placeholder="E-Mail"
                :state="emailState"
                data-test="email"
            />
        </b-form-group>
        <b-form-group
            content-cols="12"
            label-cols="12"
            content-cols-lg="8"
            label-cols-lg="2"
            label="Beschreibung"
        >
            <b-textarea
                v-model="client.description"
                minlength="4"
                maxlength="100000"
                placeholder="Beschreibung"
                :state="descriptionState"
                data-test="description"
            />
        </b-form-group>
        <b-form-group
            label="Rating-Bild"
            :label-for="`input-rating-image-${initialClient.id}`"
            :state="ratingImageState"
            :invalid-feedback="invalidRatingImageFeedback"
            content-cols="12"
            label-cols="12"
            content-cols-lg="10"
            label-cols-lg="2"
        >
            <b-form-file
                :id="`input-rating-image-${initialClient.id}`"
                v-model="ratingFile"
                accept="image/*"
                aria-label="Rating-Bild"
                data-test="Rating-Bild"
                browse-text="Bild wählen"
                placeholder="kein Bild gewählt"
                drop-placeholder="Bild hierhin ziehen."
                :disabled="isLoading"
                :state="ratingImageState"
                @input="updateRatingFile"
            />
            <div
                v-if="client.ratingImageFileData"
                class="mt-3 position-relative"
                style="max-width: 50px;"
            >
                <div
                    class="cursor-pointer position-absolute top-0 start-100 translate-middle"
                    @click="client.ratingImageFileData = client.ratingImageFileName = client.ratingImageName = null"
                >
                    <mdicon
                        name="close-circle-outline"
                    />
                </div>
                <b-img
                    :src="client.ratingImageFileData"
                    alt="Rating-Bild"
                    thumbnail
                    fluid
                    width="50"
                    height="50"
                    class=""
                />
            </div>
            <div
                class="mt-2"
            >
                <b-alert
                    show
                    variant="info"
                >
                    Vorschau:
                    <div class="bg-white p-2 text-black">
                        <walk-rating
                            :rating="3"
                            :client="client"
                        />
                    </div>
                </b-alert>
            </div>
        </b-form-group>
        <b-button
            type="submit"
            variant="secondary"
            :disabled="isFormInvalid"
            data-test="button-client-submit"
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
import * as EmailValidator from 'email-validator';
import FormError from '../Common/FormError.vue';
import getViolationsFeedback from '../../utils/validation.js';
import axios from 'axios';
import WalkRating from '../Walk/WalkRating.vue';

export default {
    name: 'ClientForm',
    props: {
        initialClient: {
            type: Object,
            required: false,
            default: {},
        },
        submitButtonText: {
            type: String,
            required: true,
        },
    },
    components: {
        FormError,
        WalkRating,
    },
    data: function () {
        return {
            ratingFile: null,
            client: {
                name: null,
                email: null,
                description: '',
                ratingImageFileData: null,
                ratingImageFileName: null,
            },
        };
    },
    computed: {
        nameState() {
            if (null === this.client.name || '' === this.client.name || undefined === this.client.name) {
                return;
            }

            return this.client.name.length >= 3 && this.client.name.length <= 200;
        },
        emailState() {
            if (null === this.client.email || '' === this.client.email || undefined === this.client.email) {
                return;
            }

            return this.client.email.length >= 3 && this.client.email.length <= 100 && EmailValidator.validate(this.client.email);
        },
        descriptionState() {
            if (null === this.client.description || undefined === this.client.description) {
                return;
            }

            return this.client.description.length >= 0 && this.client.description.length <= 10000;
        },
        ratingImageState() {
            if (!this.client.ratingImageFileData) {
                return null;
            }

            return '' === this.invalidRatingImageFeedback;
        },
        invalidRatingImageFeedback() {
            return getViolationsFeedback(['decodedRatingImageData', 'ratingImageFileData', 'ratingImageFileName'], this.error);
        },
        isLoading() {
            return this.$store.getters['user/isLoadingChange'];
        },
        currentUser() {
            return this.$store.getters['security/currentUser'];
        },
        isSuperAdmin() {
            return this.$store.getters['security/isSuperAdmin'];
        },
        isFormInvalid() {
            return !this.nameState || !this.emailState || !this.descriptionState || this.isLoading;
        },
        error() {
            return this.$store.getters['client/changeClientError'];
        },
    },
    async created() {
        this.client.name = this.initialClient.name;
        this.client.email = this.initialClient.email;
        this.client.description = this.initialClient.description || '';
        if (this.initialClient.ratingImageSrc) {
            const response = await axios.get(this.initialClient.ratingImageSrc, { responseType: 'blob' });
            if (response.status) {
                this.client.ratingImageFileData = await this.readFile(response.data);
                this.client.ratingImageFileName = this.initialClient.ratingImageName;
            }
        }
    },
    methods: {
        async handleSubmit() {
            this.$emit('submit', this.client);
        },
        resetForm() {
            this.$refs.form.reset();
            this.client.name = this.initialClient.name;
            this.client.email = this.initialClient.email;
            this.client.description = this.initialClient.description || '';
        },
        updateRatingFile: async function (file) {
            this.client.ratingImageFileData = file ? await this.readFile(file) : null;
            this.client.ratingImageFileName = file ? file.name : null;
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
    },
};
</script>

<style scoped lang="scss">
</style>
