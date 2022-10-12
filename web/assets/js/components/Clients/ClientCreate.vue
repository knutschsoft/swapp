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
export default {
    name: 'ClientCreate',
    components: {
        ClientForm,
    },
    data: function () {
        return {
        };
    },
    computed: {
        currentUser() {
            return this.$store.getters['security/currentUser'];
        },
    },
    async created() {
    },
    methods: {
        async handleSubmit(payload) {
            const client = await this.$store.dispatch('client/create', payload);
            if (client) {
                const message = `Der Klient "${client.name}" wurde erfolgreich erstellt.`;
                this.$bvToast.toast(message, {
                    title: 'Klient erstellt',
                    toaster: 'b-toaster-top-right',
                    autoHideDelay: 10000,
                    appendToast: true,
                    solid: true,
                });
                this.$refs.clientForm.resetForm();
            } else {
                this.$bvToast.toast('Upps! :-(', {
                    title: 'Klient erstellen fehlgeschlagen',
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
