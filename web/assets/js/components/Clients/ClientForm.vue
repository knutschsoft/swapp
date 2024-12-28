<script lang="ts" setup>
import { ref, computed, watch, defineProps, defineEmits, onMounted } from 'vue';
import axios from 'axios';
import * as EmailValidator from 'email-validator';
import FormError from '../Common/FormError.vue';
import WalkRating from '../Walk/WalkRating.vue';
import { getViolationsFeedback } from '../../utils/validation';
import { useClientStore } from '../../stores/client';

interface Client {
    name: string | null;
    email: string | null;
    description: string;
    ratingImageFileData: string | null;
    ratingImageFileName: string | null;
}

const props = defineProps({
    initialClient: {
        type: Object as () => Partial<Client>,
        required: false,
        default: () => ({}),
    },
    submitButtonText: {
        type: String,
        required: true,
    },
});

const emit = defineEmits(['submit']);

const clientStore = useClientStore();
const form = ref<HTMLFormElement | null>(null);
const ratingFile = ref<File | null>(null);

const client = ref<Client>({
    name: null,
    email: null,
    description: '',
    ratingImageFileData: null,
    ratingImageFileName: null,
});

const nameState = computed(() => client.value.name?.length >= 3 && client.value.name.length <= 200);
const emailState = computed(() => client.value.email?.length >= 3 && client.value.email.length <= 100 && EmailValidator.validate(client.value.email || ''));
const descriptionState = computed(() => client.value.description.length >= 0 && client.value.description.length <= 10000);
const ratingImageState = computed(() => !!client.value.ratingImageFileData && invalidRatingImageFeedback.value === '');
const invalidRatingImageFeedback = computed(() => {
    const error = isInitialForm ? errorCreate.value : errorChange.value;
    return getViolationsFeedback(['decodedRatingImageData', 'ratingImageFileData', 'ratingImageFileName'], error)
});
const isInitialForm = computed<boolean>(() => !props.initialClient.clientId);
const isLoading = computed(() => props.initialClient['@id'] ? clientStore.isLoadingChange(props.initialClient['@id']) : clientStore.isLoadingCreate);
const isFormInvalid = computed(() => !nameState.value || !emailState.value || !descriptionState.value || isLoading.value);
const errorChange = computed(() => clientStore.getErrors.change);
const errorCreate = computed(() => clientStore.getErrors.create);

onMounted(() => {
    setInitialValues();
});

watch(() => props.initialClient, () => {
    setInitialValues();
});

async function setInitialValues() {
    client.value.name = props.initialClient.name || null;
    client.value.email = props.initialClient.email || null;
    client.value.description = props.initialClient.description || '';
    if (props.initialClient.ratingImageSrc) {
        const response = await axios.get(props.initialClient.ratingImageSrc, { responseType: 'blob' });
        if (response.status) {
            client.value.ratingImageFileData = await readFile(response.data);
            client.value.ratingImageFileName = props.initialClient.ratingImageName;
        }
    } else {
        client.value.ratingImageFileData = null;
        client.value.ratingImageFileName = null;
    }
}

function handleSubmit() {
    emit('submit', client.value);
}

function resetForm() {
    form.value?.reset();
    setInitialValues();
}

async function updateRatingFile(file: File | null) {
    client.value.ratingImageFileData = file ? await readFile(file) : null;
    client.value.ratingImageFileName = file ? file.name : null;
}

function readFile(file: Blob): Promise<string> {
    return new Promise((resolve, reject) => {
        const reader = new FileReader();
        reader.onload = (res) => resolve(res?.target?.result as string);
        reader.onerror = reject;
        reader.readAsDataURL(file);
    });
}
</script>

<template>
    <b-form @submit.prevent.stop="handleSubmit" ref="form" class="p-1 p-sm-2 p-lg-3">
        <b-form-group label="Name" content-cols="12" label-cols="12" content-cols-lg="8" label-cols-lg="2">
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
        <b-form-group label="E-Mail" content-cols="12" label-cols="12" content-cols-lg="8" label-cols-lg="2">
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
        <b-form-group label="Beschreibung" content-cols="12" label-cols="12" content-cols-lg="8" label-cols-lg="2">
            <b-textarea
                v-model="client.description"
                minlength="4"
                maxlength="10000"
                placeholder="Beschreibung"
                :state="descriptionState"
                data-test="description"
            />
        </b-form-group>
        <b-form-group label="Rating-Bild" :label-for="`input-rating-image-${props.initialClient.clientId}`" :state="ratingImageState" :invalid-feedback="invalidRatingImageFeedback">
            <b-form-file
                :id="`input-rating-image-${props.initialClient.clientId}`"
                v-model="ratingFile"
                accept="image/*"
                placeholder="kein Bild gewählt"
                drop-placeholder="Bild hierhin ziehen."
                :state="ratingImageState"
                @input="updateRatingFile"
            />
            <div v-if="client.ratingImageFileData" class="mt-3 position-relative" style="max-width: 50px;">
                <div
                    class="cursor-pointer position-absolute top-0 start-100 translate-middle"
                    @click="client.ratingImageFileData = null; client.ratingImageFileName = null;"
                >
                    <v-icon>mdi-close-circle-outline</v-icon>
                </div>
                <b-img :src="client.ratingImageFileData" alt="Rating-Bild" thumbnail fluid width="50" height="50" />
            </div>
            <v-alert color="info" class="mt-2">
                Vorschau:
                <div class="bg-white p-2 text-black">
                    <walk-rating :rating="3" :client="client" />
                </div>
            </v-alert>
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
        <form-error v-if="isInitialForm" :error="errorCreate" />
        <form-error v-else :error="errorChange" />
    </b-form>
</template>

<style scoped lang="scss">
</style>
