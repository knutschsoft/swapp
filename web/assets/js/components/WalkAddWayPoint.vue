<template>
    <div>
        <b-button
            v-if="walk && walk.wayPoints.length"
            :to="{name: 'WalkEpilogue', params: { walkId: walk.walkId } }"
            class="btn btn-secondary mt-1 mt-sm-2 mt-lg-3"
            :disabled="isLoading"
            block
        >
            Runde abschließen (kein weiterer Wegpunkt)
        </b-button>

        <content-collapse
            :title="`Wegpunkt zur Runde &quot;${walk?.name}&quot; hinzufügen`"
            collapse-key="walk-add-waypoint"
            :is-loading="!walk"
            is-visible-by-default
        >
            <WayPointCreate
                v-if="walk"
                :walk="walk"
            />
        </content-collapse>

        <content-collapse
            :title="`Wegpunkte der Runde ${walk?.name} ${ totalWayPoints !== null ? `(${ totalWayPoints })` : '' }`"
            :is-loading="!walk"
            collapse-key="waypoints-of-round"
            is-visible-by-default
        >
            <WayPointList
                :walk-id="walkId"
                @refresh-total-way-points="updateTotalWayPoints"
            />
        </content-collapse>
    </div>
</template>

<script>
    "use strict";
    import WayPointList from './Walk/WayPointList';
    import ContentCollapse from './ContentCollapse.vue';
    import WayPointCreate from './WayPoint/WayPointCreate.vue';
    import { useWalkStore } from '../stores/walk';

    export default {
        name: "WalkAddWayPoint",
        components: {
            WayPointCreate,
            ContentCollapse,
            WayPointList,
        },
        props: {
            walkId: {
                required: true,
            }
        },
        data: function () {
            return {
                totalWayPoints: null,
                walkStore: useWalkStore(),
            };
        },
        computed: {
            isLoading() {
                return this.walkStore.isLoading;
            },
            hasWalks() {
                return this.walkStore.hasWalks;
            },
            walk() {
                return this.walkStore.getWalkById(this.walkId);
            },
            walks() {
                return this.walkStore.getWalks;
            },
        },
        watch: {},
        async mounted() {
            await this.walkStore.resetCreateError();
            if (!this.walk) {
                await this.walkStore.fetchById(this.walkId);
            }
            if (!this.walk) {
                this.$router.push({ name: 'Dashboard', params: { redirect: 'Diese Runde existiert nicht. Du wurdest auf das Dashboard weitergeleitet.' } });
            }
        },
        methods: {
            updateTotalWayPoints(totalWayPoints) {
                this.totalWayPoints = totalWayPoints;
            },
        },
    }
</script>

<style scoped>

</style>
