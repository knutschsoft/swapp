<template>
    <div>
        <b-button
            v-if="walk && walk.wayPoints.length"
            :to="{name: 'WalkEpilogue', params: { walkId: walk.id } }"
            class="btn btn-secondary mt-1 mt-sm-2 mt-lg-3"
            :disabled="isLoading"
            block
        >
            Runde abschließen (kein weiterer Wegpunkt)
        </b-button>

        <content-collapse
            :title="`Wegpunkt zur Runde &quot;${walk.name}&quot; hinzufügen`"
            collapse-key="walk-add-waypoint"
            :is-loading="!walk"
            is-visible-by-default
        >
            <WayPointAdd
                v-if="walk"
                :walk="walk"
            />
        </content-collapse>

        <content-collapse
            :title="`Wegpunkte der Runde ${walk.name}`"
            :is-loading="!walk"
            collapse-key="waypoints-of-round"
            is-visible-by-default
        >
            <WayPointList
                :walk-id="walkId"
            />
        </content-collapse>
    </div>
</template>

<script>
    "use strict";
    import WayPointList from './WalkDetail/WayPointList';
    import ContentCollapse from './ContentCollapse.vue';
    import WayPointAdd from './WalkDetail/WayPointAdd.vue';

    export default {
        name: "WalkAddWayPoint",
        components: {
            WayPointAdd,
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
            }
        },
        computed: {
            isLoading() {
                return this.$store.getters["walk/isLoading"];
            },
            hasWalks() {
                return this.$store.getters["walk/hasWalks"];
            },
            walk() {
                return this.$store.getters["walk/getWalkById"](this.walkId);
            },
            walks() {
                return this.$store.getters["walk/walks"];
            },
        },
        watch: {},
        mounted() {
            this.$store.dispatch('walk/findById', this.walkId);
        },
        methods: {
        },
    }
</script>

<style scoped>

</style>
