<template>
    <div>
        <v-alert
            v-if="!!redirect"
            class="position-fixed fixed-top m-0 rounded-0"
            style="z-index: 2000;"
            type="warning"
            transition="fade-transition"
            dismissible
            data-test="redirect-alert"
        >
            {{ redirect }}
        </v-alert>
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
    import StartWalk from './Dashboard/StartWalk.vue';
    import WalkList from './Dashboard/WalkList.vue';
    import WayPointList from './Dashboard/WayPointList.vue';
    import ContentCollapse from './ContentCollapse.vue';
    import { useClientStore, useTeamStore, useWalkStore } from '../stores';

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
                walkStore: useWalkStore(),
                totalWayPoints: null,
                totalWalks: null,
            }
        },
        computed: {
            hasWalks() {
                return this.walkStore.hasWalks;
            },
            walks() {
                return this.walkStore.getWalks;
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
