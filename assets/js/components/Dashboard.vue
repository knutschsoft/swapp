<template>
    <div>
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
    </div>
</template>

<script>
    "use strict";
    import StartWalk from './Dashboard/StartWalk';
    import WalkList from './Dashboard/WalkList';
    import ContentCollapse from './ContentCollapse.vue';

    export default {
        name: "Dashboard",
        components: {
            ContentCollapse,
            StartWalk,
            WalkList,
        },
        props: {},
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
