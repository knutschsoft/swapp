<template>
    <user-form
        submit-button-text="Neuen Benutzer erstellen"
        :initial-user="{ client: initialClient, user: ['ROLE_USER'] }"
        ref="userForm"
        @submit="handleSubmit"
    />
</template>

<script>
'use strict';

import UserForm from './UserForm.vue';
import {useAlertStore, useAuthStore, useUserStore} from '../../stores';

export default {
    name: 'UserCreate',
    components: {
        UserForm,
    },
    data: function () {
        return {
            alertStore: useAlertStore(),
            authStore: useAuthStore(),
            userStore: useUserStore(),
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
            const user = await this.userStore.create(payload);
            if (user) {
                this.$refs.userForm.resetForm();
                this.alertStore.success(
                    `Der Benutzer "${user.username}" wurde erfolgreich erstellt. Er hat eine E-Mail an "${user.email}" mit seinen Kontoinformationen erhalten.`,
                    'Benutzer erstellt'
                );
            } else {
                this.alertStore.error('Benutzer erstellen fehlgeschlagen', 'Upps! :-(');
            }
        },
    },
};
</script>

<style scoped lang="scss">
</style>
