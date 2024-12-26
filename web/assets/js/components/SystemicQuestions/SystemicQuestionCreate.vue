<template>
    <systemic-question-form
        submit-button-text="Neue systemische Frage erstellen"
        @submit="handleSubmit"
        :initial-client="initialClient"
        ref="systemicQuestionForm"
    />
</template>

<script>
'use strict';

import SystemicQuestionForm from './SystemicQuestionForm.vue';
import {useAlertStore, useAuthStore, useSystemicQuestionStore} from '../../stores';

export default {
    name: 'SystemicQuestionCreate',
    components: {
        SystemicQuestionForm,
    },
    data: function () {
        return {
            alertStore: useAlertStore(),
            authStore: useAuthStore(),
            systemicQuestionStore: useSystemicQuestionStore(),
        };
    },
    computed: {
        currentUser() {
            return this.authStore.currentUser;
        },
        initialClient() {
            return this.currentUser.client;
        },
    },
    async created() {
    },
    methods: {
        async handleSubmit(payload) {
            const systemicQuestion = await this.systemicQuestionStore.create(payload);
            if (systemicQuestion) {
                this.$refs.systemicQuestionForm.resetForm();
                this.alertStore.success(`Die systemische Frage "${systemicQuestion.question}" wurde erfolgreich erstellt.`, 'Systemische Frage erstellt');
                this.initialQuestion = null;
            } else {
                this.alertStore.error('Systemische Frage erstellen fehlgeschlagen', 'Upps! :-(');
            }
        },
    },
};
</script>

<style scoped lang="scss">
</style>
