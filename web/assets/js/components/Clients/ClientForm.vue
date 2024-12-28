<script lang="ts" setup>
import { ref, computed, watch, defineProps, defineEmits, onMounted } from 'vue';
import axios from 'axios';
import * as EmailValidator from 'email-validator';
import FormError from '../Common/FormError.vue';
import WalkRating from '../Walk/WalkRating.vue';
// import { getViolationsFeedback } from '../../utils/validation';
import { useClientStore } from '../../stores/client';
import { Client } from '../../model';

const props = defineProps({
    initialClient: {
        type: Object as Client,
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
// const ratingImageState = computed(() => !!client.value.ratingImageFileData && invalidRatingImageFeedback.value === '');
// const invalidRatingImageFeedback = computed(() => {
//     const error = isInitialForm ? errorCreate.value : errorChange.value;
//     return getViolationsFeedback(['decodedRatingImageData', 'ratingImageFileData', 'ratingImageFileName'], error)
// });
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
    <v-form ref="form" @submit.prevent="handleSubmit" class="pa-4">
        <v-row dense>
            <v-col cols="12">
                <v-text-field
                    v-model="client.name"
                    label="Name"
                    required
                    :rules="[() => !!nameState || 'Der Name muss zwischen 3 and 200 Zeichen enthalten.']"
                    outlined
                    dense
                />
            </v-col>

            <v-col cols="12">
                <v-text-field
                    v-model="client.email"
                    label="E-Mail"
                    required
                    :rules="[() => !!emailState || 'Ungültiges E-Mail-Format.']"
                    outlined
                    dense
                />
            </v-col>

            <v-col cols="12">
                <v-textarea
                    v-model="client.description"
                    label="Beschreibung"
                    :rules="[() => !!descriptionState || 'Die Beschreibung muss weniger als 10000 Zeichen enthalten.']"
                    outlined
                    dense
                />
            </v-col>

            <v-col
                :cols="client.ratingImageFileData ? 9 : 12"
            >
                <v-file-input
                    v-model="ratingFile"
                    label="Rating-Bild"
                    accept="image/*"
                    placeholder="Kein Bild gewählt"
                    :disabled="isLoading"
                    outlined
                    dense
                    @change="updateRatingFile"
                />
            </v-col>
            <v-col v-if="client.ratingImageFileData" cols="3">
                <div class="d-flex align-center mb-4 justify-center">
                    <v-avatar size="50" :tile="false">
                        <img :src="client.ratingImageFileData" alt="Rating-Bild" />
                    </v-avatar>
                    <v-btn class="align-self-start ml-n4 mt-n4" icon @click="client.ratingImageFileData = null">
                        <v-icon>mdi-close-circle</v-icon>
                    </v-btn>
                </div>
            </v-col>
        </v-row>

        <v-alert type="info" text>
            Vorschau:
            <walk-rating :rating="3" :client="client" />
        </v-alert>

        <v-btn :disabled="isFormInvalid" color="secondary" block type="submit">
            {{ submitButtonText }}
        </v-btn>

        <form-error v-if="isInitialForm" :error="errorCreate" />
        <form-error v-else :error="errorChange" />
    </v-form>
</template>

<style scoped lang="scss">
</style>
