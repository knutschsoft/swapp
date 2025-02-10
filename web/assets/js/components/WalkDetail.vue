<template>
    <div>
        <v-alert
            v-if="walk && walk.isUnfinished"
            prominent
            density="compact"
            type="info"
        >
            Die Runde ist noch nicht abgeschlossen.
            <v-btn
                color="secondary"
                class="ml-2"
                :to="{name:'WalkAddWayPoint', params: {walkId: walkId}}"
            >
                Runde fortsetzen
            </v-btn>
        </v-alert>
        <content-collapse
            :title="title"
            collapse-key="walk-detail"
            is-visible-by-default
            :is-loading="!walk"
        >
            <WalkDetailData
                :walk-id="walkId"
            />
        </content-collapse>
        <content-collapse
            :title="`Wegpunkte der Runde &quot;${walk?.name}&quot;`"
            collapse-key="waypoints-of-round"
            is-visible-by-default
            :is-loading="!walk"
        >
            <WayPointList
                :walk-id="walkId"
            />
        </content-collapse>
        <content-collapse
            v-if="isAllowedToEdit && walk"
            :title="`Runde &quot;${walk.name}&quot; ändern`"
            collapse-key="walk-edit"
            is-visible-by-default
            :is-loading="!walk"
        >
            <walk-form
                v-if="!walk.isUnfinished"
                submit-button-text="Runde speichern"
                :initial-walk="walk"
                @submitted="handleSubmit"
            />
            <walk-unfinished-form
                v-else
                submit-button-text="Runde speichern"
                :initial-walk="walk"
                @submitted="handleWalkUnfinishedSubmit"
            />
        </content-collapse>
        <content-collapse
            v-if="walk && isAllowedToDelete"
            title="Runde löschen"
            collapse-key="walk-delete"
            is-visible-by-default
        >
            <walk-remove-form
                submit-button-text="Runde löschen"
                :initial-walk="walk"
                :error="changeError"
                @remove="handleRemove"
            />
        </content-collapse>
    </div>
</template>

<script>
    "use strict";
    import Error from './Error.vue';
    import WalkDetailData from './Walk/WalkDetailData.vue';
    import WayPointList from './Walk/WayPointList.vue';
    import ContentCollapse from './ContentCollapse.vue';
    import WalkForm from './Walk/WalkForm.vue';
    import WalkUnfinishedForm from './Walk/WalkUnfinishedForm.vue';
    import WalkRemoveForm from './Walk/WalkRemoveForm.vue';
    import {useAlertStore, useAuthStore, useClientStore, useUserStore, useWayPointStore, useWalkStore, useTagStore} from '../stores';

    export default {
        name: "WalkDetail",
        components: {
            WalkForm,
            WalkRemoveForm,
            WalkUnfinishedForm,
            ContentCollapse,
            WayPointList,
            WalkDetailData,
            Error,
        },
        props: {
            walkId: {
                required: true
            }
        },
        data: function () {
            return {
                alertStore: useAlertStore(),
                authStore: useAuthStore(),
                clientStore: useClientStore(),
                tagStore: useTagStore(),
                userStore: useUserStore(),
                walkStore: useWalkStore(),
                wayPointStore: useWayPointStore(),
            }
        },
        computed: {
            isAllowedToEdit() {
                return this.authStore.isAdmin || this.walk && this.authStore.currentUser['@id'] === this.walk.walkCreator;
            },
            isAllowedToDelete() {
                return this.authStore.isAdmin || this.walk && this.authStore.currentUser['@id'] === this.walk.walkCreator;
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
                return this.walkStore.getErrors.change;
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
            title() {
                if (!this.walk) {
                    return '';
                }

                return `Streetwork-Runde: "${this.walk.name}" <small>von ${(new Date(this.walk.startTime)).toLocaleDateString('de-DE', { weekday: 'short', year: 'numeric', month: '2-digit', day: '2-digit' })}</small>`;
            },
            walk() {
                return this.walkStore.getWalkById(this.walkId);
            },
        },
        watch: {},
        async mounted() {
            await this.walkStore.resetChangeError();
            if (!this.walk) {
                await this.walkStore.fetchById(this.walkId);
            }
            if (!this.walk) {
                this.$router.push({ name: 'Dashboard', query: { redirect: 'Diese Runde existiert nicht. Du wurdest auf das Dashboard weitergeleitet.' } });
                return;
            }
            const promises = [
                this.clientStore.fetchByIri(this.walk.client),
            ];
            if (!this.hasTags) {
                promises.push(this.tagStore.fetchTags());
            }
            await Promise.all(promises);

            let wayPointPromises = [];
            let wayPointPromiseIds = [];
            this.walk.wayPoints.forEach(wayPointIri => {
                if (!this.getWayPointByIri(wayPointIri)) {
                    const id = wayPointIri.replace('/api/way_points/', '');
                    if (!wayPointPromiseIds.includes(id)) {
                        wayPointPromises.push(this.wayPointStore.fetchByIri(wayPointIri));
                        wayPointPromiseIds.push(id);
                    }
                }
            });
            wayPointPromises.push(this.userStore.fetchUsers());
            await Promise.all(wayPointPromises);
        },
        methods: {
            async handleRemove({walk}) {
                await this.walkStore.remove({walk: walk['@id']});
                if (!this.changeError) {
                    this.alertStore.success(`Die Runde "${walk.name}" wurde erfolgreich gelöscht.`, 'Runde gelöscht');
                    this.$router.push({name: 'Dashboard'});
                } else {
                    this.alertStore.success('Runde löschen fehlgeschlagen', 'Upps! :-(');
                }
            },
            getWayPointByIri(iri) {
                return this.wayPointStore.getWayPointByIri(iri);
            },
            async handleSubmit(payload) {
                payload.walk = this.walk['@id'];
                const walk = await this.walkStore.change(payload);
                if (walk) {
                    this.alertStore.success(`Die Runde "${walk.name}" wurde erfolgreich geändert.`, 'Runde geändert');
                } else {
                    this.alertStore.error('Runde ändern fehlgeschlagen', 'Upps! :-(');
                }
            },
            async handleWalkUnfinishedSubmit(payload) {
                payload.walk = this.walk['@id'];
                const walk = await this.walkStore.changeUnfinished(payload);
                if (walk) {
                    this.alertStore.success(`Die Runde "${walk.name}" wurde erfolgreich geändert.`, 'Runde geändert');
                } else {
                    this.alertStore.error('Runde ändern fehlgeschlagen', 'Upps! :-(');
                }
            },
        },
    }
</script>

<style scoped>

</style>
