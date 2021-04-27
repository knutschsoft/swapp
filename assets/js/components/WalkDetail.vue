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
            collapse-key="waypoints-of-round"
            is-visible-by-default
        >
            <WalkDetailData
                :walk-id="walkId"
            />
        </content-collapse>
        <content-collapse
            title="Wegpunkte dieser Runde"
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
                return this.$store.getters["studiengang/isLoading"] || this.$store.getters["walk/isLoading"];
            },
            hasError() {
                return this.$store.getters["studiengang/hasError"] || this.$store.getters["walk/hasError"];
            },
            error() {
                return this.$store.getters["studiengang/error"] || this.$store.getters["walk/error"];
            },
            hasWalks() {
                return this.$store.getters["walk/hasWalks"];
            },
            walks() {
                return this.$store.getters["walk/walks"];
            },
            title() {
                return `Streetwork-Runde: ${this.walk.name} <small>vom ${(new Date(this.walk.startTime)).toLocaleDateString('de-DE', { weekday: 'short', year: 'numeric', month: '2-digit', day: '2-digit' })}</small>`;
            },
            walk() {
                return this.$store.getters["walk/getWalkById"](this.walkId);
            },
        },
        watch: {},
        mounted() {
            if (this.walk) {
                return;
            }
            this.$store.dispatch('walk/findById', this.walkId);
        },
        methods: {
        },
    }
</script>

<style scoped>

</style>
