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
import { useAuthStore } from '../../stores/auth';
import { useSystemicQuestionStore } from '../../stores/systemic-question';
export default {
    name: 'SystemicQuestionCreate',
    components: {
        SystemicQuestionForm,
    },
    data: function () {
        return {
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
                const message = `Die systemische Frage "${systemicQuestion.question}" wurde erfolgreich erstellt.`;
                this.$bvToast.toast(message, {
                    title: 'Systemische Frage erstellt',
                    toaster: 'b-toaster-top-right',
                    autoHideDelay: 10000,
                    appendToast: true,
                    solid: true,
                });

                this.initialQuestion = null;
            } else {
                this.$bvToast.toast('Upps! :-(', {
                    title: 'Systemische Frage erstellen fehlgeschlagen',
                    toaster: 'b-toaster-top-right',
                    autoHideDelay: 10000,
                    variant: 'danger',
                    appendToast: true,
                    solid: true,
                });
            }
        },
    },
};
</script>

<style scoped lang="scss">
</style>
