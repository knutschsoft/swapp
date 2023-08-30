<template>
    <div>
        <b-alert
            :show="!!redirect"
            class="position-fixed fixed-top m-0 rounded-0"
            style="z-index: 2000;"
            variant="warning"
            fade
            dismissible
            data-test="redirect-alert"
        >
            {{ redirect }}
        </b-alert>
        <content-collapse
            title="Neue Streetwork-Runde"
            collapse-key="walk-start"
            is-visible-by-default
        >
            <StartWalk />
        </content-collapse>
        <content-collapse
            :title="`Abgeschlossene Streetwork-Runden ${ totalWalks !== null ? `(${ totalWalks })` : '' }`"
            collapse-key="finished-walk-list"
            is-visible-by-default
        >
            <WalkList
                @refresh-total-walks="updateTotalWalks"
            />
        </content-collapse>
        <content-collapse
            :title="`Liste aller Wegpunkte ${ totalWayPoints !== null ? `(${ totalWayPoints })` : '' }`"
            collapse-key="all-way-point-list"
            is-visible-by-default
        >
            <WayPointList
                @refresh-total-way-points="updateTotalWayPoints"
            />
        </content-collapse>
    </div>
</template>

<script>
    "use strict";
    import StartWalk from './Dashboard/StartWalk';
    import WalkList from './Dashboard/WalkList';
    import WayPointList from './Dashboard/WayPointList';
    import ContentCollapse from './ContentCollapse.vue';
    import { useClientStore } from '../stores/client';
    import { useTeamStore } from '../stores/team';

    export default {
        name: "Dashboard",
        components: {
            ContentCollapse,
            StartWalk,
            WalkList,
            WayPointList,
        },
        props: {
            redirect: {
                type: String,
                required: false,
            },
        },
        data: function () {
            return {
                clientStore: useClientStore(),
                teamStore: useTeamStore(),
                totalWayPoints: null,
                totalWalks: null,
            }
        },
        computed: {
            hasWalks() {
                return this.$store.getters["walk/hasWalks"];
            },
            walks() {
                return this.$store.getters["walk/walks"];
            },
            hasTeams() {
                return this.teamStore.hasTeams;
            },
            teams() {
                return this.teamStore.getTeams;
            },
        },
        created() {
        },
        async mounted() {
            await this.clientStore.fetchClients();
        },
        methods: {
            updateTotalWayPoints(totalWayPoints) {
                this.totalWayPoints = totalWayPoints;
            },
            updateTotalWalks(totalWalks) {
                this.totalWalks = totalWalks;
            },
        },
    }
</script>

<style scoped type="scss">
</style>
