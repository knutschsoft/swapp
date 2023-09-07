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
            v-if="walk && wayPoint && isAdmin"
            title="Wegpunkt bearbeiten"
            collapse-key="way-point-edit"
            is-visible-by-default
        >
            <way-point-form
                submit-button-text="Wegpunkt speichern"
                :initial-way-point="wayPoint"
                :error="changeError"
                @submit="handleSubmit"
            />
        </content-collapse>
        <content-collapse
            v-if="walk && wayPoint && isAdmin"
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
    import WayPointDetailData from './WayPoint/WayPointDetailData';
    import ContentCollapse from './ContentCollapse.vue';
    import WayPointForm from './WayPoint/WayPointForm.vue';
    import WayPointRemoveForm from './WayPoint/WayPointRemoveForm.vue';
    import { useTagStore } from '../stores/tag';
    import { useWayPointStore } from '../stores/way-point';
    import { useWalkStore } from '../stores/walk';
    import { useAuthStore } from '../stores/auth';

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
                authStore: useAuthStore(),
                tagStore: useTagStore(),
                wayPointStore: useWayPointStore(),
                walkStore: useWalkStore(),
                redirectToastId: 'way-point-detail-redirect-toast',
            };
        },
        computed: {
            isAdmin() {
                return this.authStore.isAdmin;
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
                this.$router.push({ name: 'Dashboard', params: { redirect: 'Dieser Wegpunkt oder diese Runde existiert nicht. Du wurdest auf das Dashboard weitergeleitet.' } });
            }
        },
        methods: {
            async handleRemove({wayPoint}) {
                await this.wayPointStore.remove({wayPoint: wayPoint['@id']});
                if (!this.changeError) {
                    const message = `Der Wegpunkt "${wayPoint.locationName}" wurde erfolgreich gelöscht.`;
                    this.$bvToast.toast(message, {
                        title: 'Wegpunkt gelöscht',
                        toaster: 'b-toaster-top-right',
                        variant: 'success',
                        autoHideDelay: 10000,
                        appendToast: true,
                        solid: true,
                    });

                    this.$router.push({name: 'WalkDetail', params: { walkId: this.walk.walkId }});
                } else {
                    this.$bvToast.toast('Upps! :-(', {
                        title: 'Wegpunkt löschen fehlgeschlagen',
                        toaster: 'b-toaster-top-right',
                        autoHideDelay: 10000,
                        variant: 'danger',
                        appendToast: true,
                        solid: true,
                    });
                }
            },
            async handleSubmit({form}) {
                form.wayPoint = this.wayPoint['@id'];
                const wayPoint = await this.wayPointStore.change(form);
                if (wayPoint) {
                    const message = `Der Wegpunkt "${wayPoint.locationName}" wurde erfolgreich geändert.`;
                    this.$bvToast.toast(message, {
                        title: 'Wegpunkt geändert',
                        toaster: 'b-toaster-top-right',
                        variant: 'success',
                        autoHideDelay: 10000,
                        appendToast: true,
                        solid: true,
                    });
                } else {
                    this.$bvToast.toast('Upps! :-(', {
                        title: 'Wegpunkt ändern fehlgeschlagen',
                        toaster: 'b-toaster-top-right',
                        autoHideDelay: 10000,
                        variant: 'danger',
                        appendToast: true,
                        solid: true,
                    });
                }
            },
        },
    }
</script>

<style scoped>

</style>
