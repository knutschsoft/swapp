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
            v-if="isSuperAdmin"
            title="Liste der aktiven Benutzer"
            collapse-key="active-users"
            is-visible-by-default
        >
            <active-user-list />
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
    import ActiveUserList from './Users/ActiveUserList';
    import UserCreate from './Users/UserCreate';
    import UserList from './Users/UserList';
    import ContentCollapse from './ContentCollapse.vue';

    export default {
        name: "Users",
        components: {
            ActiveUserList,
            ContentCollapse,
            UserCreate,
            UserList,
        },
        computed: {
            isSuperAdmin() {
                return this.$store.getters['security/isSuperAdmin'];
            },
        },
        async mounted() {
            await this.$store.dispatch('client/findAll');
        },
    }
</script>

<style scoped>

</style>
