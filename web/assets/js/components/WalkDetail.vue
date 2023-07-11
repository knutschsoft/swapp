<template>
    <div class="">
        <b-alert
            :show="walk && walk.isUnfinished"
            class="mt-2 mb-0"
            variant="info"
        >
            <mdicon
                name="information-outline"
            />
            Die Runde ist noch nicht abgeschlossen.
            <b-button
                variant=""
                class="ml-2"
                :to="{name:'WalkAddWayPoint', params: {walkId: walkId}}"
            >
                Runde fortsetzen
            </b-button>
        </b-alert>
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
            :title="`Wegpunkte der Runde &quot;${walk.name}&quot;`"
            collapse-key="waypoints-of-round"
            is-visible-by-default
            :is-loading="!walk"
        >
            <WayPointList
                :walk-id="walkId"
            />
        </content-collapse>
        <content-collapse
            v-if="isAdmin && walk"
            :title="`Runde &quot;${walk.name}&quot; ändern`"
            collapse-key="walk-edit"
            is-visible-by-default
            :is-loading="!walk"
        >
            <walk-form
                v-if="!walk.isUnfinished"
                submit-button-text="Runde speichern"
                :initial-walk="walk"
                @submit="handleSubmit"
            />
            <walk-unfinished-form
                v-else
                submit-button-text="Runde speichern"
                :initial-walk="walk"
                @submit="handleWalkUnfinishedSubmit"
            />
        </content-collapse>
        <content-collapse
            v-if="walk && isAdmin"
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
    import Error from './Error';
    import WalkDetailData from './Walk/WalkDetailData';
    import WayPointList from './Walk/WayPointList';
    import ContentCollapse from './ContentCollapse.vue';
    import WalkForm from './Walk/WalkForm.vue';
    import WalkUnfinishedForm from './Walk/WalkUnfinishedForm.vue';
    import WalkRemoveForm from './Walk/WalkRemoveForm.vue';

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
            }
        },
        computed: {
            isAdmin() {
                return this.$store.getters['security/isAdmin'];
            },
            isLoading() {
                return this.$store.getters["walk/isLoading"];
            },
            hasError() {
                return this.$store.getters["walk/hasError"];
            },
            error() {
                return this.$store.getters["walk/error"];
            },
            changeError() {
                return this.$store.getters['walk/errorChange'];
            },
            hasWalks() {
                return this.$store.getters["walk/hasWalks"];
            },
            walks() {
                return this.$store.getters["walk/walks"];
            },
            title() {
                return `Streetwork-Runde: "${this.walk.name}" <small>von ${(new Date(this.walk.startTime)).toLocaleDateString('de-DE', { weekday: 'short', year: 'numeric', month: '2-digit', day: '2-digit' })}</small>`;
            },
            walk() {
                return this.$store.getters["walk/getWalkById"](this.walkId);
            },
        },
        watch: {},
        async mounted() {
            await this.$store.dispatch('walk/resetChangeError');
            if (!this.walk) {
                await this.$store.dispatch('walk/findById', this.walkId);
            }
            if (!this.walk) {
                this.$router.push({ name: 'Dashboard', params: { redirect: 'Diese Runde existiert nicht. Du wurdest auf das Dashboard weitergeleitet.' } });
                return;
            }
            await this.$store.dispatch('client/findByIri', this.walk.client);

            let wayPointPromises = [];
            let wayPointPromiseIds = [];
            this.walk.wayPoints.forEach(wayPointIri => {
                if (!this.getWayPointByIri(wayPointIri)) {
                    const id = wayPointIri.replace('/api/way_points/', '');
                    if (!wayPointPromiseIds.includes(id)) {
                        wayPointPromises.push(this.$store.dispatch('wayPoint/findById', id));
                        wayPointPromiseIds.push(id);
                    }
                }
            });
            wayPointPromises.push(this.$store.dispatch('user/findAll'));
            await Promise.all(wayPointPromises);
        },
        methods: {
            async handleRemove({walk}) {
                await this.$store.dispatch('walk/remove', walk);
                if (!this.changeError) {
                    const message = `Die Runde "${walk.name}" wurde erfolgreich gelöscht.`;
                    this.$bvToast.toast(message, {
                        title: 'Runde gelöscht',
                        toaster: 'b-toaster-top-right',
                        variant: 'success',
                        autoHideDelay: 10000,
                        appendToast: true,
                        solid: true,
                    });

                    this.$router.push({name: 'Dashboard'});
                } else {
                    this.$bvToast.toast('Upps! :-(', {
                        title: 'Runde löschen fehlgeschlagen',
                        toaster: 'b-toaster-top-right',
                        autoHideDelay: 10000,
                        variant: 'danger',
                        appendToast: true,
                        solid: true,
                    });
                }
            },
            getWayPointByIri(iri) {
                return this.$store.getters['wayPoint/getWayPointByIri'](iri);
            },
            async handleSubmit(payload) {
                payload.walk = this.walk['@id'];
                const walk = await this.$store.dispatch('walk/change', payload);
                if (walk) {
                    const message = `Die Runde "${walk.name}" wurde erfolgreich geändert.`;
                    this.$bvToast.toast(message, {
                        title: 'Runde geändert',
                        toaster: 'b-toaster-top-right',
                        autoHideDelay: 10000,
                        variant: 'success',
                        appendToast: true,
                        solid: true,
                    });
                } else {
                    this.$bvToast.toast('Upps! :-(', {
                        title: 'Runde ändern fehlgeschlagen',
                        toaster: 'b-toaster-top-right',
                        autoHideDelay: 10000,
                        variant: 'danger',
                        appendToast: true,
                        solid: true,
                    });
                }
            },
            async handleWalkUnfinishedSubmit(payload) {
                payload.walk = this.walk['@id'];
                const walk = await this.$store.dispatch('walk/changeUnfinished', payload);
                if (walk) {
                    const message = `Die Runde "${walk.name}" wurde erfolgreich geändert.`;
                    this.$bvToast.toast(message, {
                        title: 'Runde geändert',
                        toaster: 'b-toaster-top-right',
                        autoHideDelay: 10000,
                        variant: 'success',
                        appendToast: true,
                        solid: true,
                    });
                } else {
                    this.$bvToast.toast('Upps! :-(', {
                        title: 'Runde ändern fehlgeschlagen',
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
