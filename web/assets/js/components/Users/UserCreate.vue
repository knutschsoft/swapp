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
import { useUserStore } from '../../stores/user';
export default {
    name: 'UserCreate',
    components: {
        UserForm,
    },
    data: function () {
        return {
            userStore: useUserStore(),
        };
    },
    computed: {
        currentUser() {
            return this.$store.getters['security/currentUser'];
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
                const message = `Der Benutzer "${user.username}" wurde erfolgreich erstellt. Er hat eine E-Mail an "${user.email}" mit seinen Kontoinformationen erhalten.`;
                this.$bvToast.toast(message, {
                    title: 'Benutzer erstellt',
                    toaster: 'b-toaster-top-right',
                    autoHideDelay: 10000,
                    appendToast: true,
                    solid: true,
                });
            } else {
                this.$bvToast.toast('Upps! :-(', {
                    title: 'Benutzer erstellen fehlgeschlagen',
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
