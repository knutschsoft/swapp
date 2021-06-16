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
                @submit="handleSubmit"
            />
        </content-collapse>
    </div>
</template>

<script>
    "use strict";
    import WayPointDetailData from './WayPoint/WayPointDetailData';
    import ContentCollapse from './ContentCollapse.vue';
    import WayPointForm from './WayPoint/WayPointForm.vue';

    export default {
        name: "WayPointDetail",
        components: {
            WayPointForm,
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
                redirectToastId: 'way-point-detail-redirect-toast',
            };
        },
        computed: {
            isAdmin() {
                return this.$store.getters['security/isAdmin'];
            },
            walk() {
                return this.$store.getters["walk/getWalkById"](this.walkId);
            },
            wayPoint() {
                return this.$store.getters["wayPoint/getWayPointById"](this.wayPointId);
            },
            title() {
                return `Wegpunkt: ${this.wayPoint.locationName} <small>vom ${(new Date(this.walk.startTime)).toLocaleDateString('de-DE', { weekday: 'short', year: 'numeric', month: '2-digit', day: '2-digit' })}</small>`;
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
            hasWalks() {
                return this.$store.getters["walk/hasWalks"];
            },
            hasTags() {
                return this.$store.getters["tag/hasTags"];
            },
            walks() {
                return this.$store.getters["walk/walks"];
            },
        },
        watch: {},
        async mounted() {
            const promises = [];
            if (!this.walk) {
                promises.push(this.$store.dispatch('walk/findById', this.walkId));
            }
            if (!this.wayPoint) {
                promises.push(this.$store.dispatch('wayPoint/findById', this.wayPointId));
            }
            if (!this.hasTags) {
                promises.push(this.$store.dispatch('tag/findAll'));
            }
            await Promise.all(promises);
            if (!this.walk || !this.wayPoint) {
                this.$router.push({ name: 'Dashboard', params: { redirect: 'Dieser Wegpunkt oder diese Runde existiert nicht. Du wurdest auf das Dashboard weitergeleitet.' } });
            }
        },
        methods: {
            async handleSubmit(payload) {
                payload.wayPoint = this.wayPoint['@id'];
                const wayPoint = await this.$store.dispatch('wayPoint/change', payload);
                if (wayPoint) {
                    const message = `Der Wegpunkt "${wayPoint.locationName}" wurde erfolgreich geändert.`;
                    this.$bvToast.toast(message, {
                        title: 'Wegpunkt geändert',
                        toaster: 'b-toaster-top-right',
                        autoHideDelay: 10000,
                        appendToast: true,
                        solid: true,
                    });

                    this.resetEditModal();
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
