<template>
    <div>
        <content-collapse
            v-if="walk && wayPoint"
            :title="title"
            collapse-key="way-point-detail-data"
            is-visible-by-default
        >
            <WayPointDetailData
                :walk-id="walkId"
                :way-point-id="wayPointId"
            />
        </content-collapse>
        <content-collapse
            v-if="walk && wayPoint && isAllowedToEdit"
            title="Wegpunkt bearbeiten"
            collapse-key="way-point-edit"
            is-visible-by-default
        >
            <way-point-form
                submit-button-text="Wegpunkt speichern"
                :initial-way-point="wayPoint"
                :error="changeError"
                @submitted="handleSubmit"
            />
        </content-collapse>
        <content-collapse
            v-if="walk && wayPoint && isAllowedToDelete"
            title="Wegpunkt löschen"
            collapse-key="way-point-delete"
            is-visible-by-default
        >
            <way-point-remove-form
                submit-button-text="Wegpunkt löschen"
                :initial-way-point="wayPoint"
                :initial-walk="walk"
                :error="changeError"
                @remove="handleRemove"
            />
        </content-collapse>
    </div>
</template>

<script>
    "use strict";
    import WayPointDetailData from './WayPoint/WayPointDetailData.vue';
    import ContentCollapse from './ContentCollapse.vue';
    import WayPointForm from './WayPoint/WayPointForm.vue';
    import WayPointRemoveForm from './WayPoint/WayPointRemoveForm.vue';
    import {useAlertStore, useAuthStore, useTagStore, useWalkStore, useWayPointStore} from '../stores';

    export default {
        name: "WayPointDetail",
        components: {
            WayPointForm,
            WayPointRemoveForm,
            ContentCollapse,
            WayPointDetailData,
        },
        props: {
            walkId: {
                required: true
            },
            wayPointId: {
                required: true
            },
        },
        data: function () {
            return {
                alertStore: useAlertStore(),
                authStore: useAuthStore(),
                tagStore: useTagStore(),
                wayPointStore: useWayPointStore(),
                walkStore: useWalkStore(),
                redirectToastId: 'way-point-detail-redirect-toast',
            };
        },
        computed: {
            isAllowedToEdit() {
                return this.authStore.isAdmin || this.authStore.currentUser['@id'] === this.walk.walkCreator;
            },
            isAllowedToDelete() {
                return this.authStore.isAdmin || this.authStore.currentUser['@id'] === this.walk.walkCreator;
            },
            walk() {
                return this.walkStore.getWalkById(this.walkId);
            },
            wayPoint() {
                return this.wayPointStore.getWayPointById(this.wayPointId);
            },
            title() {
                return `Wegpunkt: ${this.wayPoint.locationName} <small>vom ${(new Date(this.wayPoint.visitedAt)).toLocaleDateString('de-DE', { weekday: 'short', year: 'numeric', month: '2-digit', day: '2-digit' })}</small>`;
            },
            isLoading() {
                return this.walkStore.isLoading;
            },
            hasError() {
                return this.walkStore.hasError;
            },
            error() {
                return this.walkStore.getErrors;
            },
            changeError() {
                return this.wayPointStore.getErrors.change;
            },
            hasWalks() {
                return this.walkStore.hasWalks;
            },
            hasTags() {
                return this.tagStore.hasTags;
            },
            walks() {
                return this.walkStore.getWalks;
            },
        },
        watch: {},
        async mounted() {
            const promises = [];
            if (!this.walk) {
                promises.push(this.walkStore.fetchById(this.walkId));
            }
            if (!this.wayPoint) {
                promises.push(this.wayPointStore.fetchById(this.wayPointId));
            }
            if (!this.hasTags) {
                promises.push(this.tagStore.fetchTags());
            }
            await Promise.all(promises);
            if (!this.walk || !this.wayPoint) {
                this.$router.push({ name: 'Dashboard', query: { redirect: 'Dieser Wegpunkt oder diese Runde existiert nicht. Du wurdest auf das Dashboard weitergeleitet.' } });
            }
        },
        methods: {
            async handleRemove({wayPoint}) {
                await this.wayPointStore.remove({wayPoint: wayPoint['@id']});
                if (!this.changeError) {
                    this.alertStore.success(`Der Wegpunkt "${wayPoint.locationName}" wurde erfolgreich gelöscht.`, 'Wegpunkt gelöscht');
                    this.$router.push({name: 'WalkDetail', params: { walkId: this.walk.walkId }});
                } else {
                    this.alertStore.error('Wegpunkt löschen fehlgeschlagen', 'Upps! :-(');
                }
            },
            async handleSubmit({form}) {
                form.wayPoint = this.wayPoint['@id'];
                const wayPoint = await this.wayPointStore.change(form);
                if (wayPoint) {
                    this.alertStore.success(`Der Wegpunkt "${wayPoint.locationName}" wurde erfolgreich geändert.`, 'Wegpunkt geändert');
                } else {
                    this.alertStore.error('Wegpunkt ändern fehlgeschlagen', 'Upps! :-(');
                }
            },
        },
    }
</script>

<style scoped>

</style>
