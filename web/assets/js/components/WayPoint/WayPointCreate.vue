<template>
    <way-point-form
        submit-button-text="Wegpunkt speichern und weiteren Wegpunkt hinzufügen"
        :initial-walk="walk"
        :key="componentKey"
        @submit="handleSubmit"
    />
</template>

<script>
'use strict';

import WayPointForm from './WayPointForm.vue';
import {useAlertStore, useWayPointStore} from '../../stores';

export default {
    name: 'WayPointCreate',
    components: {
        WayPointForm,
    },
    props: {
        walk: {
            required: true,
            type: Object,
        },
    },
    data: function () {
        return {
            alertStore: useAlertStore(),
            wayPointStore: useWayPointStore(),
            componentKey: 0,
        };
    },
    computed: {
        currentUser() {
            return this.authStore.currentUser;
        },
    },
    async created() {
        this.initialWalk = this.walk;
    },
    methods: {
        forceRerender() {
            this.componentKey += 1;
        },
        async handleSubmit({ form, isWithFinish }) {
            const wayPoint = await this.wayPointStore.create(form);
            if (wayPoint) {
                let message = `Der Wegpunkt "${wayPoint.locationName}" wurde erfolgreich zur Runde hinzugefügt.`;
                if (isWithFinish) {
                    message += ' Die Runde kann jetzt abgeschlossen werden';
                }
                this.alertStore.success(message, 'Wegpunkt erstellt');
                if (isWithFinish) {
                    this.$router.push({
                        name: 'WalkEpilogue',
                        params: { walkId: this.walk.walkId, successMessage: 'Wegpunkt erfolgreich hinzugefügt. Die Runde kann jetzt abgeschlossen werden.' },
                    });
                } else {
                    this.forceRerender();
                    window.scrollTo({
                        top: 0,
                        left: 0,
                        behavior: 'smooth',
                    });
                }
            } else {
                this.alertStore.error('Wegpunkt erstellen fehlgeschlagen', 'Upps! :-(');
            }
        },
    },
};
</script>

<style scoped lang="scss">
</style>
