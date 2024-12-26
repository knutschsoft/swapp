<template>
    <client-form
        submit-button-text="Neuen Klienten erstellen"
        :initial-client="{}"
        ref="clientForm"
        @submit="handleSubmit"
    />
</template>

<script>
'use strict';

import ClientForm from './ClientForm.vue';
import { useAlertStore } from '../../stores/alert';
import { useClientStore } from '../../stores/client';

export default {
    name: 'ClientCreate',
    components: {
        ClientForm,
    },
    data: function () {
        return {
            alertStore: useAlertStore(),
            clientStore: useClientStore(),
        };
    },
    computed: {
        currentUser() {
            return this.authStore.currentUser;
        },
    },
    async created() {
    },
    methods: {
        async handleSubmit(payload) {
            const client = await this.clientStore.createClient(payload);
            if (client) {
                this.alertStore.success(`Der Klient "${client.name}" wurde erfolgreich erstellt.`);
                this.$refs.clientForm.resetForm();
            } else {
                this.alertStore.error(`Klient erstellen fehlgeschlagen`, `Upps! :-(`);


            }
        },
    },
};
</script>

<style scoped lang="scss">
</style>
