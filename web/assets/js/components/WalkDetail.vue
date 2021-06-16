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
    </div>
</template>

<script>
    "use strict";
    import Error from './Error';
    import WalkDetailData from './WalkDetail/WalkDetailData';
    import WayPointList from './WalkDetail/WayPointList';
    import ContentCollapse from './ContentCollapse.vue';

    export default {
        name: "WalkDetail",
        components: {
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
            title() {
                return `Streetwork-Runde: "${this.walk.name}" <small>von ${(new Date(this.walk.startTime)).toLocaleDateString('de-DE', { weekday: 'short', year: 'numeric', month: '2-digit', day: '2-digit' })}</small>`;
            },
            walk() {
                return this.$store.getters["walk/getWalkById"](this.walkId);
            },
        },
        watch: {},
        async mounted() {
            if (!this.walk) {
                await this.$store.dispatch('walk/findById', this.walkId);
            }
            if (!this.walk) {
                // return 404
            }

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
            getWayPointByIri(iri) {
                const id = iri.replace('/api/way_points/', '');

                return this.$store.getters['wayPoint/getWayPointById'](id);
            },
        },
    }
</script>

<style scoped>

</style>
