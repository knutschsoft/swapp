<template>
    <div>
        <content-collapse
            title="Liste der Benutzer"
            collapse-key="user-list"
            is-visible-by-default
        >
            <user-list />
        </content-collapse>
    </div>
</template>

<script>
    "use strict";
    import UserList from './Users/UserList';
    import ContentCollapse from './ContentCollapse.vue';

    export default {
        name: "Users",
        components: {
            ContentCollapse,
            UserList
        },
        async mounted() {
            await this.$store.dispatch('client/findAll');
        },
    }
</script>

<style scoped>

</style>
