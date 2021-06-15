<template>
    <div>
        <content-collapse
            title="Liste der Benutzer"
            collapse-key="user-list"
            is-visible-by-default
        >
            <user-list />
        </content-collapse>
        <content-collapse
            title="Neuen Benutzer erstellen"
            collapse-key="user-create"
            is-visible-by-default
        >
            <user-create />
        </content-collapse>
    </div>
</template>

<script>
    "use strict";
    import UserCreate from './Users/UserCreate';
    import UserList from './Users/UserList';
    import ContentCollapse from './ContentCollapse.vue';

    export default {
        name: "Users",
        components: {
            ContentCollapse,
            UserCreate,
            UserList,
        },
        async mounted() {
            await this.$store.dispatch('client/findAll');
        },
    }
</script>

<style scoped>

</style>
