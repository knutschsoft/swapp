<template>
    <div>
        <b-alert
            :show="!!redirect"
            class="position-fixed fixed-top m-0 rounded-0"
            style="z-index: 2000;"
            variant="warning"
            fade
            dismissible
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
            title="Abgeschlossene Streetwork-Runden"
            collapse-key="finished-walk-list"
            is-visible-by-default
        >
            <WalkList />
        </content-collapse>
        <content-collapse
            title="Streetwork-Runden exportieren"
            collapse-key="export-walks"
            is-visible-by-default
        >
            <WalkExport />
        </content-collapse>
    </div>
</template>

<script>
    "use strict";
    import StartWalk from './Dashboard/StartWalk';
    import WalkList from './Dashboard/WalkList';
    import ContentCollapse from './ContentCollapse.vue';
    import WalkExport from './Walk/WalkExport.vue';

    export default {
        name: "Dashboard",
        components: {
            ContentCollapse,
            StartWalk,
            WalkList,
            WalkExport,
        },
        props: {
            redirect: {
                type: String,
                required: false,
            },
        },
        data: function () {
            return {
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
                return this.$store.getters["team/hasTeams"];
            },
            teams() {
                return this.$store.getters["team/teams"];
            },
        },
        async created() {
            // await this.$store.dispatch("team/findAll");
            // await this.$store.dispatch("walk/findAll");
        },
        mounted() {
        },
        methods: {
        }
    }
</script>

<style scoped type="scss">
</style>
