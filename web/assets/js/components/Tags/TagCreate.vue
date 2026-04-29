<template>
    <v-form
        @submit.prevent="handleSubmit"
        class="pa-1 pa-sm-2 pa-md-4 pa-lg-5 pa-xl-6 pa-xxl-7"
    >
        <v-text-field
            v-model="name"
            required
            minlength="3"
            maxlength="100"
            placeholder="Name des Tags"
            :state="nameState"
            label="Name"
            variant="outlined"
            density="compact"
            data-test="name"
        />
        Gewählte Tag-Farbe:
        <v-select
            v-model="color"
            :items="availableColors"
            placeholder="Farbe des Tags"
            data-test="farbe"
            required
            variant="outlined"
            density="compact"
            item-value="name"
            item-title="name"
            label="Farbe"
        >
            <template v-slot:selection="{ item, index }">
                <color-badge
                    :color="item.title"
                />
            </template>
            <template v-slot:item="{props, item}">
                <v-divider></v-divider>
                <v-list-item v-bind="props">
                    <color-badge
                        :color="item.title"
                    />
                </v-list-item>
            </template>
        </v-select>
        <client-select
            v-if="isSuperAdmin"
            v-model="client"
            :is-loading="isLoading"
            :disabled="isLoading"
            :clearable="false"
            class="mb-4"
        />
        <v-tooltip
            v-if="isFormInvalid"
            top
        >
            <template v-slot:activator="{ props }">
                <div
                    v-bind="props"
                >
                    <v-btn
                        type="submit"
                        color="secondary"
                        data-test="button-tag-create"
                        :disabled="isFormInvalid || isLoading"
                        block
                        class="text-transform-none"
                        density="comfortable"
                        :loading="isLoading"
                    >
                        Neuen Tag erstellen
                    </v-btn>
                </div>
            </template>
            <span>
                Bitte erst Name und Farbe wählen bevor ein neuer Tag erstellt werden kann.
            </span>
        </v-tooltip>
        <v-btn
            v-else
            type="submit"
            color="secondary"
            data-test="button-tag-create"
            :disabled="isFormInvalid || isLoading"
            class="text-transform-none"
            density="comfortable"
            :loading="isLoading"
            block
        >
            Neuen Tag erstellen
        </v-btn>
        <form-error
            :error="error"
        />
        <v-alert
            class="w-100 text-muted mt-2 mb-0"
        >
            <b>Hinweis:</b>
            <ul class="mb-0">
                <li>Ein Tag ist nach dem Erstellen standardmäßig aktiviert.</li>
                <li>Aktivierte Tags können einem Wegpunkt zugeordnet werden.</li>
                <li>Deaktivierte Tags können einem Wegpunkt nicht zugeordnet werden. Sie sind jedoch weiterhin an bereits zugeordneten Wegpunkten vorhanden.</li>
                <li>Deaktivierte Tags werden nicht als Filter auf dem Dashboard angezeigt, wenn sie keinem Wegpunkt zugeordnet sind.</li>
            </ul>
        </v-alert>
    </v-form>
</template>

<script>
'use strict';
import ColorBadge from './ColorBadge.vue';
import { availableColors } from './availableColors';
import FormError from '../Common/FormError.vue';
import {useAlertStore, useAuthStore, useClientStore, useTagStore} from '../../stores';
import {ClientSelect} from "@/js/components/Common";

export default {
    name: 'TagCreate',
    components: {
        ClientSelect,
        FormError,
        ColorBadge,
    },
    data: function () {
        return {
            alertStore: useAlertStore(),
            authStore: useAuthStore(),
            tagStore: useTagStore(),
            clientStore: useClientStore(),
            name: null,
            color: null,
            client: '',
        };
    },
    computed: {
        colors() {
            return this.tagStore.getTags.map(tag => tag.color);
        },
        names() {
            return this.tagStore.getTags.map(tag => tag.name);
        },
        colorState() {
            if (null === this.color) {
                return;
            }

            return -1 === this.colors.indexOf(this.color);
        },
        nameState() {
            if (null === this.name) {
                return;
            }

            return -1 === this.names.indexOf(this.name);
        },
        isLoading() {
            return this.tagStore.isLoading;
        },
        currentUser() {
            return this.authStore.currentUser;
        },
        isSuperAdmin() {
            return this.authStore.isSuperAdmin;
        },
        isFormInvalid() {
            return !this.name || !this.color || !this.colorState || !this.nameState || this.isLoading;
        },
        error() {
            return this.tagStore.getErrors.create;
        },
        availableColors() {
            // availableColors ist bereits gefiltert/sortiert (siehe ./availableColors.ts).
            // Hier nur noch Farben rauswerfen, die schon als Tag verwendet werden.
            return availableColors.filter(c => -1 === this.colors.indexOf(c.name));
        },
    },
    async created() {
        await this.clientStore.fetchClients();
        this.client = this.currentUser.client;
    },
    methods: {
        async handleSubmit() {
            if (this.isFormInvalid) {
                return false;
            }
            let payload = {
                name: this.name,
                color: this.color,
                client: this.client,
            };

            const tag = await this.tagStore.create(payload);
            if (tag) {
                this.resetForm();
                this.alertStore.success(`Der Tag ${tag.name} (${tag.color}) wurde erfolgreich erstellt.`, 'Tag erstellt');
                this.tagStore.fetchTags();

                this.name = null;
                this.color = null;
            } else {
                this.alertStore.error('Tag erstellen fehlgeschlagen', 'Upps! :-(');
            }
        },
        resetForm() {
            this.name = null;
            this.color = null;
        },
    },
};
</script>

<style scoped lang="scss">
</style>
