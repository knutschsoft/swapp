<template>
    <div>
        <content-collapse
            title="Liste der Teams"
            collapse-key="waypoints-of-round"
            is-visible-by-default
        >
            <team-list />
        </content-collapse>
    </div>
</template>

<script>
    "use strict";
    import TeamList from './Teams/TeamList';
    import ContentCollapse from './ContentCollapse.vue';

    export default {
        name: "Teams",
        components: {
            ContentCollapse,
            TeamList,
        },
        mounted() {
        },
    }
</script>

<style scoped>

</style>
