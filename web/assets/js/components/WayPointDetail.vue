<template>
    <div>
        <content-collapse
            v-if="walk && wayPoint"
            :title="title"
            collapse-key="way-point--detail-data"
            is-visible-by-default
        >
            <WayPointDetailData
                :walk-id="walkId"
                :way-point-id="wayPointId"
            />
        </content-collapse>
    </div>
</template>

<script>
    "use strict";
    import WayPointDetailData from './WayPointDetail/WayPointDetailData';
    import ContentCollapse from './ContentCollapse.vue';

    export default {
        name: "WayPointDetail",
        components: {
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
            await Promise.all(promises);
            if (!this.walk || !this.wayPoint) {
                this.$router.push({ name: 'Dashboard', params: { redirect: 'Dieser Wegpunkt oder diese Runde existiert nicht. Du wurdest auf das Dashboard weitergeleitet.' } });
            }
        },
        methods: {
        },
    }
</script>

<style scoped>

</style>
