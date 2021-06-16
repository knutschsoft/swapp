<template>
    <way-point-form
        submit-button-text="Neuen Wegpunkt erstellen"
        :initial-client="{}"
        @submit="handleSubmit"
    />
</template>

<script>
'use strict';

import WayPointForm from './WayPointForm.vue';
export default {
    name: 'WayPointCreate',
    components: {
        WayPointForm,
    },
    data: function () {
        return {
        };
    },
    computed: {
        currentUser() {
            return this.$store.getters['security/currentUser'];
        },
        initialWayPoint() {
            return this.currentUser.wayPoint;
        },
    },
    async created() {
    },
    methods: {
        async handleSubmit(payload) {
            const wayPoint = await this.$store.dispatch('wayPoint/create', payload);
            if (wayPoint) {
                const message = `Der Klient "${wayPoint.name}" wurde erfolgreich erstellt.`;
                this.$bvToast.toast(message, {
                    title: 'Klient erstellt',
                    toaster: 'b-toaster-top-right',
                    autoHideDelay: 10000,
                    appendToast: true,
                    solid: true,
                });
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
